<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\ShoppingListItem;
use App\Form\Type\ShoppingListItem\ShoppingListItemType;
use App\Service\ShoppingListItemManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     "/{_locale}/list/item/{item}",
 *     locale="en",
 *     requirements={
 *         "_locale": "da|en"
 *     }
 * )
 */
class ShoppingListItemController extends AbstractController
{
    /**
     * @Route("/edit", name="shopping_list_item_edit")
     */
    public function edit(Request $request, ShoppingListItem $item)
    {
        $form = $this->createForm(ShoppingListItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $this->info('Item %item% updated', ['%item%' => (string) $item]);

            return $this->redirectToRoute('shopping_list_items', ['id' => $item->getList()->getId()]);
        }

        return $this->render('shopping_list_item/edit.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/done", name="shopping_list_item_done", methods="PUT")
     */
    public function done(Request $request, ShoppingListItem $item, ShoppingListItemManager $itemManager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('item_done'.$item->getId(), $request->request->get('_token'))) {
            $itemManager->setDone($item);
            $this->success('Item %item% marked as done', ['%item%' => (string) $item])
            ->addFlashAction([
                'route' => [
                    'name' => 'shopping_list_item_done',
                    'parameters' => [
                        'item' => $item->getId(),
                    ],
                    '_method' => 'PUT',
                    '_token' => 'item_done_undo'.$item->getId(),
                ],
                'message' => $this->translate('Undo item %item% marked as done', ['%item%' => (string) $item]),
            ]);
        } elseif ($this->isCsrfTokenValid('item_done_undo'.$item->getId(), $request->request->get('_token'))) {
            $itemManager->setUndone($item);
            $this->success('Item %item% marked as undone', ['%item%' => (string) $item]);
        } else {
            $this->error('Error marking %item% as done', ['%item%' => (string) $item]);
        }

        return $this->redirectToRoute('shopping_list_items', ['id' => $item->getList()->getId()] + $request->query->all());
    }

    /**
     * @Route("/log", name="shopping_list_item_log", methods="GET")
     */
    public function log(ShoppingListItem $item): Response
    {
        return $this->render('shopping_list_item/log.html.twig', [
            'item' => $item,
        ]);
    }
}
