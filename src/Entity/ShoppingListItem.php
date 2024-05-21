<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShoppingListItemRepository")
 * @ORM\Table(name="shopping_shopping_list_item")
 */
class ShoppingListItem implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ShoppingList", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $list;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $doneAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $quantity;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Store")
     */
    private $store;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShoppingListItemLogEntry", mappedBy="item", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $logEntries;

    public function __construct()
    {
        $this->logEntries = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getDoneAt(): ?DateTimeInterface
    {
        return $this->doneAt;
    }

    public function setDoneAt(?DateTimeInterface $doneAt): self
    {
        $this->doneAt = $doneAt;

        return $this;
    }

    public function isDone(): bool
    {
        return null !== $this->getDoneAt();
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @return Collection|ShoppingListItemLogEntry[]
     */
    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }

    public function __toString()
    {
        return $this->name ?? self::class;
    }
}
