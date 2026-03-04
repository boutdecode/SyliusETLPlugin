<?php

declare(strict_types=1);

namespace BoutDeCode\SyliusETLPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bout_de_code_sylius_etl');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
