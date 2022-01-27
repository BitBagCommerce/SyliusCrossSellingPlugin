<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Finder;

use BitBag\SyliusCrossSellingPlugin\Finder\AbstractRelatedProductsFinder;
use BitBag\SyliusCrossSellingPlugin\Finder\RelatedProductsByOrderHistoryFinder;
use BitBag\SyliusCrossSellingPlugin\Finder\RelatedProductsFinderInterface;
use BitBag\SyliusCrossSellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use BitBag\SyliusCrossSellingPlugin\Query\RelatedProductsByOrderHistoryQueryBuilderInterface;
use BitBag\SyliusCrossSellingPlugin\Repository\ProductRepositoryInterface;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class RelatedProductsByOrderHistoryFinderSpec extends ObjectBehavior
{
    public function let(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository,
        RelatedProductsByOrderHistoryQueryBuilderInterface $relatedProductsQueryBuilder,
        PaginatedFinderInterface $relatedProductsFinder
    ): void {
        $this->beConstructedWith(
            $channelContext,
            $localeContext,
            $productRepository,
            $relatedProductsQueryBuilder,
            $relatedProductsFinder
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsByOrderHistoryFinder::class);
    }

    public function it_implements_related_products_finder_interface(): void
    {
        $this->shouldHaveType(RelatedProductsFinderInterface::class);
    }

    public function it_extends_abstract_related_products_finder(): void
    {
        $this->shouldHaveType(AbstractRelatedProductsFinder::class);
    }

    public function it_finds_related_in_current_channel_by_slug(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ProductInterface $relatedProduct,
        RelatedProductsByOrderHistoryQueryBuilderInterface $relatedProductsQueryBuilder,
        Query $query,
        PaginatedFinderInterface $relatedProductsFinder,
        Pagerfanta $pagerfanta,
        FantaPaginatorAdapter $adapter
    ): void {
        $locale = 'en_US';
        $slug = 'test-123';
        $maxResults = 4;

        $aggregations = [
            RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS => [
                'buckets' => [
                    ['key' => 123],
                    ['key' => 456],
                    ['key' => 789],
                ],
            ],
        ];

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn($locale);

        $productRepository->findOneByChannelAndSlug($channel, $locale, $slug)
            ->willReturn($product);
        $productRepository->findManyByChannelAndIds($channel, [456, 789], $maxResults)
            ->willReturn([$relatedProduct]);

        $product->getId()->willReturn(123);

        $relatedProductsQueryBuilder->buildQuery(123)->willReturn($query);
        $relatedProductsFinder->findPaginated($query)->willReturn($pagerfanta);

        $pagerfanta->getAdapter()->willReturn($adapter);
        $adapter->getAggregations()->willReturn($aggregations);

        $pagerfanta->setMaxPerPage(1)->shouldBeCalled();
        $pagerfanta->setCurrentPage(1)->shouldBeCalled();

        $this->findRelatedInCurrentChannelBySlug($slug, $maxResults, [])
            ->shouldReturn([$relatedProduct]);
    }

    public function it_finds_related_by_channel_and_slug(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ProductInterface $relatedProduct,
        RelatedProductsByOrderHistoryQueryBuilderInterface $relatedProductsQueryBuilder,
        Query $query,
        PaginatedFinderInterface $relatedProductsFinder,
        Pagerfanta $pagerfanta,
        FantaPaginatorAdapter $adapter
    ): void {
        $locale = 'en_US';
        $slug = 'test-123';
        $maxResults = 4;

        $aggregations = [
            RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS => [
                'buckets' => [
                    ['key' => 123],
                    ['key' => 456],
                    ['key' => 789],
                ],
            ],
        ];

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn($locale);

        $productRepository->findOneByChannelAndSlug($channel, $locale, $slug)
            ->willReturn($product);
        $productRepository->findManyByChannelAndIds($channel, [456, 789], $maxResults)
            ->willReturn([$relatedProduct]);

        $product->getId()->willReturn(123);

        $relatedProductsQueryBuilder->buildQuery(123)->willReturn($query);
        $relatedProductsFinder->findPaginated($query)->willReturn($pagerfanta);

        $pagerfanta->getAdapter()->willReturn($adapter);
        $adapter->getAggregations()->willReturn($aggregations);

        $pagerfanta->setMaxPerPage(1)->shouldBeCalled();
        $pagerfanta->setCurrentPage(1)->shouldBeCalled();

        $this->findRelatedByChannelAndSlug($channel, $locale, $slug, $maxResults, [])
            ->shouldReturn([$relatedProduct]);
    }
}
