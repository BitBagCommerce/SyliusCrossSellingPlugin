<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusUpsellingPlugin\Finder;

use BitBag\SyliusUpsellingPlugin\Finder\AbstractRelatedProductsFinder;
use BitBag\SyliusUpsellingPlugin\Finder\RelatedProductsCombinedFinder;
use BitBag\SyliusUpsellingPlugin\Finder\RelatedProductsFinderInterface;
use BitBag\SyliusUpsellingPlugin\Repository\ProductRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class RelatedProductsCombinedFinderSpec extends ObjectBehavior
{
    function let(
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

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsCombinedFinder::class);
    }

    function it_implements_related_products_finder_interface(): void
    {
        $this->shouldHaveType(RelatedProductsFinderInterface::class);
    }

    function it_extends_abstract_related_products_finder(): void
    {
        $this->shouldHaveType(AbstractRelatedProductsFinder::class);
    }

    function it_finds_related_in_current_channel_by_slug(
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

    function it_finds_related_by_channel_and_slug(
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
