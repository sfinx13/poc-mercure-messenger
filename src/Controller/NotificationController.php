<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("app")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("/notifications", name="get_notifications", methods={"GET"})
     */
    public function getNotifications(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findBy(['trashedAt' => null, 'active' => 1], ['createdAt' => 'DESC']);

        $topics = [
            $this->getParameter('topic_url') . 'notifications'
        ];

        return $this->render('notification/notification.html.twig', [
            'notifications' => $notifications,
            'topics' => $topics
        ]);
    }
}
