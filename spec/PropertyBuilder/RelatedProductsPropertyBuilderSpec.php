<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\PropertyBuilder;

use BitBag\SyliusCrossSellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Document;
use FOS\ElasticaBundle\Event\AbstractTransformEvent;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RelatedProductsPropertyBuilderSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsPropertyBuilder::class);
    }

    function it_implements_event_subscriber_interface(): void
    {
        $this->shouldHaveType(EventSubscriberInterface::class);
    }

    function it_consumes_event(
        AbstractTransformEvent $event,
        OrderInterface $model,
        Document $document,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        ProductInterface $product1,
        ProductInterface $product2
    ): void {
        $event->getObject()->willReturn($model);
        $event->getDocument()->willReturn(new Document());

        $orderItems = new ArrayCollection([
            $orderItem1->getWrappedObject(),
            $orderItem2->getWrappedObject()
        ]);

        $orderItem1->getProduct()->willReturn($product1->getWrappedObject());
        $orderItem2->getProduct()->willReturn($product2->getWrappedObject());

        $product1->getId()->willReturn(123);
        $product2->getId()->willReturn(456);

        $model->getState()->willReturn(OrderInterface::STATE_NEW);
        $model->getItems()->willReturn($orderItems);

        $this->consumeEvent($event);

        $document->set(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS, [123, 456])
            ->willReturn(new Document());
    }

    function it_ignores_non_order_models(
        AbstractTransformEvent $event,
        ProductInterface $model
    ): void {
        $event->getObject()->willReturn($model);

        $this->consumeEvent($event);
        $event->getDocument()->shouldNotHaveBeenCalled();
    }

    function it_ignores_orders_in_state_cart(
        AbstractTransformEvent $event,
        OrderInterface $model
    ): void {
        $event->getObject()->willReturn($model);

        $model->getState()->willReturn(OrderInterface::STATE_CART);

        $this->consumeEvent($event);

        $event->getDocument()->shouldNotHaveBeenCalled();
    }
}
