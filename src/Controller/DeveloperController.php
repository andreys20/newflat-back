<?php

namespace App\Controller;

use App\Services\CatalogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    public function __construct(
        public CatalogService $catalogService
    ){}

    /**
     * @Route("/developers", name="app_developer_list")
     * @return Response
     */
    public function list(): Response
    {
        $developers = $this->catalogService->getDevelopersBuilding();
        ksort($developers);

        return $this->render('developer/list.html.twig', [
            'developers' => $developers
        ]);
    }
}