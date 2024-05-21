<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="frontpage", methods="GET")
     */
    public function index()
    {
        return $this->redirectToRoute('shopping_list_index');
    }
}
