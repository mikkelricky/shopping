<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Entity\ShoppingList;
use App\Entity\ShoppingListItem;
use App\Entity\ShoppingListItemLogEntry;
use App\Repository\ShoppingListItemRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class ShoppingListItemManager
{
    /** @var ShoppingListItemRepository */
    private $itemRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(ShoppingListItemRepository $itemRepository, EntityManagerInterface $entityManager)
    {
        $this->itemRepository = $itemRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Get an shopping list item by name..
     *
     * If no account with the given email exists, one will be created.
     *
     * @return ShoppingListItem
     */
    public function getItem(ShoppingList $list, string $name)
    {
        $items = $this->getItems($list, [$name]);

        return ($items && \count($items) > 0) ? $items[0] : null;
    }

    public function addToList($item, ShoppingList $list)
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
     * Get an shopping list item by name..
     *
     * If no account with the given email exists, one will be created.
     *
     * @param string $name
     *
     * @return Collection
     */
    public function getItems(ShoppingList $list, array $names)
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

    public function setDone(ShoppingListItem $item)
    {
        $logEntry = new ShoppingListItemLogEntry($item);
        $this->entityManager->persist($logEntry);

        $item->setDoneAt(new DateTime());
        $this->entityManager->persist($item);

        $this->entityManager->flush();
    }

    public function setUndone(ShoppingListItem $item)
    {
        $item->setDoneAt(null);
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    private function parseName(string $name)
    {
        $quantity = null;

        $tokens = preg_split('/\s+/', $name, 3);

        // Quantity
        if (\count($tokens) > 1) {
            if (preg_match('/(?:[0-9]*[,.])?[0-9]+/', $tokens[0])) {
                $quantity = (float) str_replace(',', '.', array_shift($tokens));
            }
        }

        if (\count($tokens) > 1) {
            if (preg_match('/l|kg|g/', $tokens[0])) {
                $quantity .= ' '.array_shift($tokens);
            }
        }

        $name = implode(' ', $tokens);

        return [$name, $quantity];
    }
}
