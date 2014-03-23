<?php

namespace Drupal\Dic\Bundle\DicBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

  /**
   * Generates the configuration tree builder.
   *
   * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder();

    $rootNode = $treeBuilder->root('dic');

    $rootNode
      ->children()
        ->scalarNode('example')->end()
      ->end()
    ;

    return $treeBuilder;
  }
}