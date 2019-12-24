<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\ShoppingListItem;

use App\Entity\ShoppingListItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Item name',
                ],
            ])
            ->add('quantity')
            ->add('description')
            ->add('store');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShoppingListItem::class,
        ]);
    }
}
