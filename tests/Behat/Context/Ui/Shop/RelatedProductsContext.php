<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusCrossSellingPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Tests\BitBag\SyliusCrossSellingPlugin\Behat\Page\Shop\Product\ShowPageInterface;
use Webmozart\Assert\Assert;

final class RelatedProductsContext implements Context
{
    /** @var ShowPageInterface */
    private $showPage;

    public function __construct(ShowPageInterface $showPage)
    {
        $this->showPage = $showPage;
    }

    /**
     * @Then I should see related products list with the following products:
     */
    public function iShouldSeeRelatedProductsListWithTheFollowingProducts(TableNode $table): void
    {
        $productNames = array_map(function (array $product): string {
            return $product['name'];
        }, $table->getHash());

        Assert::same(
            $this->showPage->getRelatedProductNames(),
            $productNames,
            sprintf(
                'Expected products "%s", but got products "%s"',
                implode('", "', $productNames),
                implode('", "', $this->showPage->getRelatedProductNames())
            )
        );
    }
}
