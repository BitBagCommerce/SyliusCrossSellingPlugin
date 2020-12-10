<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusUpsellingPlugin\Unit\Finder;

use BitBag\SyliusUpsellingPlugin\Finder\RelatedProductsByTaxonsFinder;
use BitBag\SyliusUpsellingPlugin\Repository\ProductRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\ImmutableLocaleContext;

final class RelatedProductsByTaxonsFinderTest extends TestCase
{
    /** @var RelatedProductsByTaxonsFinder */
    private $sut;

    /** @var MockObject|ProductRepositoryInterface */
    private $productRepository;

    public function setUp(): void
    {
        $channelContext = $this->createMock(ChannelContextInterface::class);
        $channelContext->method('getChannel')->willReturn(new Channel());

        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->setupRepository();

        $this->sut = new RelatedProductsByTaxonsFinder(
            $channelContext,
            new ImmutableLocaleContext('en_US'),
            $this->productRepository
        );
    }

    /**
     * @dataProvider expectedRelatedProductIdsDataProvider
     */
    public function test_it_returns_after_reaching_target_number_of_results(
        int $maxResults,
        array $expectedRelatedProductIds
    ): void {
        $product = $this->createProduct(1, ['taxon-A', 'taxon-B', 'taxon-C']);

        $this->productRepository->method('findOneByChannelAndSlug')->willReturn($product);

        $this->assertEquals(
            $expectedRelatedProductIds,
            $this->getIds($this->sut->findRelatedInCurrentChannelBySlug('slug', $maxResults))
        );
    }

    public function test_main_taxon_is_prioritized(): void
    {
        $product = $this->createProduct(1, ['taxon-A', 'taxon-B', 'taxon-C'], 'taxon-B');

        $this->productRepository->method('findOneByChannelAndSlug')->willReturn($product);

        $this->assertEquals(
            [4, 5, 2, 3, 6, 7],
            $this->getIds($this->sut->findRelatedInCurrentChannelBySlug('slug', 6))
        );
    }

    public function expectedRelatedProductIdsDataProvider(): iterable
    {
        return [
            ['maxResults' => 2, 'expectedRelatedProductIds' => [2, 3]],
            ['maxResults' => 3, 'expectedRelatedProductIds' => [2, 3, 4]],
            ['maxResults' => 4, 'expectedRelatedProductIds' => [2, 3, 4, 5]],
            ['maxResults' => 6, 'expectedRelatedProductIds' => [2, 3, 4, 5, 6, 7]],
            ['maxResults' => 32, 'expectedRelatedProductIds' => [2, 3, 4, 5, 6, 7]],
        ];
    }

    private function setupRepository(): void
    {
        $repositoryProducts = [
            'taxon-A' => [
                $this->createProduct(2, ['taxon-A']),
                $this->createProduct(3, ['taxon-A']),
            ],
            'taxon-B' => [
                $this->createProduct(4, ['taxon-B']),
                $this->createProduct(5, ['taxon-B']),
            ],
            'taxon-C' => [
                $this->createProduct(6, ['taxon-C']),
                $this->createProduct(7, ['taxon-C']),
            ],
        ];

        $this->productRepository->method('findLatestByChannelAndTaxonCode')
            ->willReturnCallback(function (
                $channel,
                string $taxonCode,
                int $maxResults
            ) use ($repositoryProducts): array {
                $result = $repositoryProducts[$taxonCode] ?? [];

                if (count($result) > $maxResults) {
                    $result = array_slice($result, 0, $maxResults);
                }

                return $result;
            });
    }

    /**
     * @param string[] $taxonCodes
     */
    private function createProduct(int $id, array $taxonCodes, ?string $mainTaxonCode = null): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getId')->willReturn($id);
        $product->method('getTaxons')->willReturn($this->createTaxons($taxonCodes));

        if (null !== $mainTaxonCode) {
            foreach ($product->getTaxons() as $taxon) {
                if ($taxon->getCode() === $mainTaxonCode) {
                    $product->method('getMainTaxon')->willReturn($taxon);

                    break;
                }
            }
        }

        return $product;
    }

    /**
     * @param string[] $taxonCodes
     * @return TaxonInterface[]|Collection
     */
    private function createTaxons(array $taxonCodes): Collection
    {
        return new ArrayCollection(array_map(function (string $code): TaxonInterface {
            return $this->createTaxon($code);
        }, $taxonCodes));
    }

    private function createTaxon(string $code): TaxonInterface
    {
        $taxon = $this->createMock(TaxonInterface::class);
        $taxon->method('getId')->willReturn(uniqid());
        $taxon->method('getCode')->willReturn($code);

        return $taxon;
    }

    /**
     * @param ProductInterface[] $products
     * @return int[]
     */
    protected function getIds(array $products): array
    {
        return array_map(function(ProductInterface $product): int {
            return $product->getId();
        }, $products);
    }
}
