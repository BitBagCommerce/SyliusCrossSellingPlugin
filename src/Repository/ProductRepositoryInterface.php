<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Repository;

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
