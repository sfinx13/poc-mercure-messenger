<?php

namespace App\Controller;

use App\Manager\FileManager;
use App\Message\ExportMessage;
use App\Service\Counter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("app")
 */
class ExportController extends AbstractController
{
    protected MessageBusInterface $messageBus;
    protected EntityManagerInterface $entityManager;
    protected FileManager $fileManager;
    protected Counter $counter;

    public function __construct(
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        FileManager $fileManager,
        Counter $counter
    ) {
        $this->messageBus = $messageBus;
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
        $this->counter = $counter;
    }

    /**
     * @Route("/export", name="export_file")
     */
    public function export(
        Request $request
    ): JsonResponse {
        $username = $this->getUser()->getUserIdentifier();
        $this->counter->current($username);
        $args = $request->query->all();
        $startDate = new \DateTime($args['start-date']);
        $filename = 'export_' . $username. '_' . $startDate->getTimestamp() . '.csv';

        $exportMessage = (new ExportMessage())
            ->setUser($this->getUser())
            ->setStartDate($startDate)
            ->setFilename($filename)
            ->setInterval($args['interval']);

        $file = $this->fileManager->createNew($exportMessage);
        $this->fileManager->save($file);

        $this->messageBus->dispatch($exportMessage);
        return $this->json($this->counter->increase($username), Response::HTTP_OK);
    }
}
