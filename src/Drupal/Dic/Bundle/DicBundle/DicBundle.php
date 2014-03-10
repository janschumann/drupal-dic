<?php

namespace Drupal\Dic\Bundle\DicBundle;

use Drupal\Dic\Bundle\DicBundle\DependencyInjection\Compiler\MyPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * An example for a Bundle implementation
 *
 * @package Drupal\Dic\Bundle\DicBundle
 */
class DicBundle extends Bundle {

  public function build(ContainerBuilder $container)
  {
    parent::build($container);

    $container->addCompilerPass(new MyPass());
  }
}
