<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
