<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Exception;

use BitBag\SyliusCrossSellingPlugin\Exception\ProductNotFoundException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;

class ProductNotFoundExceptionSpec extends ObjectBehavior
{
    public function let(
        ChannelInterface $channel
    ): void {
        $this->beConstructedWith('test-123', $channel, 'en_US');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductNotFoundException::class);
    }

    public function it_should_extend_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }
}
