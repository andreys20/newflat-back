<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Services\Constant\DateConstant;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/news", name="app_news_")
 */
class NewsController extends AbstractController
{
    public function __construct(
        public DateConstant $dateConstant,
        public NewsRepository $newsRepository,
        private ParameterBagInterface $params
    ){}

    /**
     * @Route("/{code}", name="one")
     * @param string $code
     * @return Response
     */
    public function one(string $code): Response
    {
        $news = $this->newsRepository->findOneBy([
            'code' => $code
        ]);

        if ($news) {
            $item = [
                'title' => $news->getTitle(),
                'description' => $news->getDescription(),
                'photo' => $this->params->get('news_images_url') . $news->getPhoto(),
                'date' => $this->dateConstant->getDateWithYearOrNot($news->getCreatedAt())
            ];
        }

        return $this->render('news/one.html.twig', [
            'news' => $item ?? null
        ]);
    }

    /**
     * @Route("/", name="list")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param NewsRepository $newsRepository
     * @return Response
     */
    public function list(Request $request, PaginatorInterface $paginator, NewsRepository $newsRepository): Response
    {
        $query = $newsRepository->getList($request);

        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', 10);

        $pagination = $paginator->paginate(
            $query,
            $page > 0 ? $page : 1,
            $perPage > 0 ? $perPage : 10,
            ['wrap-queries' => true]
        );

        $result = [];
        /** @var News $news */
        foreach ($pagination->getItems() as $news) {
            $result[] = [
                'title' => $news->getTitle(),
                'date' => $this->dateConstant->getDateWithYearOrNot($news->getCreatedAt()),
                'photo' => $this->params->get('news_images_url') . $news->getPhoto(),
                'detail' => '/news/' . $news->getCode()
            ];
        }

        return $this->render('news/list.html.twig', [
            'items' => $result ?? [],
            'before_page' => $pagination->getCurrentPageNumber() > 1 ? $pagination->getCurrentPageNumber() - 1 : 1,
            'next_page' => $pagination->getCurrentPageNumber() + 1,
        ]);
    }
}