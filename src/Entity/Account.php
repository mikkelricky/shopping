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

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 * @ORM\Table(name="shopping_account")
 */
class Account
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShoppingList", mappedBy="account", orphanRemoval=true)
     */
    private $lists;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Store", mappedBy="account")
     */
    private $stores;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->lists = new ArrayCollection();
        $this->stores = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLists(): Collection
    {
        return $this->lists;
    }

    public function addList(ShoppingList $list): self
    {
        if (!$this->lists->contains($list)) {
            $this->lists[] = $list;
            $list->setAccount($this);
        }

        return $this;
    }

    public function removeList(ShoppingList $list): self
    {
        if ($this->lists->contains($list)) {
            $this->lists->removeElement($list);
            // set the owning side to null (unless already changed)
            if ($list->getAccount() === $this) {
                $list->setAccount(null);
            }
        }

        return $this;
    }

    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function addStore(Store $store): self
    {
        if (!$this->stores->contains($store)) {
            $this->stores[] = $store;
            $store->setAccount($this);
        }

        return $this;
    }

    public function removeStore(Store $store): self
    {
        if ($this->stores->contains($store)) {
            $this->stores->removeElement($store);
            // set the owning side to null (unless already changed)
            if ($store->getAccount() === $this) {
                $store->setAccount(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s#%s', self::class, $this->getId());
    }
}
