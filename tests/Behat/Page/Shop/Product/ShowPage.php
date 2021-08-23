<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusCrossSellingPlugin\Behat\Page\Shop\Product;

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
            'related_products' => '[data-test-related-products-header] + *',
        ]);
    }
}
