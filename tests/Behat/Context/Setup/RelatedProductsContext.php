<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusUpsellingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class RelatedProductsContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var FactoryInterface */
    private $orderFactory;

    /** @var FactoryInterface */
    private $orderItemFactory;

    /** @var FactoryInterface */
    private $customerFactory;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $itemQuantityModifier;

    /** @var ObjectManager */
    private $objectManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $orderFactory,
        FactoryInterface $orderItemFactory,
        FactoryInterface $customerFactory,
        StateMachineFactoryInterface $stateMachineFactory,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->customerFactory = $customerFactory;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given /^there were (\d+) orders with (products "([^"]+)" and "([^"]+)")$/
     *
     * @param ProductInterface[] $products
     */
    public function thereWereOrdersWithProducts(
        int $count,
        array $products
    ): void {
        $this->createOrdersWithProducts($count, $products);
    }

    /**
     * @param ProductInterface[] $products
     */
    private function createOrdersWithProducts(
        int $numberOfOrders,
        array $products
    ): void {
        Assert::greaterThanEq($numberOfOrders, 1);
        for ($i = 0; $i < $numberOfOrders; ++$i) {
            $order = $this->createOrder();
            $this->stateMachineFactory->get($order, OrderTransitions::GRAPH)
                ->apply(OrderTransitions::TRANSITION_CREATE);
            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            foreach ($products as $product) {
                $variant = $product->getVariants()->first();
                $this->addVariantWithPriceToOrder($order, $variant);
            }

            $this->payOrder($order);
            $this->shipOrder($order);

            $this->objectManager->persist($order);
        }

        $this->objectManager->flush();
    }

    private function createOrder(): OrderInterface
    {
        $order = $this->createCart();
        $order->setNumber(uniqid('#'));

        $order->completeCheckout();

        return $order;
    }

    private function createCart(): OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($this->createCustomer());
        $order->setChannel($this->sharedStorage->get('channel'));
        $order->setLocaleCode($this->sharedStorage->get('locale')->getCode());
        $order->setCurrencyCode($order->getChannel()->getBaseCurrency()->getCode());

        return $order;
    }

    private function createCustomer(): CustomerInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();
        $customer->setEmail(sprintf('john%s@doe.com', uniqid()));
        $customer->setFirstName('John');
        $customer->setLastName('Doe');

        return $customer;
    }

    private function applyPaymentTransitionOnOrder(OrderInterface $order, string $transition)
    {
        foreach ($order->getPayments() as $payment) {
            $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH)
                ->apply($transition);
        }
    }

    private function addVariantWithPriceToOrder(OrderInterface $order, ProductVariantInterface $variant): void
    {
        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($variant);

        $this->itemQuantityModifier->modify($item, 1);

        $order->addItem($item);
    }

    private function payOrder(OrderInterface $order): void
    {
        $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)
            ->apply(OrderPaymentTransitions::TRANSITION_PAY);
    }

    private function shipOrder(OrderInterface $order): void
    {
        $this->stateMachineFactory->get($order, OrderShippingTransitions::GRAPH)
            ->apply(OrderShippingTransitions::TRANSITION_SHIP);
    }
}
