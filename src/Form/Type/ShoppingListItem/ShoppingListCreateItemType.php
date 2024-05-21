<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\ShoppingListItem;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ShoppingListCreateItemType extends ShoppingListItemType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Item name',
                ],
            ]);
    }
}
