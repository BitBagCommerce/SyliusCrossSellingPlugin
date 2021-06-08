<?php

namespace spec\BitBag\SyliusCrossSellingPlugin\Exception;

use BitBag\SyliusCrossSellingPlugin\Exception\ProductNotFoundException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;

class ProductNotFoundExceptionSpec extends ObjectBehavior
{
    function let(
        ChannelInterface $channel
    ): void
    {
        $this->beConstructedWith('test-123', $channel, 'en_US');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductNotFoundException::class);
    }

    function it_should_extend_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }
}
