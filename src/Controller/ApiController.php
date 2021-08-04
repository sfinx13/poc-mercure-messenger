<?php

namespace App\Controller;

use App\Entity\Notification as EntityNotification;
use App\Manager\NotificationManager;
use App\Notification\Notification;
use App\Notification\Notifier;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api")
 */
class ApiController extends AbstractController
{
    private NotificationRepository $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository, NotificationManager $notificationManager)
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @Route("/notification/{id}/action", name="read_notification", methods={"PATCH"})
     */
    public function read(
        NotificationManager $notificationManager,
        Notifier $notifier,
        Request $request,
        int $id
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /** @var EntityNotification $entityNotification **/
        $entityNotification = $this->notificationRepository->find($id);
        $entityNotification = $this->process($entityNotification, $data);
        $notificationManager->save($entityNotification);

        $data = $this->prepareData($entityNotification);

        $notifier->send(new Notification(
            ['notifications'],
            $data,
            false,
            'update-notification'
        ));
        return $this->json($data, Response::HTTP_OK);
    }

    private function process(EntityNotification $notification, $data): EntityNotification
    {
        if (!isset($data['action'])) {
            return $notification;
        }

        switch ($data['action']) {
            case 'is_read':
                $notification->setReadedAt(new \DateTimeImmutable());
                break;
            case 'is_processed':
                $notification->setReadedAt(new \DateTimeImmutable());
                $notification->setProcessedAt(new \DateTimeImmutable());
                break;
            case 'is_trashed':
                $notification->setReadedAt(new \DateTimeImmutable());
                $notification->setProcessedAt(new \DateTimeImmutable());
                $notification->setTrashedAt(new \DateTimeImmutable());
                break;
        }

        return $notification;
    }

    private function prepareData(EntityNotification $notification): array
    {
        $countNotifications = $this->notificationRepository->countNotificationNotProcessed();
        return [
            'notification_id' => $notification->getId(),
            'is_read' => $notification->getReadedAt() !== null,
            'is_processed' => $notification->getProcessedAt() !== null,
            'is_trashed' => $notification->getTrashedAt() !== null,
            'count_notifications' => $countNotifications
        ];
    }
}
