<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
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

    public function __construct()
    {
        $this->lists = new ArrayCollection();
    }

    public function getId(): ?string
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

    /**
     * @return Collection|ShoppingList[]
     */
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
}
