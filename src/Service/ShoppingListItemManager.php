<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Entity\ShoppingList;
use App\Entity\ShoppingListItem;
use App\Entity\ShoppingListItemLogEntry;
use App\Repository\ShoppingListItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class ShoppingListItemManager
{
    public function __construct(private readonly ShoppingListItemRepository $itemRepository, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Get an shopping list item by name..
     *
     * If no account with the given email exists, one will be created.
     */
    public function getItem(ShoppingList $list, string $name): ?ShoppingListItem
    {
        $items = $this->getItems($list, [$name]);

        return ($items && \count($items) > 0) ? $items[0] : null;
    }

    /**
     * @param scalar|null $item
     */
    public function addToList($item, ShoppingList $list): ShoppingListItem
    {
        if (!$item instanceof ShoppingListItem) {
            $item = $this->getItem($list, $item);
        }
        $item = $this->getItem($list, $item->getName());
        $item->setDoneAt(null);
        $list->addItem($item);
        $this->entityManager->persist($list);
        $this->entityManager->flush();

        return $item;
    }

    /**
     * Get shopping list items by names.
     */
    public function getItems(ShoppingList $list, array $names): Collection
    {
        $items = new ArrayCollection();

        foreach ($names as $name) {
            [$name, $quantity] = $this->parseName($name);
            $item = $this->itemRepository->findOneBy(['list' => $list, 'name' => $name]);
            if (null === $item) {
                $item = new ShoppingListItem();
                $item->setName($name);
            }
            $item->setQuantity($quantity);
            $items[] = $item;
        }

        return $items;
    }

    public function setDone(ShoppingListItem $item): void
    {
        $logEntry = new ShoppingListItemLogEntry($item);
        $this->entityManager->persist($logEntry);

        $item->setDoneAt(new \DateTime());
        $this->entityManager->persist($item);

        $this->entityManager->flush();
    }

    public function setUndone(ShoppingListItem $item): void
    {
        $item->setDoneAt(null);
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    private function parseName(string $name): array
    {
        $quantity = null;

        $tokens = preg_split('/\s+/', $name, 3);

        // Quantity
        if ((\count($tokens) > 1) && preg_match('/(?:\d*[,.])?\d+/', $tokens[0])) {
            $quantity = array_shift($tokens);
        }

        // Unit
        $units = [
            'l', 'liter', 'litre', 'litres',
            'kg', 'kilo', 'kilos',
            'g', 'gram', 'grams',
        ];
        if (null !== $quantity && \count($tokens) > 1
            && \in_array(strtolower((string) $tokens[0]), $units, true)) {
            $quantity .= ' '.array_shift($tokens);
        }

        $name = trim(implode(' ', $tokens));

        return [$name, $quantity];
    }
}
