<?php

namespace Drupal\Dic\Bundle\DicBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Example for a symfony dependency injection extension: compiler passes
 */
class MyPass implements CompilerPassInterface {

  /**
   * You can modify the container here before it is dumped to PHP code.
   *
   * @param ContainerBuilder $container
   *
   * @api
   */
  public function process(ContainerBuilder $container) {
    // build the container
  }

}


