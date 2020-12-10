<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusUpsellingPlugin\Repository;

use BitBag\SyliusUpsellingPlugin\Repository\ProductRepository;
use BitBag\SyliusUpsellingPlugin\Repository\ProductRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
use PhpSpec\ObjectBehavior;

final class ProductRepositorySpec extends ObjectBehavior
{
    function let(EntityManager $entityManager, Mapping\ClassMetadata $class): void
    {
        $this->beConstructedWith($entityManager, $class);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductRepository::class);
    }

    function it_implements_event_subscriber_interface(): void
    {
        $this->shouldHaveType(ProductRepositoryInterface::class);
    }
}
