<?php

namespace App\Services;

use App\Services\Elasticsearch\Helper;
use App\Services\Info\Location;
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
        $data['priceTotal'] = number_format((int)$data['price'] * 40, 0, '', ' ');
        $data['price'] = number_format((int)$data['price'], 0, '', ' ');

        return $data;
    }

    public function getListBuildingsMap(array $items): array
    {
        foreach ($items as $item) {
            $data = $item->getData();
            $data['detail'] = $this->getDetailUrlBuilding($data['title'], $data['ID']);
            $data['priceTotal'] = number_format((int)$data['price'] * 40, 0, '', ' ');
            $result[] = $data;
        }

        return $result ?? [];
    }

    private function getDetailUrlBuilding($name, $id): string
    {
        return '/complex/' . Translit::translit($name) . '?id=' . $id;
    }

    public function getDistrictLocationBuilding(): array
    {
        foreach (Location::DISTRICT as $item) {

            $data[mb_substr($item, 0, 1)][] = [
               'name' => $item,
               'count' => $this->helper->findCountBuildingInDistrict($item)
            ];
        }

        return $data ?? [];
    }

    public function getBuildingToDeveloper($developName): array
    {
        $buildingsQuery = $this->helper->findBuildingsToDeveloper($developName);

        foreach ($buildingsQuery->getResults() as $item) {
            $result[] = $this->getBuilding($item);
        }

        return $result ?? [];
    }
}