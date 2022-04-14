<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusCrossSellingPlugin\Unit\PropertyBuilder;

use BitBag\SyliusCrossSellingPlugin\PropertyBuilder\RelatedProductsPropertyBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Document;
use FOS\ElasticaBundle\Event\PostTransformEvent;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class RelatedProductsPropertyBuilderTest extends TestCase
{
    /** @var RelatedProductsPropertyBuilder */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new RelatedProductsPropertyBuilder();
    }

    /**
     * @dataProvider acceptedStatesDataProvider
     */
    public function test_it_processes_orders_in_accepted_state(string $acceptedOrderState): void
    {
        $event = $this->createEvent($acceptedOrderState, [
            $this->createProduct(123),
        ]);

        $this->sut->consumeEvent($event);

        $this->assertSame(
            $event->getDocument()->get(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS),
            [123]
        );
    }

    /**
     * @dataProvider ignoredStatesDataProvider
     */
    public function test_it_ignores_orders_in_unwanted_state(string $ignoredOrderState): void
    {
        $event = $this->createEvent($ignoredOrderState, []);

        $this->sut->consumeEvent($event);

        $this->assertFalse(
            $event->getDocument()->has(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS)
        );
    }

    public function test_it_handles_multiple_products(): void
    {
        $event = $this->createEvent(OrderInterface::STATE_NEW, [
            $this->createProduct(123),
            $this->createProduct(456),
            $this->createProduct(789),
        ]);

        $this->sut->consumeEvent($event);

        $this->assertSame(
            $event->getDocument()->get(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS),
            [123, 456, 789]
        );
    }

    public function test_it_filters_empty_products(): void
    {
        $event = $this->createEvent(OrderInterface::STATE_NEW, [
            $this->createProduct(123),
            null,
        ]);

        $this->sut->consumeEvent($event);

        $this->assertSame(
            $event->getDocument()->get(RelatedProductsPropertyBuilder::PROPERTY_PRODUCT_IDS),
            [123]
        );
    }

    public function acceptedStatesDataProvider(): array
    {
        return [
            [OrderInterface::STATE_NEW],
            [OrderInterface::STATE_FULFILLED],
        ];
    }

    public function ignoredStatesDataProvider(): array
    {
        return [
            [OrderInterface::STATE_CART],
            [OrderInterface::STATE_CANCELLED],
        ];
    }

    private function createEvent(string $orderState, array $products): PostTransformEvent
    {
        $document = new Document();

        $orderItems = new ArrayCollection();
        foreach ($products as $product) {
            $orderItem = $this->createMock(OrderItemInterface::class);
            $orderItem->method('getProduct')->willReturn($product);
            $orderItems->add($orderItem);
        }

        $order = $this->createMock(OrderInterface::class);
        $order->method('getState')->willReturn($orderState);
        $order->method('getItems')->willReturn($orderItems);

        return new PostTransformEvent($document, [], $order);
    }

    private function createProduct(int $id): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getId')->willReturn($id);

        return $product;
    }
}
