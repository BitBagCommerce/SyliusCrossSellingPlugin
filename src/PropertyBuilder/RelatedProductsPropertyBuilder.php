<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\PropertyBuilder;

use FOS\ElasticaBundle\Event\TransformEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RelatedProductsPropertyBuilder implements EventSubscriberInterface
{
    public const PROPERTY_PRODUCT_IDS = 'product_ids';

    private const ORDER_STATES = [
        OrderInterface::STATE_NEW,
        OrderInterface::STATE_FULFILLED,
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            TransformEvent::POST_TRANSFORM => 'consumeEvent',
        ];
    }

    public function consumeEvent(TransformEvent $event): void
    {
        $model = $event->getObject();

        if (!$model instanceof OrderInterface || !in_array($model->getState(), self::ORDER_STATES, true)) {
            return;
        }

        $productIds = $model->getItems()->map(function (OrderItemInterface $orderItem): ?int {
            $product = $orderItem->getProduct();

            /** @noinspection PhpExpressionAlwaysNullInspection */
            return null !== $product ? $product->getId() : null;
        });

        $productIds = array_filter($productIds->toArray());

        $document = $event->getDocument();
        $document->set(self::PROPERTY_PRODUCT_IDS, $productIds);
    }
}
