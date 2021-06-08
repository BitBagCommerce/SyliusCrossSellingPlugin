<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
