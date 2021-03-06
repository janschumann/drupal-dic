<?php

namespace Drupal\Dic\Bundle\DicBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * DicExtension. This is an example implementation of how to implement a
 * dependency Injection Extension extension in a drupal context
 */
class DicExtension extends Extension
{
  /**
   * Responds to the app.config configuration parameter.
   *
   * @param array            $configs
   * @param ContainerBuilder $container
   */
  public function load(array $configs, ContainerBuilder $container)
  {
    $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

    $loader->load('services.xml');
  }

  /**
   * Returns the base path for the XSD files.
   *
   * @return string The XSD base path
   */
  public function getXsdValidationBasePath()
  {
    return __DIR__.'/../Resources/config/schema';
  }

  public function getNamespace()
  {
    return 'http://drupal.org/schema/dic/dic';
  }
}
