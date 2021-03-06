<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShoppingListItemLogEntryRepository")
 * @ORM\Table(name="shopping_shopping_list_log_entry")
 */
class ShoppingListItemLogEntry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShoppingListItem", inversedBy="logEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $quantity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShoppingList", inversedBy="logEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $list;

    public function __construct(ShoppingListItem $item)
    {
        $this->createdAt = new DateTime();
        $this->item = $item;
        $this->list = $item->getList();
        $this->name = $item->getName();
        $this->quantity = $item->getQuantity();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getItem(): ?ShoppingListItem
    {
        return $this->item;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getList(): ?ShoppingList
    {
        return $this->list;
    }

    public function setList(?ShoppingList $list): self
    {
        $this->list = $list;

        return $this;
    }
}
