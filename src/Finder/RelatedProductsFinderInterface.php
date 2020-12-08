<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\Finder;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface RelatedProductsFinderInterface
{
    /**
     * @return ProductInterface[]
     */
    public function findRelatedInCurrentChannelBySlug(string $slug, int $count): array;

    /**
     * @return ProductInterface[]
     */
    public function findRelatedByChannelAndSlug(
        ChannelInterface $channel,
        string $locale,
        string $slug,
        int $count
    ): array;
}
