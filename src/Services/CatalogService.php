<?php

namespace App\Services;

use App\Services\Elasticsearch\Helper;
use App\Services\Info\Location;
use App\Services\Utilites\Translit;

class CatalogService
{
    private array $countDevelopsArray;
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
        $data['priceTotal'] = (int)$data['price'] * 40;

        return $data;
    }

    public function getListBuildingsMap(array $items): array
    {
        foreach ($items as $item) {
            $result[] = $item->getData();
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

    public function getDevelopersBuilding(): array
    {
        $developers = $this->helper->findDevelopersList();

        foreach ($developers->getResults() as $developer) {
            $data = $developer->getData();
            $developName = trim($data['developer']);
            $key = mb_substr($developName, 0, 1);

            $result[$key][$developName] = [
                'name' => $developName
            ];

            $result[$key][$developName]['count'] = $this->getCountDeveloper($developName);
        }

        return $result ?? [];
    }

    private function getCountDeveloper($developer): int
    {
        if (!isset($this->countDevelopsArray[$developer])) {
            $this->countDevelopsArray[$developer] = 1;
            return 1;
        } else {
            $this->countDevelopsArray[$developer] = $this->countDevelopsArray[$developer] + 1;
            return $this->countDevelopsArray[$developer];
        }
    }
}