<?php

namespace App\Controller;

use App\Services\CatalogService;
use App\Services\Info\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    public function __construct(
        private Article $article,
        private CatalogService $catalogService
    ){}
    /**
     * @Route("", name="app_main")
     */
    public function index(): Response
    {
        $articles = $this->article->getList();
        $districts = $this->catalogService->getDistrictLocationBuilding();

        return $this->render('main/index.html.twig', [
            'articles' => $articles,
            'districts' => $districts
        ]);
    }
}