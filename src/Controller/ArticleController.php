<?php

namespace App\Controller;

use App\Services\Info\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article", name="app_article")
 */
class ArticleController extends AbstractController
{
    public function __construct(
        public Article $article
    ){}

    /**
     * @Route("/{code}", name="_one")
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
}