<?php

namespace App\Controller;

use App\Services\CatalogService;
use App\Services\DeveloperService;
use App\Services\Elasticsearch\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    public function __construct(
        public DeveloperService $developerService,
        public CatalogService $catalogService,
        public Helper $helper
    ){}

    /**
     * @Route("/developers", name="app_developer_list")
     * @return Response
     */
    public function list(): Response
    {
        $developers = $this->developerService->getDevelopersBuilding();
        ksort($developers);

        return $this->render('developer/list.html.twig', [
            'developers' => $developers
        ]);
    }

    /**
     * @Route("developer/{code}", name="app_developer")
     * @param Request $request
     * @return Response
     */
    public function developer(Request $request): Response
    {
        if ($request->get('id')) {
            $develop = $this->developerService->getDeveloper($request);
            $buildings = $this->catalogService->getBuildingToDeveloper($request->get('id'));

            return $this->render('developer/detail.html.twig', [
                'develop' => $develop,
                'buildings' => $buildings
            ]);
        }

        return $this->render('developer/detail.html.twig', [
            'develop' => [],
            'buildings' => []
        ]);
    }
}