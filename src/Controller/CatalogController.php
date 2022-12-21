<?php

namespace App\Controller;

use App\Services\CatalogService;
use App\Services\Elasticsearch\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/complex", name="app_complex")
 */
class CatalogController extends AbstractController
{
    public function __construct(
        private Helper $helper,
        private CatalogService $catalogService
    ){}

    /**
     * @Route("/", name="_list")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page',    0);

        $dataElasticQuery = $this->helper->findPaginationBuildingsList($request);
        $items = $this->catalogService->getListBuildings($dataElasticQuery->getResults());

        return $this->render('catalog/index.html.twig', [
            'items' => $items,
            'before_page' => $page <= 0 ? 0 : $page - 1,
            'next_page' => $page + 1,
            'total' => $dataElasticQuery->getTotalHits()
        ]);
    }

    /**
     * @Route("/map", name="_map_list")
     * @return Response
     */
    public function mapList(): Response
    {

        $dataElasticQuery = $this->helper->findMapBuildingsList();
        $items = $this->catalogService->getListBuildingsMap($dataElasticQuery->getResults());

        return $this->render('building/map.html.twig', [
            'items' => $items
        ]);
    }

    /**
     * @Route("/{code}", name="_building")
     */
    public function building(Request $request): Response
    {
        if ($request->get('id')) {
            $itemElasticQuery = $this->helper->findBuilding($request->get('id'));
            $item = $this->catalogService->getBuilding($itemElasticQuery->getResults()[0]);

            return $this->render('building/index.html.twig', [
                'item' => $item,
            ]);
        } else {
            dd('Error');
        }
    }
}