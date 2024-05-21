<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Form\Type\ShoppingList;

use App\Service\ShoppingListItemManager;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListAddItemsType extends ShoppingListType
{
    /** @var ShoppingListItemManager */
    private $itemManager;

    public function __construct(ShoppingListItemManager $itemManager)
    {
        $this->itemManager = $itemManager;
    }

    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', TextareaType::class, [
                'help' => 'One item per line',
            ])
        ;
        $builder->get('items')
            ->addModelTransformer(new CallbackTransformer(
                static function (array $items = null) {
                    return implode(\PHP_EOL, $items ?? []);
                },
                function (string $items) use ($options) {
                    $names = array_filter(array_map('trim', explode(\PHP_EOL, $items)));

                    return $this->itemManager->getItems($options['list'], $names);
                }
            ));
    }

    /**
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('list');
        parent::configureOptions($resolver); // TODO: Change the autogenerated stub
    }
}
