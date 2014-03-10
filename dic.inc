<?php

/**
 * Register namespace prefixes
 */
function _dic_register() {
  $loader = drupal_classloader();
  $loader->addPrefix('Symfony\Component\Config', dirname(__FILE__) . '/vendor/symfony/config');
  $loader->addPrefix('Symfony\Component\DependencyInjection', dirname(__FILE__) . '/vendor/symfony/dependency-injection');
  $loader->addPrefix('Symfony\Component\Filesystem', dirname(__FILE__) . '/vendor/symfony/filesystem');
  $loader->addPrefix('Symfony\Component\HttpKernel', dirname(__FILE__) . '/../vendor/symfony/http-kernel');
  $loader->addPrefix('Drupal\Dic', dirname(__FILE__) . '/src');
}

/**
 * Initializes the project kernel
 *
 * @param bool $ensureBootable Weather the kernel should be bootable
 *
 * @return \Drupal\Dic\ProjectKernel
 */
function _dic_kernel($ensureBootable = false) {
  static $kernel = NULL;

  if (is_null($kernel)) {
    _dic_register();

    // initialize the kernel
    $environment = variable_get('dic_environment', 'dev');
    $debug       = variable_get('dic_debug', FALSE);
    $rootDir     = variable_get('dic_root_dir', FALSE);
    if (!$rootDir) {
      $rootDir = DRUPAL_ROOT . '/' . variable_get('file_public_path', '');
    }
    $kernel = new \Drupal\Dic\ProjectKernel($environment, $debug, $rootDir);
  }
  elseif (TRUE === $ensureBootable) {
    // clone the kernel to make sure it will be (re-)booted
    $kernel = clone $kernel;
  }

  return $kernel;
}
