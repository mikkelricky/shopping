<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\ShoppingListItem;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatableMessage;

class ShoppingListCreateItemType extends ShoppingListItemType
{
    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => new TranslatableMessage('Add item'),
                ],
                'help' => new TranslatableMessage('Prefix item name with a number and optional unit to set a quantity, e.g. “2 l milk”.'),
            ])
            ->add('add', SubmitType::class, [
                'label' => new TranslatableMessage('Add item'),
            ])
        ;
    }
}
