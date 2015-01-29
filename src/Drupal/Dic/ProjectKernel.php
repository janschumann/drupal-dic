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
    // load bundle specific settings
    foreach($this->getBundles() as $bundle) {
      $this->loadSettings($loader, strtolower(str_replace('Bundle', '', $bundle->getName())));
    }

    // load general settings
    $this->loadSettings($loader);
  }

  private function loadSettings($loader, $name = '') {
    // try to load environment specific file
    $prefix = $this->configDir . '/' . ($name ? $name . '_' : '') . 'settings';
    $settings = $prefix . '_' . $this->getEnvironment() . '.xml';
    if (file_exists($settings)) {
      $loader->load($settings);
    }
    else {
      // load general file as a fallback
      $settings = $prefix . '.xml';
      if (file_exists($settings)) {
        $loader->load($settings);
      }
    }
  }

  /**
   * Consider
   * - Server Parameters prefixed with DRUPAL__
   * - Constants prefixed with DRUPAL_
   *
   * {@inheritdoc}
   */
  protected function getEnvParameters() {
    $parameters = array();

    // add config dir as a parameter
    $parameters['kernel.config_dir'] = $this->configDir;

    // add server variables
    foreach ($_SERVER as $key => $value) {
      if (0 === strpos($key, 'DRUPAL__')) {
        $parameters[strtolower(str_replace('__', '.', substr($key, 8)))] = $value;
      }
    }

    // add constants
    $constants = get_defined_constants(true);
    foreach ($constants['user'] as $key => $value) {
      if (0 === strpos($key, strtoupper($this->name) . '_')) {
        $parameters[strtolower(str_replace('_', '.', $key))] = $value;
      }
    }

    return $parameters;
  }
}
