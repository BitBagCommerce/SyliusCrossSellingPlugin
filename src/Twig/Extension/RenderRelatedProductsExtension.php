<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\Twig\Extension;

use BitBag\SyliusUpsellingPlugin\Finder\RelatedProductsFinderInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RenderRelatedProductsExtension extends AbstractExtension
{
    private const DEFAULT_COUNT = 4;

    /** @var RelatedProductsFinderInterface */
    private $relatedProductsFinder;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var string */
    private $defaultTemplate;

    public function __construct(
        RelatedProductsFinderInterface $relatedProductsFinder,
        EngineInterface $templatingEngine,
        string $defaultTemplate
    ) {
        $this->relatedProductsFinder = $relatedProductsFinder;
        $this->templatingEngine = $templatingEngine;
        $this->defaultTemplate = $defaultTemplate;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'bitbag_upselling_render_related_products',
                [$this, 'renderRelatedProducts'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function renderRelatedProducts(
        string $slug,
        int $count = self::DEFAULT_COUNT,
        ?string $template = null
    ): string {
        $template = $template ?? $this->defaultTemplate;

        $products = $this->relatedProductsFinder->findRelatedInCurrentChannelBySlug($slug, $count);

        return $this->templatingEngine->render($template, [
            'products' => $products,
        ]);
    }
}
