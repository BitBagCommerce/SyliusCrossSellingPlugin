<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusUpsellingPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Tests\BitBag\SyliusUpsellingPlugin\Behat\Page\Shop\Product\ShowPageInterface;
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
