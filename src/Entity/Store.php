<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoreRepository")
 * @ORM\Table(name="shopping_store")
 * @UniqueEntity("name")
 */
class Store implements TimestampableInterface
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="stores")
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Location", mappedBy="store", orphanRemoval=true)
     */
    private $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name ?? self::class;
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

    /**
     * @return Collection|Location[]
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setStore($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->contains($location)) {
            $this->locations->removeElement($location);
            // set the owning side to null (unless already changed)
            if ($location->getStore() === $this) {
                $location->setStore(null);
            }
        }

        return $this;
    }
}
