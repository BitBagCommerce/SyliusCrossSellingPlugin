<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\Finder;

use BitBag\SyliusUpsellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use BitBag\SyliusUpsellingPlugin\Repository\ProductRepositoryInterface;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class RelatedProductsFinder implements RelatedProductsFinderInterface
{
    private const MAX_AGGREGATION_SIZE = 50;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleContextInterface */
    private $localeContext;

    /** @var PaginatedFinderInterface */
    private $relatedProductsFinder;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        PaginatedFinderInterface $relatedProductsFinder,
        ProductRepositoryInterface $productRepository
    ) {
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->relatedProductsFinder = $relatedProductsFinder;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function findRelatedInCurrentChannelBySlug(string $slug, int $count): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->findRelatedByChannelAndSlug(
            $channel,
            $this->localeContext->getLocaleCode(),
            $slug,
            $count
        );
    }

    /**
     * {@inheritDoc}
     */
    public function findRelatedByChannelAndSlug(
        ChannelInterface $channel,
        string $locale,
        string $slug,
        int $count
    ): array {
        $product = $this->productRepository->findOneByChannelAndSlug($channel, $locale, $slug);
        if (null === $product) {
            return [];
        }

        $relatedProductIds = $this->getRelatedProductIds($product->getId());

        return $this->productRepository->findManyByChannelAndIds(
            $channel,
            $relatedProductIds,
            $count
        );
    }

    /**
     * @return int[]
     */
    private function getRelatedProductIds(int $productId): array
    {
        $boolQuery = new BoolQuery();
        $boolQuery->addMust((new Term())->setTerm(
            RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS,
            (string) $productId
        ));

        $query = new Query($boolQuery);
        $query->addAggregation($this->getProductIdsAggregation());

        $products = $this->relatedProductsFinder->findPaginated($query);

        return $this->extractProductIds($products, $productId);
    }

    private function getProductIdsAggregation(): Terms
    {
        $attributesAggregation = new Terms(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS);
        $attributesAggregation->setField(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS);
        $attributesAggregation->setSize(self::MAX_AGGREGATION_SIZE);

        return $attributesAggregation;
    }

    /**
     * @return int[]
     */
    private function extractProductIds(Pagerfanta $result, int $excludeId): array
    {
        $result->setMaxPerPage(1);
        $result->setCurrentPage(1);

        $adapter = $result->getAdapter();
        if (!$adapter instanceof FantaPaginatorAdapter) {
            return [];
        }

        $aggregation = $adapter->getAggregations()[RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS];

        $productIds = [];
        foreach ($aggregation['buckets'] as $bucket) {
            $id = $bucket['key'];
            if ($id === $excludeId) {
                continue;
            }

            $productIds[] = $id;
        }

        return $productIds;
    }
}
