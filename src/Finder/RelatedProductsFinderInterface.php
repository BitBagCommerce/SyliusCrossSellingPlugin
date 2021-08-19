<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Finder;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface RelatedProductsFinderInterface
{
    /**
     * @param int[] $excludedProductIds
     * @return ProductInterface[]
     */
    public function findRelatedInCurrentChannelBySlug(
        string $slug,
        int $maxResults,
        array $excludedProductIds = []
    ): array;

    /**
     * @param int[] $excludedProductIds
     * @return ProductInterface[]
     */
    public function findRelatedByChannelAndSlug(
        ChannelInterface $channel,
        string $locale,
        string $slug,
        int $maxResults,
        array $excludedProductIds = []
    ): array;
}
