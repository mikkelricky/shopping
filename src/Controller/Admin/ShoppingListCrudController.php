<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller\Admin;

use App\Entity\ShoppingList;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class ShoppingListCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ShoppingList::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewList = Action::new('viewItems', 'View items')
            ->linkToUrl(fn (ShoppingList $list) => $this->generateUrl('shopping_list_items', ['id' => $list->getId()]));

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $viewList)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('Shopping list'))
            ->setEntityLabelInPlural(new TranslatableMessage('Shopping lists'))
            ->showEntityActionsInlined()
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnDetail();
        yield TextField::new('name');
        yield AssociationField::new('items');
        // TextEditorField::new('description'),
    }
}
