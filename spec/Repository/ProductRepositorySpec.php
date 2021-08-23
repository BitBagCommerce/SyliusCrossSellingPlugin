<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Repository;

use BitBag\SyliusCrossSellingPlugin\Repository\ProductRepository;
use BitBag\SyliusCrossSellingPlugin\Repository\ProductRepositoryInterface;
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
