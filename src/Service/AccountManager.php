<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;

class AccountManager
{
    /** @var AccountRepository */
    private $accountRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(AccountRepository $accountRepository, EntityManagerInterface $entityManager)
    {
        $this->accountRepository = $accountRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Get an account by email.
     *
     * If no account with the given email exists, one will be created.
     *
     * @return Account
     */
    public function getAccount(string $email)
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
