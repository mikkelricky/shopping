<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use App\Repository\ShoppingListItemLogEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'shopping_shopping_list_log_entry')]
#[ORM\Entity(repositoryClass: ShoppingListItemLogEntryRepository::class)]
class ShoppingListItemLogEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $quantity = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: ShoppingList::class, inversedBy: 'logEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingList $list = null;

    public function __construct(#[ORM\ManyToOne(targetEntity: ShoppingListItem::class, inversedBy: 'logEntries')]
        #[ORM\JoinColumn(nullable: false)]
        private ShoppingListItem $item)
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTime();
        $this->list = $item->getList();
        $this->name = $item->getName();
        $this->quantity = $item->getQuantity();
    }

    public function getId(): ?Uuid
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

    public function getCreatedAt(): ?\DateTimeInterface
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
