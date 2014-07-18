<?php

namespace Drupal\Dic\Bundle\DicBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @package Drupal\Dic\Bundle\DicBundle
 */
class DicBundle extends Bundle {

  public function build(ContainerBuilder $container)
  {
    parent::build($container);

    $container->addCompilerPass(new RegisterListenersPass('event_dispatcher', 'drupal.event_listener', 'drupal.event_subscriber'), PassConfig::TYPE_BEFORE_REMOVING);
  }
}
