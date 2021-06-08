<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusCrossSellingPlugin\Exception;

use Sylius\Component\Core\Model\ChannelInterface;

class ProductNotFoundException extends \Exception
{
    public function __construct(string $slug, ChannelInterface $channel, string $locale)
    {
        $message = sprintf(
            'Could not find product "%s" in channel "%s" with locale "%s"',
            $slug,
            $channel->getName(),
            $locale
        );

        parent::__construct($message, 404);
    }
}
