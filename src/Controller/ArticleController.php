<?php

namespace App\Controller;

use App\Services\Info\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    public function __construct(
        public Article $article
    ){}

    /**
     * @Route("/article/{code}", name="app_article_one")
     * @param string $code
     * @return Response
     */
    public function one(string $code): Response
    {
        $item = $this->article->get($code);

        return $this->render('article/one.html.twig', [
            'article' => $item
        ]);
    }

    /**
     * @Route("/articles", name="app_article_list")
     * @return Response
     */
    public function list(): Response
    {
        $articles = $this->article->getList();

        return $this->render('article/list.html.twig', [
            'articles' => $articles
        ]);
    }


}