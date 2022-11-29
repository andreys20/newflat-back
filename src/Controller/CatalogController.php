<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogController extends AbstractController
{
    /**
     * @Route("/catalog", name="app_catalog")
     */
    public function index(): Response
    {
        return $this->render('catalog/index.html.twig');
    }

    /**
     * @Route("/catalog/building", name="app_catalog_building")
     */
    public function building(): Response
    {
        return $this->render('building/index.html.twig');
    }
}