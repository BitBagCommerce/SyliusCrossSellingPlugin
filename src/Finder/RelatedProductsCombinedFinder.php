<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
        RelatedProductsFinderInterface $relatedProductsByTaxonsFinder
    ) {
        parent::__construct($channelContext, $localeContext, $productRepository);
        $this->relatedProductsByOrderHistoryFinder = $relatedProductsByOrderHistoryFinder;
        $this->relatedProductsByTaxonsFinder = $relatedProductsByTaxonsFinder;
    }

    /**
     * {@inheritDoc}
     */
    public function findRelatedByChannelAndSlug(
        ChannelInterface $channel,
        string $locale,
        string $slug,
        int $maxResults,
        array $excludedProductIds = []
    ): array {
        $relatedProducts = $this->relatedProductsByOrderHistoryFinder->findRelatedByChannelAndSlug(
            $channel,
            $locale,
            $slug,
            $maxResults,
            $excludedProductIds = []
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
                array_merge($excludedProductIds, $this->getIds($relatedProducts))
            )
        );

        return $relatedProducts;
    }
}
