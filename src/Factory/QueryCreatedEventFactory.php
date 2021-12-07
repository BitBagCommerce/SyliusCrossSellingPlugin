<?php
/*

This file was created by developers working at BitBag

Do you need more information about us and what we do? Visit our   website!

We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/
declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Factory;

use BitBag\SyliusCrossSellingPlugin\Events\QueryCreatedEvent;
use BitBag\SyliusCrossSellingPlugin\Events\QueryCreatedEventInterface;
use Elastica\Query\AbstractQuery;

final class QueryCreatedEventFactory implements QueryCreatedEventFactoryInterface
{
    public function createNewEvent(AbstractQuery $boolQuery): QueryCreatedEventInterface
    {
        return new QueryCreatedEvent($boolQuery);
    }
}
