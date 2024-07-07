<?php

namespace App\Services\Admin;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Services\Constant\DateConstant;
use App\Services\FileService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class NewsService
{
    public function __construct
    (
        private NewsRepository $newsRepository,
        private EntityManagerInterface $em,
        private Environment $twig,
        public FileService $fileService,
        private ParameterBagInterface $params,
        private DateConstant $dateConstant
    )
    {}

    protected array $columns = [
        'id'        => [
            'addClass' => 'text-start',
            'label'    => 'id',
            'visible'  => false
        ],
        'title'      => [
            'addClass' => 'text-start',
            'label'    => 'Заголовок'
        ],
        'description' => [
            'addClass' => 'text-center',
            'label'    => 'Содержание'
        ],
        'code'     => [
            'addClass' => 'text-start',
            'label'    => 'Символьный код',
            'sortable' => false
        ],
        'createdAt'     => [
            'addClass' => 'text-start',
            'label'    => 'Дата публикации'
        ],
        'photo'       => [
            'addClass' => 'text-start',
            'label'    => 'Фото',
            'sortable' => false
        ],
        'sort'    => [
            'addClass' => 'text-start',
            'label'    => 'Сортировка'
        ],
        'actions'   => [
            'addClass' => 'text-right',
            'label'    => '',
            'sortable' => false,
        ],
    ];

    public function getColumnsTable(): array
    {
        $columnsTmp = [];
        foreach ($this->columns as $key => $column) {
                $columnsTmp[$key] = $column;
        }

        return $columnsTmp;
    }

    public function getAll(Request $request): array
    {
        $start = $request->request->getInt('start');
        $length = $request->request->getInt('length', 1);
        $draw = $request->request->getInt('draw', 1);
        $order = (array)$request->get('order', []);
//        $filters = (array)$request->get('filters', []);
        $search = $request->get('search', '');

        $response = [
            'draw' => $draw
        ];

        $accessColumns = array_keys($this->getColumnsTable());
        $news = $this->newsRepository->getAll($accessColumns, $start, $length, $order, $search['value'] ?? '');

        $response['recordsFiltered'] = $this->newsRepository->getAllCountFilter($search['value'] ?? '');
        $response['recordsTotal'] = $this->newsRepository->getAllCount();

        $arr = [];
        /** @var News $element */
        foreach ($news as $element) {

            $tmp['id'] = $element->getId();
            $tmp['title'] = $element->getTitle();
            $tmp['code'] = $element->getCode();
            $tmp['description'] = mb_strimwidth($element->getDescription(), 0, 120, "...");
            $tmp['createdAt'] = $element->getCreatedAt()->format('d-m-Y');
            $tmp['photo'] = $element->getPhoto();
            $tmp['sort'] = $element->getSort();
            $tmp['actions'] = $this->getButtons($element);

            $arr[] = $tmp;
        }

        $response['data'] = $arr;

        return $response;
    }

    public function createOrUpdateNews(Request $request, News $news = null): News
    {
        $newsModel = $request->request->all()['news'];

        if (!$news) {
            $news = new News();
        }

        if ($request->files->get('news')['photo']) {
            $file = $this->fileService->saveFile($request->files->get('news')['photo'], $this->params->get('news_images_directory'));
            $news->setPhoto($file->getFilename());
        }

        $news->setTitle($newsModel['title']);
        $news->setDescription($newsModel['description']);
        $news->setCode($newsModel['code']);
        $news->setSort(!empty($newsModel['sort']) ? $newsModel['sort'] : null);
        $news->setCreatedAt(new DateTimeImmutable());

        $this->em->persist($news);
        $this->em->flush();

        return $news;
    }

    public function newsToModel(News $news): array
    {
        return [
            'id' => $news->getId(),
            'title' => $news->getTitle(),
            'description' => $news->getDescription(),
            'code' => $news->getCode(),
            'sort' => $news->getSort(),
            'photo' => $news->getPhoto() ? $this->params->get('news_images_directory') . $news->getPhoto() : ''
        ];
    }

    private function getButtons(News $news): string
    {
        try {
            return $this->twig->render('admin/news/_table_buttons.html.twig', ['news' => $news]);
        } catch (Exception $e) {
            return '';
        }
    }

    public function getNewsSlider():array
    {
        $news = $this->newsRepository->getListSlider();
        usort($news, static fn($a, $b) => ($a['sort'] < $b['sort']));

        foreach ($news as $key => $item) {
            $news[$key]['date'] = $this->dateConstant->getDateWithYearOrNot($item['createdAt']);
            $news[$key]['photo'] = $item['photo'] ? $this->params->get('news_images_url') . $item['photo'] : '';
        }

        return $news;
    }
}