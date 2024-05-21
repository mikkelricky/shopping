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
use App\Entity\Store;
use App\Form\Type\StoreType;
use App\Repository\StoreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     "/{_locale}/store/{account}",
 *     name="store_",
 *     locale="en",
 *     requirements={
 *         "_locale": "da|en"
 *     }
 * )
 */
class StoreController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index(StoreRepository $storeRepository, Account $account): Response
    {
        return $this->render('store/index.html.twig', [
            'stores' => $storeRepository->findBy([], ['name' => 'ASC']),
            'account' => $account,
        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     */
    public function new(Request $request, Account $account): Response
    {
        $store = new Store();
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($store);
            $em->flush();

            return $this->redirectToRoute('store_index', ['account' => $account->getId()]);
        }

        return $this->render('store/new.html.twig', [
            'store' => $store,
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods="GET")
     */
    public function show(Store $store): Response
    {
        return $this->render('store/show.html.twig', ['store' => $store]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods="GET|POST")
     */
    public function edit(Request $request, Store $store, Account $account): Response
    {
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->success($this->translator->trans('Store {name} updated', ['name' => $store->getName()]));

            return $this->redirectToRoute('store_index', ['account' => $account->getId()]);
        }

        return $this->render('store/edit.html.twig', [
            'store' => $store,
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
