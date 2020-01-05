<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Entity\Account;
use App\Entity\ShoppingList;
use App\Entity\ShoppingListItem;
use App\Form\Type\ShoppingList\ShoppingListAddItemsType;
use App\Form\Type\ShoppingList\ShoppingListCreateType;
use App\Form\Type\ShoppingList\ShoppingListRecoverType;
use App\Form\Type\ShoppingList\ShoppingListShareType;
use App\Form\Type\ShoppingList\ShoppingListType;
use App\Form\Type\ShoppingListItem\ShoppingListCreateItemType;
use App\Repository\ShoppingListRepository;
use App\Service\FlashActionManager;
use App\Service\ShoppingListItemManager;
use App\Service\ShoppingListManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route(
 *     "/{_locale}",
 *     locale="en",
 *     requirements={
 *         "_locale": "da|en"
 *     }
 * )
 */
class ShoppingListController extends AbstractController
{
    /** @var ShoppingListManager */
    private $listManager;

    public function __construct(EntityManagerInterface $entityManager, FlashActionManager $flashActionManager, TranslatorInterface $translator, ShoppingListManager $listManager)
    {
        parent::__construct($entityManager, $flashActionManager, $translator);
        $this->listManager = $listManager;
    }

    /**
     * @Route("/list", name="shopping_list_index", methods="GET")
     */
    public function index(): Response
    {
        return $this->render('shopping_list/index.html.twig');
    }

    /**
     * @Route("/list/new", name="shopping_list_new", methods="GET|POST")
     * @Route("/{account}/list/new", name="shopping_account_list_new", methods="GET|POST")
     */
    public function new(Request $request, Account $account = null): Response
    {
        $list = new ShoppingList();
        $list->setAccount($account);
        $form = $this->createForm(ShoppingListCreateType::class, $list);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($list);
            $em->flush();

            $this->listManager->notifyListCreated($list);

            return $this->redirectToRoute('shopping_account_list_created', [
                'account' => $list->getAccount()->getId(),
                'list' => $list->getId(),
            ]);
        }

