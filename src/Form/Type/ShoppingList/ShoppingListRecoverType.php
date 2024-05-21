<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\ShoppingList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class ShoppingListRecoverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Your e-mail address',
            ]);
    }
}
