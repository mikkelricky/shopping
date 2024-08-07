<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Command;

use App\Service\StoreManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:store:fetch',
    description: 'fetch stores',
)]
class StoreFetchCommand extends Command
{
    public function __construct(private readonly StoreManager $storeManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = new ConsoleLogger($output);
        $this->storeManager->setLogger($logger);
        $this->storeManager->updateStores();

        return static::SUCCESS;
    }
}
