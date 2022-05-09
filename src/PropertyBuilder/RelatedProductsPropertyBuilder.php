<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\PropertyBuilder;

use FOS\ElasticaBundle\Event\AbstractTransformEvent;
use FOS\ElasticaBundle\Event\PostTransformEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RelatedProductsPropertyBuilder implements EventSubscriberInterface
{
    public const PROPERTY_PRODUCT_IDS = 'product_ids';

    public const PROPERTY_STATE = 'state';

    private const ORDER_STATES = [
        OrderInterface::STATE_NEW,
        OrderInterface::STATE_FULFILLED,
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            PostTransformEvent::class => 'consumeEvent',
        ];
    }

    public function consumeEvent(AbstractTransformEvent $event): void
    {
        $model = $event->getObject();

        if (!$model instanceof OrderInterface) {
            return;
        }

        if (!in_array($model->getState(), self::ORDER_STATES, true)) {
            return;
        }

        $document = $event->getDocument();

        $document->set(self::PROPERTY_STATE, $model->getState());

        $productIds = $model->getItems()->map(function (OrderItemInterface $orderItem): ?int {
            $product = $orderItem->getProduct();

            /** @noinspection PhpExpressionAlwaysNullInspection */
            return null !== $product ? $product->getId() : null;
        });

        $productIds = array_filter($productIds->toArray());

        $document->set(self::PROPERTY_PRODUCT_IDS, $productIds);
    }
}
