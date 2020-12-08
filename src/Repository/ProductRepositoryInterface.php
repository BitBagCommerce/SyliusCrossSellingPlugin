<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\Repository;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface as CoreProductRepositoryInterface;

interface ProductRepositoryInterface extends CoreProductRepositoryInterface
{
    /**
     * Returns at most $maxResults entities, in order matching the $productIds array.
     *
     * @param int[] $productIds
     * @return ProductInterface[]
     */
    public function findManyByChannelAndIds(
        ChannelInterface $channel,
        array $productIds,
        int $maxResults
    ): array;

    public function findOneByChannelAndId(ChannelInterface $channel, int $id): ?ProductInterface;

    /**
     * @param int[] $excludedProductIds
     * @return ProductInterface[]
     */
    public function findLatestByChannelAndTaxonCode(
        ChannelInterface $channel,
        string $code,
        int $count,
        array $excludedProductIds = []
    ): array;
}
