<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusCrossSellingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Workflow\WorkflowInterface;
use Webmozart\Assert\Assert;

final class RelatedProductsContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly FactoryInterface $orderFactory,
        private readonly FactoryInterface $orderItemFactory,
        private readonly FactoryInterface $customerFactory,
        private readonly WorkflowInterface $orderWorkflow,
        private readonly OrderItemQuantityModifierInterface $itemQuantityModifier,
        private readonly EntityManagerInterface $entityManager
    ) {
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


            $this->orderWorkflow->apply($order, OrderTransitions::TRANSITION_CREATE);

            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            foreach ($products as $product) {
                $variant = $product->getVariants()->first();
                $this->addVariantWithPriceToOrder($order, $variant);
            }

            $this->payOrder($order);
            $this->shipOrder($order);

            $this->entityManager->persist($order);
        }

        $this->entityManager->flush();
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
            $this->orderWorkflow->get($payment, PaymentTransitions::GRAPH)
                ->apply($payment, $transition);
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
        $this->orderWorkflow->get($order, OrderPaymentTransitions::GRAPH)
            ->apply(OrderPaymentTransitions::TRANSITION_PAY);
    }

    private function shipOrder(OrderInterface $order): void
    {
        $this->orderWorkflow->get($order, OrderShippingTransitions::GRAPH)
            ->apply(OrderShippingTransitions::TRANSITION_SHIP);
    }
}
