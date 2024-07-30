<?php

namespace App\Command;

use Elastica\Client;
use Elasticsearch\ClientBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:import-json-data',
    description: 'Import JSON data from elasticsearch'
)]
class ImportJsonData extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = ClientBuilder::create()
            ->setHosts(['localhost:9200'])
            ->setBasicAuthentication('elastic', 'elastic')
            ->build();

        $ourData = file_get_contents("data_developers.json");


        try {
        $dates = json_decode($ourData);
        } catch (\Exception $exception) {
            dd($exception);
        }

        $params = ['body' => []];

        foreach ($dates as $key => $data) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'mskguru_developers',
                    '_id'    => $key
                ]
            ];

            $params['body'][] = [
                'name'     => $data->name,
                'logo' => $data->logo,
                'year' => $data->year,
                'rented_qnty' => $data->rented_qnty,
                'being_built_qnty' => $data->being_built_qnty,
                'description' => $data->description
            ];

//            $params['body'][] = [
//                'id'     => $data->ID,
//                'url' => $data->url,
//                'title' => $data->title,
//                'price_min' => $data->price_min,
//                'price_max' => $data->price_max,
//                'price_per_metr_max' => $data->price_per_metr_max,
//                'price_per_metr_min' => $data->price_per_metr_min,
//                'status' => $data->status,
//                'date' => $data->date,
//                'location' => $data->location,
//                'developer' => $data->developer,
//                'images' => $data->images,
//                'parameters' => $data->parameters,
//                'description' => $data->description,
//            ];

            if ($key % 1000 == 0) {
                $responses = $client->bulk($params);


                $params = ['body' => []];

                // unset the bulk response when you are done to save memory
                unset($responses);
            }
        }

        if (!empty($params['body'])) {
            $responses = $client->bulk($params);
        }


        return Command::SUCCESS;
    }
}