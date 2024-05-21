<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Entity\ShoppingList;
use App\Entity\ShoppingListItem;
use App\Entity\Store;
use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Environment;

class ShoppingListManager
{
    /** @var AccountManager */
    private $accountRepository;

    /** @var MailerInterface */
    private $mailer;

    /** @var array */
    private $from;

    /** @var Environment */
    private $twig;

    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(AccountRepository $accountRepository, MailerInterface $mailer, array $from, Environment $twig, PropertyAccessorInterface $propertyAccessor)
    {
        $this->accountRepository = $accountRepository;
        $this->mailer = $mailer;
        $this->from = $from;
        $this->twig = $twig;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function notifyListCreated(ShoppingList $list): void
    {
        $message = (new Email())
            ->subject('Shopping list created')
            ->text($this->twig->render('shopping_list/email/list_created.txt.twig', [
                'list' => $list,
            ]));

        $this->send($message, $list->getAccount()->getEmail());
    }

    public function recoverLists(string $email): bool
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

    public function shareList(ShoppingList $list, string $email, array $data = []): bool
    {
        $message = (new Email())
            ->subject('Shared shopping list')
            ->text($this->twig->render('shopping_list/email/share.txt.twig', [
                'list' => $list,
                'data' => $data,
            ]));

        $this->send($message, $email);

        return true;
    }

    public function applyFilter(Collection $items, array $filter = null, array $orderBy = null): Collection
    {
        if (null !== $filter) {
            $items = $items->filter(static function (ShoppingListItem $item) use ($filter) {
                if (isset($filter['store'])) {
                    $filter['store'] = (array) $filter['store'];

                    return !empty(array_intersect(
                        $item->getStores()->map(static function (Store $store) {
                            return $store->getName();
                        })->toArray(),
                        $filter['store']
                    ));
                }

                return false;
            });
        }

        if (null !== $orderBy) {
            $sorted = $items->toArray();
            usort($sorted, function (ShoppingListItem $a, ShoppingListItem $b) use ($orderBy) {
                // @TODO Handle more than one sort field.
                foreach ($orderBy as $property => $direction) {
                    $valueA = $this->propertyAccessor->getValue($a, $property);
                    $valueB = $this->propertyAccessor->getValue($b, $property);
                    $value = \is_string($valueA) && \is_string($valueB) ?
                        strcasecmp($valueA, $valueB) : $valueA <=> $valueB;

                    return 0 === strcasecmp('asc', $direction) ? $value : -$value;
                }
            });

            $items = new ArrayCollection($sorted);
        }

        return $items;
    }

    private function send(Email $email, $addresses): void
    {
        $from = new Address($this->from['address'], $this->from['name'] ?? '');
        $email
            ->from($from)
            ->to($addresses);

        $this->mailer->send($email);
    }
}
