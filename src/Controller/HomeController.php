<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     *
     * @Route("/", name="home_page")
     *
     * @param ParameterBagInterface $parameterBag
     * @return Response
     * @throws \Exception
     */
    public function home(ParameterBagInterface $parameterBag): Response
    {
        $csvDir = $this->getParameter('kernel.project_dir') . '/public/csv/';
        $finder = new Finder();

        $filesystem = new Filesystem();
        if ($filesystem->exists($csvDir)) {
            $finder->files()->in($csvDir);
            $files = [];

            if ($finder->hasResults()) {
                $finder->sortByAccessedTime()->reverseSorting();
                foreach ($finder as $file) {
                    $files[] = [
                        'filename' => $file->getFilename(),
                        'size' => round($file->getSize() / 1024, 0) . ' Ko',
                        'datetime' => (new \DateTime())->setTimestamp($file->getATime())->format('d-M-Y H:i:s')
                    ];
                }
            }

        }
        return $this->render('home/index.html.twig', [
            'app_url' => $parameterBag->get('app_url'),
            'mercure_publish_url' => $parameterBag->get('mercure_publish_url'),
            'files' => $files ?? []
        ]);
    }

}
