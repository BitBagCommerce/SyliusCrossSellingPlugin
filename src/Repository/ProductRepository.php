<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\Repository;

use Doctrine\DBAL\Connection;
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

    /**
     * {@inheritDoc}
     */
    public function findLatestByChannelAndTaxonCode(
        ChannelInterface $channel,
        string $code,
        int $count,
        array $excludedProductIds = []
    ): array {
        $qb = $this->createQueryBuilder('o');
        $expr = $qb->expr();

        $qb
            ->innerJoin('o.channels', 'channel')
            ->innerJoin('o.productTaxons', 'productTaxons')
            ->innerJoin('productTaxons.taxon', 'taxon')
            ->andWhere('o.enabled = true')
            ->andWhere($expr->isMemberOf(':channel', 'o.channels'))
            ->andWhere($expr->eq('taxon.code', ':code'))
            ->addOrderBy('productTaxons.position', 'asc')
            ->setParameter('code', $code)
            ->setParameter('channel', $channel)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;

        if (count($excludedProductIds) > 0) {
            $qb
                ->andWhere($expr->notIn('o.id', ':excludedProductIds'))
                ->setParameter('excludedProductIds', $excludedProductIds, Connection::PARAM_INT_ARRAY)
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
