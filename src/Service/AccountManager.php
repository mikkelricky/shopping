<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;

class AccountManager
{
    public function __construct(private readonly AccountRepository $accountRepository, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Get an account by email.
     *
     * If no account with the given email exists, one will be created.
     */
    public function getAccount(string $email): Account
    {
        $account = $this->accountRepository->findOneBy(['email' => $email]);
        if (null === $account) {
            $account = new Account();
            $account->setEmail($email);
            $this->entityManager->persist($account);
            $this->entityManager->flush();
        }

        return $account;
    }
}
