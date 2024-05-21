<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service\StoreFetcher;

interface StoreFetcherInterface
{
    /**
     * @return array
     *               <store name> => [
     *               [
     *               name
     *               address
     *               latitude
     *               longitude
     *               ]
     *               ]
     */
    public function getStores(): array;
}
