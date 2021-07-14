<?php

namespace App\Controller;

use App\Message\DeleteMessage;
use App\Message\ExportMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * ExportController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param AdapterInterface $cache
     */
    public function __construct(EntityManagerInterface $entityManager,
                                AdapterInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
    }

    /**
     * @Route("/export", name="export_file")
     *
     * @param MessageBusInterface $messageBus
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function export(MessageBusInterface $messageBus,
                           Request $request): JsonResponse
    {
        $this->countMessage();
        $args = $request->query->all();
        $exportMessage = (new ExportMessage())
            ->setProjectId($args['project-id'])
            ->setStartDate(new \DateTime($args['start-date']))
            ->setInterval($args['interval']);

        $messageBus->dispatch($exportMessage);

        /** @var CacheItemInterface $counter */
        $counter = $this->cache->getItem('counter');

        if (!$counter->isHit()) {
            $counter->set(1);
        } else {
            $counter->set((int)$counter->get() + 1);
        }

        $this->cache->save($counter);

        return $this->json($counter->get(), Response::HTTP_OK);
    }

    /**
     * @Route("/files", name="remove_files", methods={"DELETE"})
     *
     * @param MessageBusInterface $messageBus
     * @param Request $request
     * @return JsonResponse
     */
    public function remove(MessageBusInterface $messageBus,
                           Request $request): JsonResponse
    {
        $args = $request->query->all();
        $deleteMessage = (new DeleteMessage())->setExtension($args['extension']);

        $messageBus->dispatch($deleteMessage);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function countMessage(): void
    {
        $connection = $this->entityManager->getConnection();
        $request = "SELECT count(*) as nb_message FROM messenger_messages where delivered_at is null;";
        $statement = $connection->prepare($request);
        $result = $statement->executeQuery();
        $data = $result->fetchAssociative();
        $nbMessage = (int)$data['nb_message'];

        if (0 === $nbMessage) {
            $this->cache->clear();
        }

        $counter = $this->cache->getItem('counter');
        $counter->set($nbMessage);
        $this->cache->save($counter);
    }
}
