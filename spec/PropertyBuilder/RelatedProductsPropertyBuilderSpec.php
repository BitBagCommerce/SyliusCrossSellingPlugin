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
use FOS\ElasticaBundle\Event\PostTransformEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RelatedProductsPropertyBuilderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RelatedProductsPropertyBuilder::class);
    }

    public function it_implements_event_subscriber_interface(): void
    {
        $this->shouldHaveType(EventSubscriberInterface::class);
    }

    public function it_consumes_event(
        OrderInterface $model,
        OrderItemInterface $orderItem1,
        OrderItemInterface $orderItem2,
        ProductInterface $product1,
        ProductInterface $product2
    ): void {
        $document = new Document();
        $event = new PostTransformEvent($document, [], $model->getWrappedObject());

        $model->getState()->willReturn(OrderInterface::STATE_NEW);

        $orderItems = new ArrayCollection([
            $orderItem1->getWrappedObject(),
            $orderItem2->getWrappedObject(),
        ]);

        $orderItem1->getProduct()->willReturn($product1->getWrappedObject());
        $orderItem2->getProduct()->willReturn($product2->getWrappedObject());

        $product1->getId()->willReturn(123);
        $product2->getId()->willReturn(456);

        $model->getItems()->willReturn($orderItems);

        $this->consumeEvent($event);
    }

    public function it_ignores_non_order_models(ProductInterface $model, Document $document): void
    {
        $event = new PostTransformEvent($document->getWrappedObject(), [], $model->getWrappedObject());

        $this->consumeEvent($event);
        $document->set(RelatedProductsPropertyBuilder::PROPERTY_STATE, Argument::any())->shouldNotHaveBeenCalled();
        $document->set(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS, Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_ignores_orders_in_state_cart(OrderInterface $model, Document $document): void
    {
        $event = new PostTransformEvent($document->getWrappedObject(), [], $model->getWrappedObject());

        $model->getState()->willReturn(OrderInterface::STATE_CART);

        $this->consumeEvent($event);
        $document->set(RelatedProductsPropertyBuilder::PROPERTY_STATE, Argument::any())->shouldNotHaveBeenCalled();
        $document->set(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS, Argument::any())->shouldNotHaveBeenCalled();
    }
}
