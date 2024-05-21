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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

class StoreFetchCommand extends Command
{
    protected static $defaultName = 'app:store:fetch';

    /** @var StoreManager */
    private $storeManager;

    public function __construct(StoreManager $storeManager)
    {
        parent::__construct();
        $this->storeManager = $storeManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new ConsoleLogger($output);
        $this->storeManager->setLogger($logger);
        $this->storeManager->updateStores();

        return 0;
    }
}
