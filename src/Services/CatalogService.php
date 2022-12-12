<?php

namespace App\Services;

use App\Services\Elasticsearch\Helper;
use App\Services\Utilites\Translit;

class CatalogService
{
    public function __construct(
        private Helper $helper
    ){}

    public function getListBuildings(array $items): array
    {
        foreach ($items as $item) {
            $result[] = $this->getBuilding($item);
        }

        return $result ?? [];
    }

    public function getBuilding($item): array
    {
        $data = $item->getData();

        $data['detail'] = $this->getDetailUrlBuilding($data['title'], $data['ID']);

        return $data;
    }

    private function getDetailUrlBuilding($name, $id): string
    {
        return '/complex/' . Translit::translit($name) . '?id=' . $id;
    }
}