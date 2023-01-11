<?php

namespace App\Services;

use App\Services\Elasticsearch\Helper;
use App\Services\Utilites\Translit;
use Symfony\Component\HttpFoundation\Request;

class DeveloperService
{
    private array $countDevelopsArray;

    public function __construct(
        private Helper $helper
    )
    {
    }

    public function getDevelopersBuilding(): array
    {
        $developers = $this->helper->findDevelopersList();

        foreach ($developers->getResults() as $developer) {
            $data = $developer->getData();
            $developName = trim($data['developer']);
            $key = mb_substr($developName, 0, 1);

            $detail = $this->getDetailUrlDeveloper($developName);
            $result[$key][$developName] = [
                'name' => $developName,
                'detail' => $detail
            ];

            $result[$key][$developName]['count'] = $this->getCountDeveloper($developName);
        }

        return $result ?? [];
    }

    private function getDetailUrlDeveloper($name): string
    {
        return '/developer/' . Translit::translit($name) . '?id=' . $name;
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

    public function getDeveloper(Request $request): array
    {
        $developElasticQuery = $this->helper->findDeveloper($request->get('id'));

        if (isset($developElasticQuery->getResults()[0])) {
            $develop = $developElasticQuery->getResults()[0];
            $data = $develop->getData();

            $data['detail'] = $this->getDetailUrlDeveloper($data['name']);
        }

        return $data ?? [];
    }
}