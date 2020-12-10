<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\Finder;

use BitBag\SyliusUpsellingPlugin\Exception\ProductNotFoundException;
use BitBag\SyliusUpsellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use BitBag\SyliusUpsellingPlugin\Query\RelatedProductsByOrderHistoryQueryBuilderInterface;
use BitBag\SyliusUpsellingPlugin\Repository\ProductRepositoryInterface;
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
