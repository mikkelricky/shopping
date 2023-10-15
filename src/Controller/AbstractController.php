<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018– Mikkel Ricky
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

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(EntityManagerInterface $entityManager, private readonly FlashActionManager $flashActionManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param (scalar|null)[] $parameters
     */
    protected function error(string $message, array $parameters = []): self
    {
        return $this->danger($message, $parameters);
    }

    /**
     * @param (scalar|null)[] $parameters
     */
    protected function danger(string $message, array $parameters = []): self
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    /**
     * @param (int|string)[] $parameters
     */
    protected function info(string $message, array $parameters = []): self
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    /**
     * @param (string|null)[] $parameters
     */
    protected function success(string $message, array $parameters = []): self
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    protected function addFlash(string $type, mixed $message): void
    {
        [$this->lastFlashType, $this->lastFlashMessage] = [$type, $message];
        parent::addFlash($type, $message);
    }

    protected function addFlashAction(array $action = null): void
    {
        $this->flashActionManager->addFlashAction($action, $this->lastFlashType, $this->lastFlashMessage);
    }

    protected function translate(string $message, array $parameters = []): string
    {
        return $this->translator->trans($message, $parameters);
    }

    protected function goBack(Request $request, string|RedirectResponse $defaultUrl): RedirectResponse
    {
        $referer = $request->headers->get('referer');
        if (null !== $referer) {
            return $this->redirect($referer);
        }

        return new RedirectResponse($defaultUrl);
    }
}
