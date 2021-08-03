<?php

namespace App\Controller;

use App\Entity\NotificationType;
use App\Manager\FileManager;
use App\Manager\NotificationManager;
use App\Message\ExportMessage;
use App\Service\Counter;
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
    private MessageBusInterface $messageBus;
    private FileManager $fileManager;
    private NotificationManager $notificationManager;
    private Counter $counter;

    public function __construct(
        MessageBusInterface $messageBus,
        FileManager $fileManager,
        NotificationManager $notificationManager,
        Counter $counter
    ) {
        $this->messageBus = $messageBus;
        $this->fileManager = $fileManager;
        $this->notificationManager = $notificationManager;
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
            ->setUsername($username)
            ->setStartDate($startDate)
            ->setFilename($filename)
            ->setTemplate(NotificationType::TEMPLATE_EXPORT_START)
            ->setInterval($args['interval']);

        $file = $this->fileManager->createFrom($exportMessage);
        $notification = $this->notificationManager->createFrom($exportMessage);
        $exportMessage->setData([
            'notification_id' => $notification->getId(),
            'notification_content' => $notification->getContent(),
            'file_id' => $file->getId()
        ]);
        $this->messageBus->dispatch($exportMessage);
        return $this->json($this->counter->increase($username), Response::HTTP_OK);
    }
}
