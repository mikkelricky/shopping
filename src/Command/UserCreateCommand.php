<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\String\ByteString;

#[AsCommand(
    name: 'app:user:create',
    description: 'Add a short description for your command',
)]
class UserCreateCommand extends Command
{
    public function __construct(private UserProviderInterface $userProvider, private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $entityManager)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'User identifier')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Password')
            ->addOption('role', null, InputOption::VALUE_REQUIRED, 'Role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $identifier = $input->getArgument('identifier');
        $password = $input->getOption('password') ?? (string) ByteString::fromRandom(64);

        try {
            $this->userProvider->loadUserByIdentifier($identifier);
            throw new InvalidArgumentException(sprintf('User with identifier %s already exists', $identifier));
        } catch (UserNotFoundException $userNotFoundException) {
        }

        $user = (new User())
            ->setEmail($identifier);

        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            $password
        ));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User %s created', $user->getUserIdentifier()));

        return static::SUCCESS;
    }
}
