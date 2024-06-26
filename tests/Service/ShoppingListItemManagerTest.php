<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Service;

use App\Service\ShoppingListItemManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShoppingListItemManagerTest extends KernelTestCase
{
    /**
     * @dataProvider parseNameProvider
     *
     * @param array<?string> $expected
     */
    public function testParseName(string $name, array $expected): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $service = $container->get(ShoppingListItemManager::class);
        $method = new \ReflectionMethod($service, 'parseName');
        $method->setAccessible(true);
        $actual = $method->invoke($service, $name);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<mixed>
     */
    public function parseNameProvider(): array
    {
        return [
            ['Noget med et meget langt navn', ['Noget med et meget langt navn', null]],

            ['2 l milk', ['milk', '2 l']],
            ['3 French hens', ['French hens', '3']],

            ['7 æbler', ['æbler', '7']],

            ['2 l mælk', ['mælk', '2 l']],
            ['2 liter mælk', ['mælk', '2 liter']],
            ['0,5 l mælk', ['mælk', '0,5 l']],
            ['2,5 l mælk', ['mælk', '2,5 l']],
            ['2,5 liter mælk', ['mælk', '2,5 liter']],

            ['500 g hvedemel', ['hvedemel', '500 g']],
            ['500 gram hvedemel', ['hvedemel', '500 gram']],

            ['500 grams flour', ['flour', '500 grams']],

            ['2 lime med skal', ['lime med skal', '2']],
            ['2 lime med skal ', ['lime med skal', '2']],
            ['2  lime med skal ', ['lime med skal', '2']],

            ['luft ', ['luft', null]],
            [' luft ', ['luft', null]],
            [' luft', ['luft', null]],

            ['2 l ', ['', '2 l']],
            ['2 li ', ['li', '2']],
        ];
    }
}
