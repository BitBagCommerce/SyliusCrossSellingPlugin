<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Query;

use BitBag\SyliusCrossSellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;

final class RelatedProductsByOrderHistoryQueryBuilder implements RelatedProductsByOrderHistoryQueryBuilderInterface
{
    private const MAX_AGGREGATION_SIZE = 50;

    public function buildQuery(int $productId): Query
    {
        $boolQuery = new BoolQuery();
        $boolQuery->addMust((new Term())->setTerm(
            RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS,
            (string) $productId,
        ));

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
