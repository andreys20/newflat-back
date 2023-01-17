<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function save(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(News $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getList(Request $request): QueryBuilder
    {
        $query = $this->createQueryBuilder('News');

        if (!empty($search = $request->get('search'))) {
            $query
                ->andWhere('LOWER(News.title) LIKE LOWER(:search)')
                ->setParameter('search', "%$search%");
        }

        $query->orderBy(new OrderBy("News.sort", 'desc'));

        return $query;
    }

    public function getAll(array $columns = [], int $start = 0, int $length = 0, array $order = [], string $search = ''): Paginator
    {
        $query = $this->addGetQueryBuilder();

        $this->addStart($query, $start);
        $this->addLength($query, $length);

        $this->addSearch($query, $search);

        $this->addSort($query, $order, $columns);


        return $this->getResultPagination($query);
    }

    private function addGetQueryBuilder(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder();
    }

    private function getOrCreateQueryBuilder(QueryBuilder $query = null): QueryBuilder
    {
        return $query ?: $this->createQueryBuilder('News')->select('News');
    }

    private function addStart(QueryBuilder $query, int $start): void
    {
        $query->setFirstResult($start);
    }

    private function addLength(QueryBuilder $query, int $length): void
    {
        if ($length > 0) {
            $query->setMaxResults($length);
        }
    }

    private function addSearch(QueryBuilder $query, string $search): void
    {
        if (!empty($search)) {
            $query
                ->andWhere('News.title LIKE :search')
                ->setParameter('search', "%$search%");
        }
    }

    private function addSort(QueryBuilder $query, array $order, array $columns): void
    {
        if (!empty($order)) {
            $column = $columns[$order[0]['column']];
            $dir = $order[0]['dir'] === 'asc' ? 'ASC' : 'DESC';

            $query->orderBy("News.$column", $dir);
        } else {
            $query->orderBy('News.id', 'DESC');
        }
    }

    private function getResultPagination(QueryBuilder $query): Paginator
    {
        return new Paginator($query);
    }

    public function getAllCountFilter(string $search = ''): int
    {
        $query = $this->addGetQueryBuilder();
        $this->addSearch($query, $search);

        return $this->getCountResult($query);
    }

    private function getCountResult(QueryBuilder $query)
    {
        try {
            return $query
                ->select('count(distinct News) as count')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getAllCount(): int
    {
        $query = $this->addGetQueryBuilder();

        return $this->getCountResult($query);
    }
}
