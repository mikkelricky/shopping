<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashActionManager
{
    private static $sessionKey = '_flash_actions';

    private SessionInterface $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function addFlashAction(array $action, string $type, string $message): void
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
