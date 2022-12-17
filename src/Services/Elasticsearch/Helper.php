<?php

namespace App\Services\Elasticsearch;

use Elastica\ResultSet;
use Exception;
use Psr\Log\LoggerInterface;
use Elastica\Client;
use Symfony\Component\HttpFoundation\Request;

class Helper
{
    private array $paramsQuery = [];

    public function __construct
    (
        private LoggerInterface $exceptionLogger,
        private Client $client,
    ){}

    public function checkConnection(): void
    {
        try {
            $this->client->refreshAll()->getStatus();
        } catch (Exception $e) {
            throw new Exception('Not Connect to Elastic ' . $e->getMessage() . ' ' . $e->getTraceAsString());
        }
    }

    /**
     * Поиск в Elasticsearch логов позиции, с фильтрацией по Car и Mission
     * @param Request $request
     * @return ResultSet|array
     */
    public function findPaginationBuildingsList(Request $request): ResultSet|array
    {
        $page = $request->query->getInt('page', 0);
        $perPage = $request->query->getInt('per_page', 10);

        $sort = $request->query->get('sort');
        $order = $request->query->get('order');

        $this->paramsQuery = [
            "from" => $page * 10,
            "size" => $perPage
        ];

        $this->addQuerySort($sort, $order);
        $this->addQueryFilter($request);

        if ($this->client->getIndex(Config::KRISHA_KZ_INDEX)->exists()) {
            return $this->client->getIndex(Config::KRISHA_KZ_INDEX)->search($this->paramsQuery);
        }

        return [];
    }

    public function findBuilding(int $buildingId): ResultSet|array
    {
        $this->addQueryMaxSize(1);
        $this->addQuerySearchId($buildingId);

        if ($this->client->getIndex(Config::KRISHA_KZ_INDEX)->exists()) {
            return $this->client->getIndex(Config::KRISHA_KZ_INDEX)->search($this->paramsQuery);
        }

        return [];
    }

    public function findCountBuildingInDistrict(string $nameDistrict): int
    {
        $this->addQuerySearchField('location', $nameDistrict);

        if ($this->client->getIndex(Config::KRISHA_KZ_INDEX)->exists()) {
            return $this->client->getIndex(Config::KRISHA_KZ_INDEX)->search($this->paramsQuery)->getTotalHits();
        }

        return 0;
    }

    private function addQuerySearchId(int $buildingId): void
    {
        if ($buildingId) {
            $this->paramsQuery['query']['bool']['filter']['term'] = [
                 'ID.keyword' => $buildingId
            ];
        }
    }

    private function addQuerySearchField(string $field, mixed $value): void
    {
        if ($field && $value) {
            $this->paramsQuery['query']['bool']['must']['match'][$field] = $value;
        }
    }

    private function addQueryMaxSize(?int $size = 10): void
    {
        $this->paramsQuery['size'] = $size;
    }

    private function addQuerySort(?string $sort, ?string $order): void
    {
        if ($sort && $order) {
            $this->paramsQuery['sort'] = [
                $sort => [
                    "unmapped_type" => "keyword",
                    "order" => $order
                ]
            ];
        }
    }

    private function addQueryFilter(Request $request)
    {
        $filter = $request->get('filter');

        if (isset($filter['price'])) {
            if (isset($filter['price']['from']) && !empty($filter['price']['from'])) {
                $this->paramsQuery['query']['bool']['filter']['range']['price']['gte'] = $filter['price']['from'];
            }

            if (isset($filter['price']['to']) && !empty($filter['price']['to'])) {
                $this->paramsQuery['query']['bool']['filter']['range']['price']['lte'] = $filter['price']['to'];
            }
        }

        if (isset($filter['date']) && $filter['date']) {
            $this->paramsQuery['query']['bool']['must'][]['match']['date'] = $filter['date'];
        }

        if (isset($filter['location']) && $filter['location']) {
            $this->addQuerySearchField('location', $filter['location']);
        }

        if (isset($filter['developer']) && $filter['developer']) {
            $this->addQuerySearchField('developer.keyword', $filter['developer']);
        }

        if (isset($filter['status']) && $filter['status']) {
            $this->addQuerySearchField('status.keyword', $filter['status']);
        }

        if (isset($filter['title']) && $filter['title']) {
            $this->paramsQuery['query']['bool']['must'][]['match']['title'] = $filter['title'];
        }
    }

    public function findDevelopersList(): ResultSet|array
    {
        $this->paramsQuery = [
            "_source" => [
                "developer"
            ]
        ];
        $this->paramsQuery['size'] = 10000;

        if ($this->client->getIndex(Config::KRISHA_KZ_INDEX)->exists()) {
            return $this->client->getIndex(Config::KRISHA_KZ_INDEX)->search($this->paramsQuery);
        }

        return [];
    }
}