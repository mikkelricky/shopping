<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Repository;

use App\Entity\ShoppingListItemLogEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShoppingListItemLogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShoppingListItemLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShoppingListItemLogEntry[]    findAll()
 * @method ShoppingListItemLogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShoppingListItemLogEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingListItemLogEntry::class);
    }
}
