<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashActionManager
{
    /** @var SessionInterface */
    protected $session;

    private static $sessionKey = '_flash_actions';

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addFlashAction(array $action, string $type, string $message)
    {
        $actions = $this->session->get(self::$sessionKey, []);
        $actions[$type][$message][] = $action;
        $this->session->set(self::$sessionKey, $actions);
    }

    public function getFlashActions(string $type, string $message)
    {
        try {
            $actions = $this->session->get(self::$sessionKey);
            if (null !== $type && null !== $message && isset($actions[$type][$message])) {
                $this->session->set(self::$sessionKey, null);

                return $actions[$type][$message];
            }

            return null;
        } catch (\RuntimeException $e) {
            return null;
        }
    }
}
