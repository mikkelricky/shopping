<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Twig;

use App\Service\FlashActionManager;
use Symfony\Bridge\Twig\AppVariable as BaseAppVariable;

class AppVariable extends BaseAppVariable
{
    /** @var FlashActionManager */
    private $flashActionManager;

    public function setFlashActionManager(FlashActionManager $flashActionManager): void
    {
        $this->flashActionManager = $flashActionManager;
    }

    public function getFlashActions(string $type, string $message)
    {
        return $this->flashActionManager->getFlashActions($type, $message);
    }
}
