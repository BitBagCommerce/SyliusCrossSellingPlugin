<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Finder;

use BitBag\SyliusCrossSellingPlugin\Repository\ProductRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

class RelatedProductsCombinedFinder extends AbstractRelatedProductsFinder implements RelatedProductsFinderInterface
{
    /** @var RelatedProductsFinderInterface */
    private $relatedProductsByOrderHistoryFinder;

    /** @var RelatedProductsFinderInterface */
    private $relatedProductsByTaxonsFinder;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository,
        RelatedProductsFinderInterface $relatedProductsByOrderHistoryFinder,
        RelatedProductsFinderInterface $relatedProductsByTaxonsFinder,
    ) {
        parent::__construct($channelContext, $localeContext, $productRepository);
        $this->relatedProductsByOrderHistoryFinder = $relatedProductsByOrderHistoryFinder;
        $this->relatedProductsByTaxonsFinder = $relatedProductsByTaxonsFinder;
    }

    /**
     * @inheritDoc
     */
    public function findRelatedByChannelAndSlug(
        ChannelInterface $channel,
        string $locale,
        string $slug,
        int $maxResults,
        array $excludedProductIds = [],
    ): array {
        $relatedProducts = $this->relatedProductsByOrderHistoryFinder->findRelatedByChannelAndSlug(
            $channel,
            $locale,
            $slug,
            $maxResults,
            $excludedProductIds,
        );

        if (count($relatedProducts) >= $maxResults) {
            return $relatedProducts;
        }

        $relatedProducts = array_merge(
            $relatedProducts,
            $this->relatedProductsByTaxonsFinder->findRelatedByChannelAndSlug(
                $channel,
                $locale,
                $slug,
                $maxResults - count($relatedProducts),
                array_merge($excludedProductIds, $this->getIds($relatedProducts)),
            ),
        );

        return $relatedProducts;
    }
}
