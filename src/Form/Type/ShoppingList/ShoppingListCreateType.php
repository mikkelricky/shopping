<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\ShoppingList;

use App\Entity\Account;
use App\Service\AccountManager;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class ShoppingListCreateType extends ShoppingListType
{
    public function __construct(private readonly AccountManager $accountManager)
    {
    }

    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $account = $builder->getData()->getAccount();
        $builder
            ->add('account', EmailType::class, [
                'label' => 'Your e-mail address',
                'help' => 'We will send details on how to access the shopping list to this address.',
                'disabled' => null !== $account,
            ])
            ->add('name', null, [
                'label' => 'Shopping list name',
            ])
            ->add('description', null, [
                'label' => 'Shopping list description',
            ]);

        $builder->get('account')
            ->addModelTransformer(new CallbackTransformer(
                static fn (?Account $account = null) => $account ? $account->getEmail() : null,
                fn (string $email) => $this->accountManager->getAccount($email)
            ));
    }
}
