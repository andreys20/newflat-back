<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Services\Admin\NewsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/panel/news", name="app_admin_panel_news")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("", name="_list", methods={"GET","POST"})
     */
    public function index(Request $request, NewsService $newsAdminService)
    {
        if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
            $response = $newsAdminService->getAll($request);
            return $this->json($response);
        }

        return $this->render('admin/news/table.html.twig', [
            'columns' => $newsAdminService->getColumnsTable()
        ]);
    }

    /**
     * @Route("/new", name="_new", methods={"GET", "POST"})
     */
    public function new(
        Request $request,
        NewsService $newsAdminService
    ): Response
    {
        $newsModel = $request->get('news');

        if ($request->isXmlHttpRequest() && $request->request->has('news')) {
            $newsAdminService->createOrUpdateNews($request);

            return $this->redirectToRoute('app_admin_panel_news_list', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? 'admin/news/_new.html.twig' : 'admin/news/new.html.twig';

        return $this->render($template, [
            'news' => $newsModel
        ]);
    }

    /**
     * @Route("/{id}/edit", name="_edit", methods={"GET", "POST"})
     */
    public function edit
    (
        Request $request,
        News $news,
        NewsService $newsAdminService
    ): Response
    {
        $newsModel = $newsAdminService->newsToModel($news);

        if ($request->isXmlHttpRequest() && $request->request->has('news')) {
            $newsAdminService->createOrUpdateNews($request, $news);

            return $this->redirectToRoute('app_admin_panel_news_list', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? 'admin/news/_edit.html.twig' : 'admin/news/edit.html.twig';
        return $this->renderForm($template, [
            'news' => $newsModel
        ]);
    }

    /**
     * @Route("/{id}/delete", name="_delete", methods={"POST"})
     */
    public function delete(Request $request, News $news, EntityManagerInterface $em, ParameterBagInterface $params): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            unlink($params->get('news_images_directory') . $news->getPhoto());
            $em->remove($news);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_panel_news_list', [], Response::HTTP_SEE_OTHER);
    }
}