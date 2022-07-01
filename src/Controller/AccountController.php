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
use App\Form\Type\AccountType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     "/{_locale}/_account/{account}",
 *     name="account_",
 *     locale="en",
 *     requirements={
 *         "_locale": "da|en"
 *     }
 * )
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="show")
     */
    public function show(Account $account): Response
    {
        return $this->render('account/show.html.twig', [
            'account' => $account,
        ]);
    }

    /**
     * @Route ("/edit", name="edit")
     */
    public function edit(Request $request, Account $account): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('account_show', ['account' => $account->getId()]);
        }

        return $this->render('account/edit.html.twig', [
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }
}
