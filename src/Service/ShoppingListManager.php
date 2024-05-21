<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2019 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Entity\ShoppingList;
use App\Entity\ShoppingListItem;
use App\Repository\AccountRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ShoppingListManager
{
    /** @var MailerInterface */
    private $mailer;

    /** @var Environment */
    private $twig;

    /** @var AccountManager */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository, MailerInterface $mailer, Environment $twig)
    {
        $this->accountRepository = $accountRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function notifyListCreated(ShoppingList $list)
    {
        $message = (new Email())
            ->subject('Shopping list created')
            ->text($this->twig->render('shopping_list/email/list_created.txt.twig', [
                'list' => $list,
            ]));

        $this->send($message, $list->getAccount()->getEmail());
    }

    public function recoverLists(string $email)
    {
        $account = $this->accountRepository->findOneByEmail($email);
        if (null !== $account) {
            $message = (new Email())
                ->subject('Shopping lists')
                ->text($this->twig->render('shopping_list/email/recover.txt.twig', [
                    'account' => $account,
                ]));

            $this->send($message, $email);
        }

        return true;
    }

    public function shareList(ShoppingList $list, string $email, array $data = [])
    {
        $message = (new Email())
            ->subject('Shared shopping list')
            ->text($this->twig->render('shopping_list/email/share.txt.twig', [
                'list' => $list,
                'data' => $data,
            ]));

        return true;
    }

    public function applyFilter(Collection $items, array $filter = null)
    {
        return $items->filter(function (ShoppingListItem $item) use ($filter) {
            if (null === $filter) {
                return true;
            }

            if (isset($filter['store'])) {
                if (!\is_array($filter['store'])) {
                    $filter['store'] = [$filter['store']];
                }

                return null !== $item->getStore() && \in_array($item->getStore()->getId(), $filter['store'], true);
            }

            return false;
        });
    }

    private function send(Email $email, $addresses)
    {
        $email
            ->from('shopping@example.com')
            ->to($addresses);

        return $this->mailer->send($email);
    }
}
