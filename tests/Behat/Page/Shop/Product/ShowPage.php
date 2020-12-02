<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusUpsellingPlugin\Behat\Page\Shop\Product;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;

class ShowPage extends BaseShowPage implements ShowPageInterface
{
    /**
     * @return string[]
     */
    public function getRelatedProductNames(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('related_products')->findAll('css', '[data-test-product-name]')
        );
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'related_products' => '[data-test-related-products]',
        ]);
    }
}
