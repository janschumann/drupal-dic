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
   * @param string  $environment
   * @param bool    $debug
   * @param string  $rootDir      The root path of all files this kernel creates
   * @param string  $configDir    The directory to find configuration files
   */
  public function __construct($environment, $debug, $rootDir, $configDir)
  {
    $this->name = "Drupal";
    $this->rootDir = $rootDir;
    $this->configDir = $configDir;

    parent::__construct($environment, $debug);
  }

  /**
   * Set all bundles used by drupal modules
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
    // check if settings exists. this will allow the dic module to be installed before any settings are provided
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
