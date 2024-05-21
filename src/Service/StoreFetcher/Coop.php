<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service\StoreFetcher;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Coop implements StoreFetcherInterface
{
    private static array $brands = [
        'SuperBrugsen' => 'SuperBrugsen',
        'Dagli\'Brugsen' => 'Dagli\'Brugsen',
        'Kvickly' => 'Kvickly',
        // 'Grønland' => 'Grønland',
        // 'FK' => 'FK',
        'Irma' => 'Irma',
        'Fakta' => 'Fakta',
        'Coop.dk' => 'Coop.dk',
        // 'COOP MAD' => 'COOP MAD',
    ];

    private array $options;

    public function __construct(array $coopConfig)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($coopConfig);
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'subscription-key',
        ])->setDefaults([
            'per-page' => 500,
        ]);
    }

    public function getStores(): array
    {
        $client = HttpClient::create([
            'headers' => [
                'Ocp-Apim-Subscription-Key' => $this->options['subscription-key'],
            ],
        ]);

        $results = [];
        $query = [
            'page' => 1,
            'size' => $this->options['per-page'],
        ];
        while (true) {
            $response = $client->request('GET', 'https://api.cl.coop.dk/storeapi/v1/stores', [
                'query' => $query,
            ]);

            $data = $response->toArray();
            if (isset($data['Data'])) {
                $results[] = $data['Data'];
            }
            if (isset($data['NextPage'])) {
                $query['page'] = $data['NextPage'];
            } else {
                break;
            }
        }

        $items = array_merge(...$results);

        $stores = [];
        foreach ($items as $item) {
            if (isset(self::$brands[$item['RetailGroupName']])) {
                $stores[self::$brands[$item['RetailGroupName']]][] = [
                    'name' => $item['Name'],
                    'address' => implode(\PHP_EOL, [
                        $item['Address'],
                        $item['Zipcode'].' '.$item['City'],
                    ]),
                    'latitude' => $item['Location']['coordinates'][0] ?? null,
                    'longitude' => $item['Location']['coordinates'][1] ?? null,
                ];
            }
        }

        return $stores;
    }
}
