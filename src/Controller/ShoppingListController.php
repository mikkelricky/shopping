<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
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
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $this->listManager->notifyListCreated($list);

            return $this->redirectToRoute('shopping_account_list_created', [
                'account' => $list->getAccount()->getId(),
                'id' => $list->getId(),
            ]);
        }

        return $this->render('shopping_list/new.html.twig', [
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{account}/list/{id}/created", name="shopping_account_list_created", methods="GET")
     */
    public function created(Account $account, ShoppingList $list): Response
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
     * @Route("/{account}/list/{id}/share", name="shopping_account_list_share", methods="GET|POST")
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
                $this->success('List {id} successfully shared with {email}', ['list' => $list->getName(), 'email' => $email]);

                return $this->redirectToRoute('shopping_account', ['account' => $list->getAccount()->getId()]);
            }
            $this->error('Error sharing list {id} with {email}', [
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
     * @Route("/list/{id}/items", name="shopping_list_items", methods="GET|POST")
     * @Route("/{account}/list/{id}/items", name="shopping_account_list_items", methods="GET|POST")
     */
    public function items(Request $request, ShoppingList $list, ShoppingListItemManager $itemManager, Account $account = null): Response
    {
        $item = new ShoppingListItem();
        $form = $this->createForm(ShoppingListCreateItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $itemManager->getItem($list, $item->getName());
            $item->setDoneAt(null);
            $list->addItem($item);
            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $this->success('Item %item% added', ['%item%' => $item->getName()])
                ->addFlashAction([
                    'url' => $this->generateUrl('shopping_list_item_edit', ['item' => $item->getId()]),
                    'message' => $this->translate('Edit item %item%', ['%item%' => (string) $item]),
                ]);

            return $this->goBack(
                $request,
                null !== $account
                    ? $this->redirectToRoute('shopping_account_list_items', ['account' => $account->getId(), 'id' => $list->getId()])
                    : $this->redirectToRoute('shopping_list_items', ['id' => $list->getId()])
            );
        }

        $filter = $request->get('filter');
        $order = $request->get('order');

        return $this->renderForm('shopping_list/items.html.twig', [
            'account' => $account,
            'list' => $list,
            'undone_items' => $this->listManager->applyFilter($list->getUndoneItems(), $filter, $order)->getValues(),
            'done_items' => $this->listManager->applyFilter($list->getDoneItems(), $filter, $order)->getValues(),
            'filter' => $filter,
            'add_item_form' => $form,
        ]);
    }

    /**
     * @Route("/list/{id}/manifest.json", name="shopping_list_manifest", methods="GET")
     */
    public function itemsManifest(Request $request, ShoppingList $list, Packages $packages, array $pwaConfig): JsonResponse
    {
        $icons = $pwaConfig['icons'];
        array_walk($icons, static function (&$value, $key) use ($packages) {
            $value = [
                'src' => $packages->getUrl($value),
                'sizes' => $key,
                'type' => 'image/png',
            ];
        });

        $manifest = [
            'short_name' => $list->getName(),
            'name' => $list->getName(),
            'icons' => array_values($icons),
            'start_url' => $this->generateUrl('shopping_list_items', [
                'id' => $list->getId(),
                // 'utm_source' => 'homescreen',
            ]),
            'display' => 'standalone',
            'orientation' => 'portrait',
            'background_color' => '#003764',
            'theme_color' => '#003764',
            'lang' => 'da',
        ];

        return new JsonResponse($manifest);
    }

    /**
     * @Route("/list/{id}/serviceWorker.js", name="shopping_list_serviceworker", methods="GET")
     */
    public function serviceWorker(Request $request, ShoppingList $list): Response
    {
        $content = $this->renderView('shopping_list/serviceWorker.js.twig', [
            'list' => $list,
        ]);

        return new Response($content, 200, ['content-type' => 'text/javascript']);
    }

    /**
     * @Route("/list/{id}/offline", name="shopping_list_offline", methods="GET")
     */
    public function offline(Request $request, ShoppingList $list): Response
    {
        return $this->render('shopping_list/offline.html.twig');
    }

    /**
     * @Route("/{account}/list/{id}/edit", name="shopping_account_list_edit", methods="GET|POST")
     */
    public function edit(Request $request, Account $account, ShoppingList $list): Response
    {
        $form = $this->createForm(ShoppingListType::class, $list);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('shopping_account_list_edit', ['id' => $list->getId()]);
        }

        return $this->render('shopping_list/edit.html.twig', [
            'account' => $account,
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{account}/list/{id}", name="shopping_account_list_delete", methods="DELETE")
     */
    public function delete(Request $request, ShoppingList $list): Response
    {
        if ($this->isCsrfTokenValid('delete'.$list->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($list);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('shopping_list_index');
    }

    /**
     * @Route("/list/{id}/log", name="shopping_list_log", methods="GET")
     * @Route("/{account}/list/{id}/log", name="shopping_account_list_log", methods="GET")
     */
    public function log(ShoppingList $list, Account $account = null): Response
    {
        return $this->render('shopping_list/log.html.twig', [
            'account' => $account,
            'list' => $list,
        ]);
    }

    /**
     * @Route("/list/{id}/item/add", name="shopping_list_add_item", methods="POST")
     * @Route("/{account}/list/{id}/item/add", name="shopping_account_list_add_item", methods="POST")
     */
    public function addItem(Request $request, ShoppingList $list, ShoppingListItemManager $itemManager, Account $account = null): RedirectResponse
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
            ? $this->generateUrl('shopping_account_list_add_item', ['account' => $account->getId(), 'id' => $list->getId()])
            : $this->generateUrl('shopping_list_add_item', ['id' => $list->getId()]));
    }

    /**
     * @Route("/list/{id}/items/add", name="shopping_list_add_items", methods="GET|POST")
     * @Route("/{account}/list/{id}/items/add", name="shopping_account_list_add_items", methods="GET|POST")
     *
     * @return RedirectResponse|Response
     */
    public function addItems(Request $request, ShoppingList $list, Account $account = null)
    {
        $form = $this->createForm(ShoppingListAddItemsType::class, null, [
            'list' => $list,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ShoppingListItem[] $items */
            $items = $form->get('items')->getData();

            foreach ($items as $item) {
                $item->setDoneAt(null);
                $list->addItem($item);
            }

            $existingItems = $items->filter(static function (ShoppingListItem $item) {
                return null !== $item->getId();
            });
            $newItems = $items->filter(static function (ShoppingListItem $item) {
                return null === $item->getId();
            });

            $this->entityManager->persist($list);
            $this->entityManager->flush();

            $this->info('Items added; %count_existing% existing; %count_new% new', [
                '%count_existing%' => \count($existingItems),
                '%count_new%' => \count($newItems),
            ]);

            if (null !== $account) {
                return $this->redirectToRoute('shopping_account_list_items', [
                    'account' => $account->getId(),
                    'id' => $list->getId(),
                ]);
            }

            return $this->redirectToRoute('shopping_list_items', ['id' => $list->getId()]);
        }

        return $this->render('shopping_list/add_items.html.twig', [
            'account' => $account,
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }
}
