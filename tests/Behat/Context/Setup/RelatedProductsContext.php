<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusUpsellingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;

final class RelatedProductsContext implements Context
{
    /**
     * @Given there were :arg3 orders with product :arg1 and product :arg2
     */
    public function thereWereOrdersWithProductAndProduct($arg1, $arg2, $arg3): void
    {
        // todo: implement method
    }

}
