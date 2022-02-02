<?php

namespace App\Controller;

use App\Entity\NotificationType;
use App\Messenger\Message\DeleteMessage;
use App\Repository\FileRepository;
use App\Security\CookieGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('app')]
class FilesController extends AbstractController
{
    #[Route('/files', name: 'get_files', methods: 'GET')]
    public function getFiles(FileRepository $fileRepository, CookieGenerator $cookieGenerator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $files = $fileRepository->findBy(
            ['user' => $this->getUser()],
            ['exportedAt' => 'desc']
        );

        $topics = [
            $this->getParameter('topic_url') . 'user/' . $this->getUser()->getUserIdentifier() . '/files',
            $this->getParameter('topic_url') . 'message'
        ];

        $response = $this->render('home/index.html.twig', [
            'files' => $files,
            'topics' => $topics
        ]);

        $response->headers->setCookie($cookieGenerator->setTopics($topics)->generate());

        return $response;
    }

    #[Route('/files', name: 'delete_files', methods: 'DELETE')]
    public function removeAll(MessageBusInterface $messageBus): JsonResponse
    {
        $deleteMessage = (new DeleteMessage())
            ->setUsername($this->getUser())
            ->setTemplate(NotificationType::TEMPLATE_DELETE_FILES);

        $messageBus->dispatch($deleteMessage);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/files/{filename}', name: 'delete_file', methods: 'DELETE')]
    public function remove(
        MessageBusInterface $messageBus,
        string $filename
    ): JsonResponse {
        $deleteMessage = (new DeleteMessage())->setFilename($filename)->setUsername($this->getUser());

        $messageBus->dispatch($deleteMessage);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
