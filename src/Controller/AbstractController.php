<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Controller;

use App\Service\FlashActionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractController extends BaseController
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var string */
    private $lastFlashType;

    /** @var string */
    private $lastFlashMessage;

    /** @var FlashActionManager */
    private $flashActionManager;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, FlashActionManager $flashActionManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->flashActionManager = $flashActionManager;
        $this->translator = $translator;
    }

    protected function error($message, $parameters = [])
    {
        return $this->danger($message, $parameters);
    }

    protected function danger($message, $parameters = [])
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    protected function info($message, $parameters = [])
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    protected function success($message, $parameters = [])
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    protected function addFlash(string $type, string $message): void
    {
        [$this->lastFlashType, $this->lastFlashMessage] = [$type, $message];
        parent::addFlash($type, $message);
    }

    protected function addFlashAction(array $action = null)
    {
        $this->flashActionManager->addFlashAction($action, $this->lastFlashType, $this->lastFlashMessage);
    }

    protected function translate($message, array $parameters = [])
    {
        return $this->translator->trans($message, $parameters);
    }

    protected function goBack(Request $request, $route, $parameters = [])
    {
        $referer = $request->headers->get('referer');
        if (null !== $referer) {
            return $this->redirect($referer);
        }

        if (null !== $this->container->get('router')->getRouteCollection()->get($route)) {
            return $this->redirectToRoute($route, $parameters);
        }

        return new RedirectResponse($route);
    }
}
