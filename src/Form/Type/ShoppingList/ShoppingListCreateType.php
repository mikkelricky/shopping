<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
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
    /** @var AccountManager */
    private $accountManager;

    public function __construct(AccountManager $accountManager)
    {
        $this->accountManager = $accountManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $account = $builder->getData()->getAccount();
        $builder
            ->add('account', EmailType::class, [
                'label' => 'Your e-mail address',
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
                function (Account $account = null) {
                    return $account ? $account->getEmail() : null;
                },
                function (string $email) {
                    return $this->accountManager->getAccount($email);
                }
            ));
    }
}
