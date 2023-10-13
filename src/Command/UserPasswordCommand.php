<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsCommand(
    name: 'app:user:password',
    description: 'Add a short description for your command',
)]
class UserPasswordCommand extends Command
{
    public function __construct(private readonly UserProviderInterface $userProvider, private readonly UserPasswordHasherInterface $passwordHasher, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'User identifier')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $identifier = $input->getArgument('identifier');
        $password = $input->getOption('password');

        if (empty($password)) {
            $helper = $this->getHelper('question');

            $question = new Question(sprintf('Password for %s? ', $identifier));
            $question->setHidden(true);
            $question->setHiddenFallback(false);
            $question->setValidator(function ($answer) {
                if (empty($answer)) {
                    throw new \RuntimeException('The password cannot be empty');
                }

                return $answer;
            });

            $password = $helper->ask($input, $output, $question);
        }

        if (empty($password)) {
        }

        try {
            $user = $this->userProvider->loadUserByIdentifier($identifier);
            \assert($user instanceof PasswordAuthenticatedUserInterface);
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $password
            ));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success(sprintf('Password for user %s updated', $user->getUserIdentifier()));
        } catch (UserNotFoundException) {
            throw new InvalidArgumentException(sprintf('User with identifier %s does not exist', $identifier));
        }

        return static::SUCCESS;
    }
}