        return $this->render('shopping_list/new.html.twig', [
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{account}/list/{list}/created", name="shopping_account_list_created", methods="GET")
     */
    public function created(Request $request, Account $account, ShoppingList $list): Response
    {
        return $this->render('shopping_list/created.html.twig', [
            'account' => $account,
            'list' => $list,
        ]);
    }

    /**
     * @Route("/account/recover", name="shopping_list_recover", methods="GET|POST")
     */
    public function recover(Request $request, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ShoppingListRecoverType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            if ($this->listManager->recoverLists($email)) {
                $this->success($translator->trans('Mail sent to {email}.', ['email' => $email]));

                return $this->redirectToRoute('shopping_list_index');
            }
            $this->error($translator->trans('Error sending email to {email}', ['email' => $email]));
        }

        return $this->render('shopping_list/recover.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{account}/list", name="shopping_account", methods="GET")
     */
    public function list(Account $account, ShoppingListRepository $listRepository): Response
    {
        return $this->render('shopping_list/list.html.twig', [
            'account' => $account,
            'lists' => $listRepository->findByAccount($account),
        ]);
    }

    /**
     * @Route("/{account}/list/{list}/share", name="shopping_account_list_share", methods="GET|POST")
     */
    public function share(Request $request, Account $account, ShoppingList $list): Response
    {
        $form = $this->createForm(ShoppingListShareType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $sender = $form->get('sender')->getData();
            $message = $form->get('message')->getData();
            if ($this->listManager->shareList($list, $email, ['sender' => $sender, 'message' => $message])) {
                $this->success('List {list} successfully shared with {email}', ['list' => $list->getName(), 'email' => $email]);

                return $this->redirectToRoute('shopping_account', ['account' => $list->getAccount()->getId()]);
            }
            $this->error('Error sharing list {list} with {email}', [
                'list' => $list->getName(),
                'email' => $email,
            ]);
        }

        return $this->render('shopping_list/share.html.twig', [
            'account' => $account,
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list/{list}/items", name="shopping_list_items", methods="GET|POST")
     * @Route("/{account}/list/{list}/items", name="shopping_account_list_items", methods="GET|POST")
     */
    public function items(Request $request, Account $account = null, ShoppingList $list, ShoppingListItemManager $itemManager): Response
    {
        $item = new ShoppingListItem();
        $form = $this->createForm(ShoppingListCreateItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $itemManager->getItem($list, $item->getName());
            $item->setDoneAt(null);
            $list->addItem($item);
            $this->getDoctrine()->getManager()->persist($list);
            $this->getDoctrine()->getManager()->flush();

            $this->success('Item %item% added', ['%item%' => $item->getName()])
                ->addFlashAction([
                    'url' => $this->generateUrl('shopping_list_item_edit', ['item' => $item->getId()]),
                    'message' => $this->translate('Edit item %item%', ['%item%' => (string) $item]),
                ]);

            return null !== $account
                ? $this->redirectToRoute('shopping_account_list_items', ['account' => $account->getId(), 'list' => $list->getId()])
                : $this->redirectToRoute('shopping_list_items', ['list' => $list->getId()]);
        }

        $filter = $request->get('filter');

        return $this->render('shopping_list/items.html.twig', [
            'account' => $account,
            'list' => $list,
            'undone_items' => $this->listManager->applyFilter($list->getUndoneItems(), $filter),
            'done_items' => $this->listManager->applyFilter($list->getDoneItems(), $filter),
            'filter' => $filter,
            'add_item_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{account}/list/{list}/edit", name="shopping_account_list_edit", methods="GET|POST")
     */
    public function edit(Request $request, Account $account, ShoppingList $list): Response
    {
        $form = $this->createForm(ShoppingListType::class, $list);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shopping_list_edit', ['list' => $list->getId()]);
        }

        return $this->render('shopping_list/edit.html.twig', [
            'account' => $account,
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{account}/list/{list}", name="shopping_account_list_delete", methods="DELETE")
     */
    public function delete(Request $request, Account $account, ShoppingList $list): Response
    {
        if ($this->isCsrfTokenValid('delete'.$list->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($list);
            $em->flush();
        }

        return $this->redirectToRoute('shopping_list_index');
    }

    /**
     * @Route("/list/{list}/log", name="shopping_list_log", methods="GET")
     * @Route("/{account}/list/{list}/log", name="shopping_account_list_log", methods="GET")
     */
    public function log(Request $request, Account $account = null, ShoppingList $list, ShoppingListItemManager $itemManager)
    {
        return $this->render('shopping_list/log.html.twig', [
            'account' => $account,
            'list' => $list,
        ]);
    }

    /**
     * @Route("/list/{list}/item/add", name="shopping_list_add_item", methods="POST")
     * @Route("/{account}/list/{list}/item/add", name="shopping_account_list_add_item", methods="POST")
     */
    public function addItem(Request $request, Account $account = null, ShoppingList $list, ShoppingListItemManager $itemManager)
    {
        $name = $request->request->get('name');
        if ($this->isCsrfTokenValid('add_item_'.$name, $request->request->get('_token'))) {
            $item = $itemManager->addToList($name, $list);

            $this->success('Item %item% added to list', ['%item%' => $item->getName()])
                ->addFlashAction([
                    'url' => $this->generateUrl('shopping_list_item_edit', ['item' => $item->getId()]),
                    'message' => $this->translate('Edit item %item%', ['%item%' => $item->getName()]),
                ]);
        } else {
            $this->error('Error adding item %item% to list', ['%item%' => $name]);
        }

        return $this->goBack($request, null !== $account
            ? $this->generateUrl('shopping_account_list_add_item', ['account' => $account->getId(), 'list' => $list->getId()])
            : $this->generateUrl('shopping_list_add_item', ['list' => $list->getId()]));
    }

    /**
     * @Route("/list/{list}/items/add", name="shopping_list_add_items", methods="GET|POST")
     * @Route("/{account}/list/{list}/items/add", name="shopping_account_list_add_items", methods="GET|POST")
     */
    public function addItems(Request $request, Account $account = null, ShoppingList $list, ShoppingListItemManager $itemManager)
    {
        $form = $this->createForm(ShoppingListAddItemsType::class, null, [
            'list' => $list,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $items = $form->get('items')->getData();

            foreach ($items as $item) {
                $item->setDoneAt(null);
                $list->addItem($item);
            }

            $existingItems = $items->filter(function (ShoppingListItem $item) {
                return null !== $item->getId();
            });
            $newItems = $items->filter(function (ShoppingListItem $item) {
                return null === $item->getId();
            });

            $this->getDoctrine()->getManager()->persist($list);
            $this->getDoctrine()->getManager()->flush();

            $this->info('Items added; %count_existing% existing; %count_new% new', [
                '%count_existing%' => \count($existingItems),
                '%count_new%' => \count($newItems),
            ]);

            if (null !== $account) {
                return $this->redirectToRoute('shopping_account_list_items', [
                    'account' => $account->getId(),
                    'list' => $list->getId(),
                ]);
            }

            return $this->redirectToRoute('shopping_list_items', ['list' => $list->getId()]);
        }

        return $this->render('shopping_list/add_items.html.twig', [
            'account' => $account,
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }
}
