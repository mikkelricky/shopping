<?php

/*
 * This file is part of Shopping.
 *
 * (c) 2018–2020 Mikkel Ricky
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Entity\Location;
use App\Entity\Store;
use App\Service\StoreFetcher\StoreFetcherInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StoreManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use LoggerTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var array */
    private $options;

    public function __construct(EntityManagerInterface $entityManager, array $storeManagerConfig)
    {
        $this->entityManager = $entityManager;
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($storeManagerConfig);
    }

    public function updateStores(): void
    {
        $storeRepository = $this->entityManager->getRepository(Store::class);
        /** @var StoreFetcherInterface $service */
        foreach ($this->options['store_fetchers'] as $service) {
            $this->info(sprintf('Service: %s', \get_class($service)));
            try {
                $stores = $service->getStores();
                foreach ($stores as $name => $locations) {
                    $this->info(sprintf('name: %s', $name));
                    $store = $storeRepository->findOneBy(['name' => $name]);
                    if (null === $store) {
                        $store = (new Store())
                            ->setName($name);
                    }
                    $storeLocations = new ArrayCollection($store->getLocations()->toArray());
                    foreach ($storeLocations as $location) {
                        $store->getLocations()->removeElement($location);
                    }
                    foreach ($locations as $location) {
                        $this->info(sprintf('location: %s', json_encode($location)));
                        $location = (new Location())
                            ->setName($location['name'])
                            ->setAddress($location['address'])
                            ->setLatitude($location['latitude'])
                            ->setLongitude($location['longitude']);
                        $existingLocations = $storeLocations->filter(static function (Location $l) use ($location, $store) {
                            return $l->getName() === $location->getName()
                                && $l->getAddress() === $location->getAddress()
                                && $l->getLatitude() === $location->getLatitude()
                                && $l->getLongitude() === $location->getLongitude();
                        });
                        // Keep existing location or add new.
                        $store->addLocation($existingLocations->first() ? $existingLocations->first() : $location);
                    }
                    $this->entityManager->persist($store);
                }
                $this->entityManager->flush();
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'store_fetchers',
            ])
            ->setAllowedValues('store_fetchers', static function ($services) {
                if (!\is_array($services)) {
                    return false;
                }

                foreach ($services as $service) {
                    if (!$service instanceof StoreFetcherInterface) {
                        return false;
                    }
                }

                return true;
            });
    }

    public function log($level, $message, array $context = [])
    {
        if (null !== $this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
