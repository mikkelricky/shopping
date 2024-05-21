<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use App\Repository\ShoppingListItemRepository;
use App\Validator\ItemName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'shopping_shopping_list_item')]
#[ORM\Entity(repositoryClass: ShoppingListItemRepository::class)]
class ShoppingListItem implements \Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id;

    /**
     * @ItemName()
     */
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: ShoppingList::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingList $list = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $doneAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $quantity = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: ShoppingListItemLogEntry::class, mappedBy: 'item', orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $logEntries;

    #[ORM\ManyToMany(targetEntity: Store::class)]
    private Collection $stores;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->logEntries = new ArrayCollection();
        $this->stores = new ArrayCollection();
    }

    public function getId(): ?Uuid
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

    public function getDoneAt(): ?\DateTimeInterface
    {
        return $this->doneAt;
    }

    public function setDoneAt(?\DateTimeInterface $doneAt): self
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

    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }

    public function __toString(): string
    {
        return (string) ($this->name ?? self::class);
    }

    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function addStore(Store $store): self
    {
        if (!$this->stores->contains($store)) {
            $this->stores[] = $store;
        }

        return $this;
    }

    public function removeStore(Store $store): self
    {
        if ($this->stores->contains($store)) {
            $this->stores->removeElement($store);
        }

        return $this;
    }
}
