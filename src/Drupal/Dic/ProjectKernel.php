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
   * @var string
   */
  private $configDir = '';

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
  public function __construct($environment, $debug, $rootDir, $configDir)
  {
    $this->name = 'DrupalProjectKernel';
    $this->rootDir = $rootDir;
    $this->configDir = $configDir;

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
    $this->booted = false;
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
    $settings = $this->configDir . '/settings_' . $this->getEnvironment() . '.xml';
    // check if settings exists. this will allow the module to be installed before any settings are provided
    if (file_exists($settings)) {
      $loader->load($settings);
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
        $parameters[strtolower(str_replace('__', '.', substr($key, 8)))] = $value;
      }
    }

    return $parameters;
  }
}
