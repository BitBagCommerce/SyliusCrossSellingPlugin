<?php

declare(strict_types=1);

namespace BitBag\SyliusUpsellingPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bitBag_sylius_example_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
