<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Query;

use BitBag\SyliusCrossSellingPlugin\Query\RelatedProductsByOrderHistoryQueryBuilder;
use BitBag\SyliusCrossSellingPlugin\Query\RelatedProductsByOrderHistoryQueryBuilderInterface;
use Elastica\Query;
use PhpSpec\ObjectBehavior;

final class RelatedProductsByOrderHistoryQueryBuilderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsByOrderHistoryQueryBuilder::class);
    }

    public function it_implements_event_subscriber_interface(): void
    {
        $this->shouldHaveType(RelatedProductsByOrderHistoryQueryBuilderInterface::class);
    }

    public function it_builds_query(): void
    {
        $this->buildQuery(123)->shouldReturnAnInstanceOf(Query::class);
    }
}
