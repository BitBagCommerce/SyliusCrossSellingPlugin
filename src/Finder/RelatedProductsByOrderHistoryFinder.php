<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Finder;

use BitBag\SyliusCrossSellingPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusCrossSellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use BitBag\SyliusCrossSellingPlugin\Query\RelatedProductsByOrderHistoryQueryBuilderInterface;
use BitBag\SyliusCrossSellingPlugin\Repository\ProductRepositoryInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

class RelatedProductsByOrderHistoryFinder extends AbstractRelatedProductsFinder implements RelatedProductsFinderInterface
{
    /** @var RelatedProductsByOrderHistoryQueryBuilderInterface */
    private $queryBuilder;

    /** @var PaginatedFinderInterface */
    private $relatedProductsFinder;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository,
        RelatedProductsByOrderHistoryQueryBuilderInterface $relatedProductsQueryBuilder,
        PaginatedFinderInterface $relatedProductsFinder
    ) {
        parent::__construct($channelContext, $localeContext, $productRepository);
        $this->queryBuilder = $relatedProductsQueryBuilder;
        $this->relatedProductsFinder = $relatedProductsFinder;
    }

    public function findRelatedByChannelAndSlug(
        ChannelInterface $channel,
        string $locale,
        string $slug,
        int $maxResults,
        array $excludedProductIds = []
    ): array {
        $product = $this->productRepository->findOneByChannelAndSlug($channel, $locale, $slug);
        if (null === $product) {
            throw new ProductNotFoundException($slug, $channel, $locale);
        }

        return $this->getRelatedByOrderHistory($product->getId(), $channel, $maxResults);
    }

    /**
     * @return ProductInterface[]
     */
    protected function getRelatedByOrderHistory(
        int $productId,
        ChannelInterface $channel,
        int $maxResults
    ): array {
        $products = $this->relatedProductsFinder->findPaginated(
            $this->queryBuilder->buildQuery($productId)
        );

        return $this->productRepository->findManyByChannelAndIds(
            $channel,
            $this->extractProductIds($products, $productId),
            $maxResults
        );
    }

    /**
     * @return int[]
     */
    protected function extractProductIds(Pagerfanta $result, int $excludeId): array
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
