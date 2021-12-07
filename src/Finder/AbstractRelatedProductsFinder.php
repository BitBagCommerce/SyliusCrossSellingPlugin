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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

abstract class AbstractRelatedProductsFinder implements RelatedProductsFinderInterface
{
    /** @var ChannelContextInterface */
    protected $channelContext;

    /** @var LocaleContextInterface */
    protected $localeContext;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ProductRepositoryInterface $productRepository
    ) {
        $this->channelContext = $channelContext;
        $this->localeContext = $localeContext;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public function findRelatedInCurrentChannelBySlug(
        string $slug,
        int $maxResults,
        array $excludedProductIds = []
    ): array {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->findRelatedByChannelAndSlug(
            $channel,
            $this->localeContext->getLocaleCode(),
            $slug,
            $maxResults,
            $excludedProductIds
        );
    }

    /**
     * @param ProductInterface[] $products
     *
     * @return int[]
     */
    protected function getIds(array $products): array
    {
        return array_map(function (ProductInterface $product): int {
            return $product->getId();
        }, $products);
    }
}
