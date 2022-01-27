<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Finder;

use BitBag\SyliusCrossSellingPlugin\Finder\AbstractRelatedProductsFinder;
use BitBag\SyliusCrossSellingPlugin\Finder\RelatedProductsCombinedFinder;
use BitBag\SyliusCrossSellingPlugin\Finder\RelatedProductsFinderInterface;
use BitBag\SyliusCrossSellingPlugin\Repository\ProductRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class RelatedProductsCombinedFinderSpec extends ObjectBehavior
{
    public function let(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository,
        RelatedProductsFinderInterface $relatedProductsByOrderHistoryFinder,
        RelatedProductsFinderInterface $relatedProductsByTaxonsFinder
    ): void {
        $this->beConstructedWith(
            $channelContext,
            $localeContext,
            $productRepository,
            $relatedProductsByOrderHistoryFinder,
            $relatedProductsByTaxonsFinder
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsCombinedFinder::class);
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
        RelatedProductsFinderInterface $relatedProductsByOrderHistoryFinder,
        RelatedProductsFinderInterface $relatedProductsByTaxonsFinder,
        ProductInterface $productByOrderHistory,
        ProductInterface $productByTaxon
    ): void {
        $locale = 'en_US';
        $slug = 'test-123';
        $maxResults = 4;

        $relatedProductId = 456;

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn($locale);

        $productByOrderHistory->getId()->willReturn(456);

        $relatedProductsByOrderHistoryFinder
            ->findRelatedByChannelAndSlug($channel, $locale, $slug, $maxResults, [])
            ->willReturn([$productByOrderHistory]);

        $relatedProductsByTaxonsFinder
            ->findRelatedByChannelAndSlug($channel, $locale, $slug, $maxResults - 1, [$relatedProductId])
            ->willReturn([$productByTaxon]);

        $this->findRelatedInCurrentChannelBySlug($slug, $maxResults, [])
            ->shouldReturn([
                $productByOrderHistory,
                $productByTaxon,
            ]);
    }

    public function it_finds_related_by_channel_and_slug(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleContextInterface $localeContext,
        RelatedProductsFinderInterface $relatedProductsByOrderHistoryFinder,
        RelatedProductsFinderInterface $relatedProductsByTaxonsFinder,
        ProductInterface $productByOrderHistory,
        ProductInterface $productByTaxon
    ): void {
        $locale = 'en_US';
        $slug = 'test-123';
        $maxResults = 4;

        $relatedProductId = 456;

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn($locale);

        $productByOrderHistory->getId()->willReturn(456);

        $relatedProductsByOrderHistoryFinder
            ->findRelatedByChannelAndSlug($channel, $locale, $slug, $maxResults, [])
            ->willReturn([$productByOrderHistory]);

        $relatedProductsByTaxonsFinder
            ->findRelatedByChannelAndSlug($channel, $locale, $slug, $maxResults - 1, [$relatedProductId])
            ->willReturn([$productByTaxon]);

        $this->findRelatedByChannelAndSlug($channel, $locale, $slug, $maxResults, [])
            ->shouldReturn([
                $productByOrderHistory,
                $productByTaxon,
            ]);
    }
}
