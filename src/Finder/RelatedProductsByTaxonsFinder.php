<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Finder;

use BitBag\SyliusCrossSellingPlugin\Exception\ProductNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

class RelatedProductsByTaxonsFinder extends AbstractRelatedProductsFinder implements RelatedProductsFinderInterface
{
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
        $product = $this->productRepository->findOneByChannelAndSlug($channel, $locale, $slug);
        if (null === $product) {
            throw new ProductNotFoundException($slug, $channel, $locale);
        }

        $taxons = $this->getTaxons($product);

        $result = [];
        $excludedProductIds[] = $product->getId();

        foreach ($taxons as $taxon) {
            $relatedByTaxon = $this->productRepository->findLatestByChannelAndTaxonCode(
                $channel,
                (string)$taxon->getCode(),
                $maxResults - count($result),
                $excludedProductIds
            );
            $result = array_merge($result, $relatedByTaxon);

            if (count($result) >= $maxResults) {
                return $result;
            }

            $excludedProductIds = array_merge($excludedProductIds, $this->getIds($relatedByTaxon));
        }

        return $result;
    }

    /**
     * @return TaxonInterface[]
     */
    protected function getTaxons(ProductInterface $product): array
    {
        $taxons = [];

        $mainTaxon = $product->getMainTaxon();
        if (null !== $mainTaxon) {
            $taxons[] = $mainTaxon;
        }

        foreach ($product->getTaxons() as $taxon) {
            if (null !== $mainTaxon && $mainTaxon->getId() === $taxon->getId()) {
                continue;
            }

            $taxons[] = $taxon;
        }

        return $taxons;
    }
}
