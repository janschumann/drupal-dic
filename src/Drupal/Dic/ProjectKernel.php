<?php

namespace Drupal\Dic;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class ProjectKernel extends Kernel {

  /**
   * @var array
   */
  private $drupalBundles = array();

  /**
   * Constructor.
   *
   * This adds the parameter rootDir witch should be set to the
   * root save path of cache and log files that should be used by this kernel.
   * Usually a drupal temp or assets path will be used here
   *
   * @param string $environment
   * @param bool   $debug
   * @param        $rootDir
   */
  public function __construct($environment, $debug, $rootDir)
  {
    $this->rootDir = $rootDir;
    $this->name = 'DrupalProjectKernel';

    parent::__construct($environment, $debug);
  }

  /**
   * Set all bundles found by the hook register_bundles
   * These will be used by registerBundles()
   *
   * @param array $bundles
   */
  public function setDrupalBundles(array $bundles) {
    $this->drupalBundles = $bundles;
  }

  /**
   * {@inheritdoc}
   */
  public function registerBundles()
  {
    $bundles = array();
    foreach ($this->drupalBundles as $bundle) {
      $bundles[] = new $bundle();
    }

    return $bundles;
  }

  /**
   * {@inheritdoc}
   */
  public function registerContainerConfiguration(LoaderInterface $loader)
  {
    // check if settings exists. this will allow the module to be installed before any settings are provided
    if (file_exists(__DIR__ . '/../../../../../../../default/settings_' . $this->getEnvironment() . '.xml')) {
      $loader->load(__DIR__ . '/../../../../../../../default/settings_' . $this->getEnvironment() . '.xml');
    }
  }

  /**
   * Consider only Server Parameters prefixed with DRUPAL__
   *
   * {@inheritdoc}
   */
  protected function getEnvParameters()
  {
    $parameters = array();
    foreach ($_SERVER as $key => $value) {
      if (0 === strpos($key, 'DRUPAL__')) {
        $parameters[strtolower(str_replace('__', '.', substr($key, 9)))] = $value;
      }
    }

    return $parameters;
  }
}
