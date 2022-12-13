<?php

namespace App\Controller;

use App\Services\Info\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    public function __construct(
        public Article $article
    ){}

    /**
     * @Route("/developers", name="app_developer_list")
     * @return Response
     */
    public function list(): Response
    {

        return $this->render('article/one.html.twig');
    }
}