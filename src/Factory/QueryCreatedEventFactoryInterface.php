<?php

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Factory;

use BitBag\SyliusCrossSellingPlugin\Events\QueryCreatedEventInterface;
use Elastica\Query\AbstractQuery;

interface QueryCreatedEventFactoryInterface
{
    public function createNewEvent(AbstractQuery $boolQuery): QueryCreatedEventInterface;
}
