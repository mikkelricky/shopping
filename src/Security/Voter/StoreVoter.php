<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
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
    /** @var RequestStack */
    private $requestStack;

    // these strings are just invented: you can use anything
    public const VIEW = 'view';
    public const EDIT = 'edit';

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    protected function supports($attribute, $subject)
    {
        return \in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Store;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case self::EDIT:
                $request = $this->requestStack->getCurrentRequest();
                $account = $this->requestStack->getCurrentRequest();
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case self::VIEW:
                return true;
        }

        return false;
    }
}
