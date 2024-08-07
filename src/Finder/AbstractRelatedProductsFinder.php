<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
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
        ProductRepositoryInterface $productRepository,
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
        array $excludedProductIds = [],
    ): array {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        return $this->findRelatedByChannelAndSlug(
            $channel,
            $this->localeContext->getLocaleCode(),
            $slug,
            $maxResults,
            $excludedProductIds,
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
