<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Query;

use BitBag\SyliusCrossSellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use BitBag\SyliusCrossSellingPlugin\Notifier\QueryDispatcherInterface;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;

final class RelatedProductsByOrderHistoryQueryBuilder implements RelatedProductsByOrderHistoryQueryBuilderInterface
{
    private const MAX_AGGREGATION_SIZE = 50;

    /** @var QueryDispatcherInterface $queryDispatcher */
    private $queryDispatcher;

    public function __construct(QueryDispatcherInterface $queryDispatcher)
    {
        $this->queryDispatcher = $queryDispatcher;
    }

    public function buildQuery(int $productId): Query
    {
        $boolQuery = new BoolQuery();
        $boolQuery->addMust((new Term())->setTerm(
            RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS,
            (string) $productId
        ));

        $this->queryDispatcher->dispatchNewQuery($boolQuery);

        $query = new Query($boolQuery);
        $query->addAggregation($this->getProductIdsAggregation());

        return $query;
    }

    private function getProductIdsAggregation(): Terms
    {
        $attributesAggregation = new Terms(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS);
        $attributesAggregation->setField(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS);
        $attributesAggregation->setSize(self::MAX_AGGREGATION_SIZE);

        return $attributesAggregation;
    }
}
