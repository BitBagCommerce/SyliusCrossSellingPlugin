<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusCrossSellingPlugin\Twig\Extension;

use BitBag\SyliusCrossSellingPlugin\Finder\RelatedProductsFinderInterface;
use BitBag\SyliusCrossSellingPlugin\Twig\Extension\RenderRelatedProductsExtension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;

final class RenderRelatedProductsExtensionSpec extends ObjectBehavior
{
    public function let(
        RelatedProductsFinderInterface $relatedProductsFinder,
        Environment $twig
    ): void {
        $this->beConstructedWith(
            $relatedProductsFinder,
            $twig,
            '@defaultTemplate.html.twig'
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RenderRelatedProductsExtension::class);
    }

    public function it_implements_extension_interface(): void
    {
        $this->shouldHaveType(ExtensionInterface::class);
    }

    public function it_renders_related_products(
        RelatedProductsFinderInterface $relatedProductsFinder,
        Environment $twig
    ): void {
        $relatedProducts = [];

        $relatedProductsFinder->findRelatedInCurrentChannelBySlug(Argument::cetera())
            ->willReturn($relatedProducts);

        /** @noinspection PhpTemplateMissingInspection */
        $twig->render('@customTemplate.html.twig', [
            'products' => $relatedProducts,
        ])->willReturn('rendered template');

        $this->renderRelatedProducts('test-123', 8, '@customTemplate.html.twig');
    }
}
