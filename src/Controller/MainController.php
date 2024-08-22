<?php

namespace App\Controller;

use App\Services\Admin\NewsService;
use App\Services\CatalogService;
use App\Services\Info\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    public function __construct(
        private Article $article,
        private CatalogService $catalogService,
        private NewsService $newsService
    ){}
    /**
     * @Route("", name="app_main")
     */
    public function index(): Response
    {
        $articles = $this->article->getList();
        $districts = $this->catalogService->getDistrictLocationBuilding();
        $news = $this->newsService->getNewsSlider();

        return $this->render('main/index.html.twig', [
            'articles' => $articles,
            'districts' => $districts,
            'news' => $news
        ]);
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function indexPrivacy(): Response
    {
        return $this->render('privacy/index.html.twig');
    }
}