<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Finder;

use BitBag\SyliusCrossSellingPlugin\Finder\AbstractRelatedProductsFinder;
use BitBag\SyliusCrossSellingPlugin\Finder\RelatedProductsByTaxonsFinder;
use BitBag\SyliusCrossSellingPlugin\Finder\RelatedProductsFinderInterface;
use BitBag\SyliusCrossSellingPlugin\Repository\ProductRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class RelatedProductsByTaxonsFinderSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository
    ): void {
        $this->beConstructedWith(
            $channelContext,
            $localeContext,
            $productRepository
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsByTaxonsFinder::class);
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
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ArrayCollection $taxons,
        TaxonInterface $taxon,
        ProductInterface $relatedProduct
    ): void {
        $locale = 'en_US';
        $slug = 'test-123';
        $maxResults = 4;
        $taxonCode = 'taxon-A';

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn($locale);

        $productRepository->findOneByChannelAndSlug($channel, $locale, $slug)
            ->willReturn($product);
        $productRepository->findLatestByChannelAndTaxonCode($channel, $taxonCode, $maxResults, [123])
            ->willReturn([$relatedProduct]);

        $product->getId()->willReturn(123);
        $product->getTaxons()->willReturn($taxons);
        $product->getMainTaxon()->willReturn($taxon);

        $taxons->getIterator()->willYield([$taxon->getWrappedObject()]);

        $taxon->getId()->willReturn(77);
        $taxon->getCode()->willReturn($taxonCode);

        $relatedProduct->getId()->willReturn(456);

        $this->findRelatedInCurrentChannelBySlug('test-123', $maxResults, [])
            ->shouldReturn([$relatedProduct]);
    }

    function it_finds_related_by_channel_and_slug(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ArrayCollection $taxons,
        TaxonInterface $taxon,
        ProductInterface $relatedProduct
    ): void {
        $locale = 'en_US';
        $slug = 'test-123';
        $maxResults = 4;
        $taxonCode = 'taxon-A';

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn($locale);

        $productRepository->findOneByChannelAndSlug($channel, $locale, $slug)
            ->willReturn($product);
        $productRepository->findLatestByChannelAndTaxonCode($channel, $taxonCode, $maxResults, [123])
            ->willReturn([$relatedProduct]);

        $product->getId()->willReturn(123);
        $product->getTaxons()->willReturn($taxons);
        $product->getMainTaxon()->willReturn($taxon);

        $taxons->getIterator()->willYield([$taxon->getWrappedObject()]);

        $taxon->getId()->willReturn(77);
        $taxon->getCode()->willReturn($taxonCode);

        $relatedProduct->getId()->willReturn(456);

        $this->findRelatedByChannelAndSlug($channel, $locale, $slug, $maxResults, [])
            ->shouldReturn([$relatedProduct]);
    }
}
