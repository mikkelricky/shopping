<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service\StoreFetcher;

use Symfony\Component\HttpClient\HttpClient;

class Rema1000 implements StoreFetcherInterface
{
    public function getStores(): array
    {
        $client = HttpClient::create();

        $response = $client->request('GET', 'https://rema1000.dk/wp-content/themes/rema1000/get_stores.php');
        $items = $response->toArray();

        $locations = array_map(static function (array $item) {
            return [
                'name' => 'REMA 1000',
                'address' => implode(PHP_EOL, [
                    $item['address'],
                    $item['zipcode'].' '.$item['city'],
                ]),
                'latitude' => $item['latitude'],
                'longitude' => $item['longitude'],
            ];
        }, $items);

        return ['REMA 1000' => $locations];
    }
}
