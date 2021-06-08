<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Query;

use BitBag\SyliusCrossSellingPlugin\Query\RelatedProductsByOrderHistoryQueryBuilder;
use BitBag\SyliusCrossSellingPlugin\Query\RelatedProductsByOrderHistoryQueryBuilderInterface;
use Elastica\Query;
use PhpSpec\ObjectBehavior;

final class RelatedProductsByOrderHistoryQueryBuilderSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsByOrderHistoryQueryBuilder::class);
    }

    function it_implements_event_subscriber_interface(): void
    {
        $this->shouldHaveType(RelatedProductsByOrderHistoryQueryBuilderInterface::class);
    }

    function it_builds_query(): void
    {
        $this->buildQuery(123)->shouldReturnAnInstanceOf(Query::class);
    }
}
