<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Security\Voter;

use App\Entity\Store;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StoreVoter extends Voter
{
    // these strings are just invented: you can use anything
    final public const VIEW = 'view';
    final public const EDIT = 'edit';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    protected function supports($attribute, $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Store;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case self::EDIT:
                // $request = $this->requestStack->getCurrentRequest();
                // $account = $this->requestStack->getCurrentRequest();
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::VIEW:
                return true;
        }

        return false;
    }
}
