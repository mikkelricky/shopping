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
use Symfony\Contracts\HttpClient\ResponseInterface;

use function GuzzleHttp\Psr7\parse_header;

class SallingGroup implements StoreFetcherInterface
{
    private static array $brands = [
        'bilka' => 'Bilka',
        'salling' => 'Salling',
        'foetex' => 'Føtex',
        // 'carlsjr' => 'Carl\'s Jr.',
        'netto' => 'Netto',
        // 'br' => 'BR',
    ];

    private array $options;

    public function __construct(array $sallingGroupConfig)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($sallingGroupConfig);
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'bearer',
        ])->setDefaults([
            'per-page' => 500,
        ]);
    }

    public function getStores(): array
    {
        $client = HttpClient::create([
            'headers' => [
                'Authorization' => 'Bearer '.$this->options['bearer'],
            ],
        ]);

        $results = [];
        $response = $client->request('GET', 'https://api.sallinggroup.com/v2/stores/', [
            'query' => [
                'fields' => 'brand,name,address,coordinates',
                'per_page' => $this->options['per-page'],
            ],
        ]);

        $results[] = $response->toArray();

        $links = $this->getLinks($response);
        while (isset($links['next'])) {
            $response = $client->request('GET', $links['next']);
            $results[] = $response->toArray();
            $links = $this->getLinks($response);
        }

        $items = array_merge(...$results);

        $stores = [];
        foreach ($items as $item) {
            if (isset(self::$brands[$item['brand']])) {
                $stores[self::$brands[$item['brand']]][] = [
                    'name' => $item['name'],
                    'address' => implode(\PHP_EOL, [
                        $item['address']['street'],
                        $item['address']['zip'].' '.$item['address']['city'],
                    ]),
                    'latitude' => $item['coordinates'][0],
                    'longitude' => $item['coordinates'][1],
                ];
            }
        }

        return $stores;
    }

    /**
     * @return string[]|null
     *
     * @psalm-return array<string>|null
     */
    private function getLinks(ResponseInterface $response): ?array
    {
        $headers = $response->getHeaders();
        if (!isset($headers['link'])) {
            return null;
        }
        $data = parse_header(reset($headers['link']));

        $links = [];

        foreach ($data as $item) {
            if (isset($item[0], $item['rel'])) {
                $links[$item['rel']] = substr((string) $item[0], 1, -1);
            }
        }

        return $links;
    }
}
