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

    /** @var FlashActionManager */
    private $flashActionManager;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(EntityManagerInterface $entityManager, FlashActionManager $flashActionManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->flashActionManager = $flashActionManager;
        $this->translator = $translator;
    }

    /**
     * @param (null|scalar)[] $parameters
     *
     * @psalm-param array{list?: null|string, email?: string, '%item%'?: null|scalar} $parameters
     */
    protected function error(string $message, array $parameters = []): self
    {
        return $this->danger($message, $parameters);
    }

    /**
     * @param (null|scalar)[] $parameters
     *
     * @psalm-param array{list?: null|string, email?: string, '%item%'?: null|scalar} $parameters
     */
    protected function danger(string $message, array $parameters = []): self
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    /**
     * @param (int|string)[] $parameters
     *
     * @psalm-param 'Item %item% updated'|'Items added; %count_existing% existing; %count_new% new' $message
     * @psalm-param array{'%count_existing%'?: int, '%count_new%'?: int, '%item%'?: string} $parameters
     */
    protected function info(string $message, array $parameters = []): self
    {
        $this->addFlash(__FUNCTION__, $this->translate($message, $parameters));

        return $this;
    }

    /**
     * @param (null|string)[] $parameters
     *
     * @psalm-param array{list?: null|string, email?: string, '%item%'?: null|string} $parameters
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

    /**
     * @psalm-param 'Edit item %item%'|'Undo item %item% marked as done' $message
     */
    protected function translate(string $message, array $parameters = []): string
    {
        return $this->translator->trans($message, $parameters);
    }

    /**
     * @param RedirectResponse|string $defaultUrl
     */
    protected function goBack(Request $request, string|RedirectResponse $defaultUrl): RedirectResponse
    {
        $referer = $request->headers->get('referer');
        if (null !== $referer) {
            return $this->redirect($referer);
        }

        return new RedirectResponse($defaultUrl);
    }
}
