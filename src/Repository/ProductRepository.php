<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Repository;

use Doctrine\DBAL\Connection;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as CoreProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

class ProductRepository extends CoreProductRepository implements ProductRepositoryInterface
{
    /**
     * @inheritDoc
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
     * @inheritDoc
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

        if (0 < count($excludedProductIds)) {
            $qb
                ->andWhere($expr->notIn('o.id', ':excludedProductIds'))
                ->setParameter('excludedProductIds', $excludedProductIds, Connection::PARAM_INT_ARRAY)
            ;
        }

        return $qb->getQuery()->getResult();
    }
}
