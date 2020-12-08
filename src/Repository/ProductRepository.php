<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\Repository;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as CoreProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductRepository extends CoreProductRepository implements ProductRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findManyByChannelAndIds(
        ChannelInterface $channel,
        array $productIds,
        int $maxResults
    ): array {
        $products = [];

        foreach ($productIds as $productId) {
            $product = $this->findOneByChannelAndId($channel, $productId);

            if (null !== $product) {
                $products[] = $product;

                if (count($products) >= $maxResults) {
                    break;
                }
            }
        }

        return $products;
    }

    public function findOneByChannelAndId(ChannelInterface $channel, int $id): ?ProductInterface
    {
        $qb = $this->createQueryBuilder('o');
        $expr = $qb->expr();

        return $qb
            ->where($expr->eq('o.id', ':id'))
            ->andWhere($expr->isMemberOf(':channel', 'o.channels'))
            ->andWhere('o.enabled = true')
            ->setParameter('id', $id)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
