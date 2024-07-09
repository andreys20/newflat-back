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

        $ourData = file_get_contents("data.json");
        $dates = json_decode($ourData);

        $params = ['body' => []];

        foreach ($dates as $key => $data) {

            $params['body'][] = [
                'index' => [
                    '_index' => 'mskguru',
                    '_id'    => $key
                ]
            ];

            $params['body'][] = [
                'id'     => $data->ID,
                'url' => $data->url,
                'title' => $data->title,
                'price_min' => $data->price_min,
                'price_max' => $data->price_max,
                'status' => $data->status,
                'date' => $data->date,
                'location' => $data->location,
                'developer' => $data->developer,
                'images' => $data->images,
                'parameters' => $data->parameters,
                'description' => $data->description,
            ];

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