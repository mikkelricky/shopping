<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;


#[ORM\Table(name: 'shopping_shopping_list')]
#[ORM\Entity(repositoryClass: 'App\Repository\ShoppingListRepository')]
class ShoppingList
{
    use TimestampableEntity;

    
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Account', inversedBy: 'lists')]
    #[ORM\JoinColumn(nullable: false)]
    private $account;

    
    #[ORM\OneToMany(targetEntity: 'App\Entity\ShoppingListItem', mappedBy: 'list', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private $items;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->items = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? self::class;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getDoneItems(): Collection
    {
        return $this->getItems()->filter(static function (ShoppingListItem $item) {
            return $item->isDone();
        });
    }

    public function getUndoneItems(): Collection
    {
        return $this->getItems()->filter(static function (ShoppingListItem $item) {
            return !$item->isDone();
        });
    }

    public function addItem(ShoppingListItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setList($this);
        }

        return $this;
    }

    public function removeItem(ShoppingListItem $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getList() === $this) {
                $item->setList(null);
            }
        }

        return $this;
    }
}
