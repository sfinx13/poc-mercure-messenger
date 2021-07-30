<?php

namespace App\Controller;

use App\Message\DeleteMessage;
use App\Repository\FileRepository;
use App\Security\CookieGenerator;
use Lcobucci\JWT\Encoder;
use Lcobucci\JWT\ClaimsFormatter;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\MicrosecondBasedDateConversion;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("app")
 */
class FilesController extends AbstractController
{
    /**
     * @Route("/files", name="get_files", methods={"GET"})
     */
    public function getFiles(FileRepository $fileRepository, CookieGenerator $cookieGenerator): Response
    {
        $files = $fileRepository->findBy(
            ['user' => $this->getUser()],
            ['exportedAt' => 'desc']
        );

        $topics = [
            $this->getParameter('topic_url') . '/files/' . $this->getUser()->getUserIdentifier(),
            $this->getParameter('topic_url') . '/message'
        ];

        $response = $this->render('home/index.html.twig', [
            'files' => $files ?? [],
            'topics' => $topics
        ]);

        $response->headers->setCookie($cookieGenerator->setTopics($topics)->generate());

        return $response;
    }

    /**
     * @Route("/files", name="delete_files", methods={"DELETE"})
     */
    public function remove(
        MessageBusInterface $messageBus,
        Request $request
    ): JsonResponse {
        $deleteMessage = (new DeleteMessage())->setUser($this->getUser());

        $messageBus->dispatch($deleteMessage);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
