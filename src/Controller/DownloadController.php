<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class DownloadController extends AbstractController
{
    /**
     * @Route("/download/{filename}", name="download")
     *
     * @param string $filename
     * @return BinaryFileResponse
     */
    public function download(string $filename): BinaryFileResponse
    {
        $csvDir = $this->getParameter('kernel.project_dir') . '/public/csv/';

        $response = new  BinaryFileResponse($csvDir.$filename);
        $response->headers->set('Content-Type', 'text/csv');

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
