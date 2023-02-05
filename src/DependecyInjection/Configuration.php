<?php
namespace Wintex\SimpleApiBundle\DependecyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $configTreeBuilder = new TreeBuilder('wintex_api');

        $configTreeBuilder->getRootNode()
           ->children()
                ->scalarNode('entity_namespace')
                    ->isRequired()
                ->end()
                ->append($this->entitiesSection())
            ->end()
        ;

        return $configTreeBuilder;
    }

    public function groupSection()
    {
        $builder = new TreeBuilder("groups");

        $node = $builder->getRootNode();

        $node
            ->scalarPrototype()->end()
        ->end();

        return $node;
    }

    public function entitiesSection()
    {
        $builder = new TreeBuilder("entities");

        $node = $builder->getRootNode();

        $node
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->booleanNode("skip_null_values")->defaultFalse()->end()
                    ->append($this->groupSection())
                    ->append($this->extractPropertySection())
                    ->append($this->entitiesRoutesSection())
                ->end()
            ->end();

        return $node;
    }

    public function entitiesRoutesSection()
    {
        $builder = new TreeBuilder("routes");

        $node = $builder->getRootNode();

        $node
            ->useAttributeAsKey('name')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('repository_method')->end()
                    ->append($this->groupSection())
                    ->append($this->extractPropertySection())
                ->end()
            ->end();

        return $node;
    }

    public function extractPropertySection()
    {
        $builder = new TreeBuilder("extract_property");

        $node = $builder->getRootNode();

        $node
            ->arrayPrototype()
                ->children()
                    ->scalarNode("object")->end()
                    ->scalarNode("property")->end()
                ->end()
            ->end();

        return $node;
    }
}