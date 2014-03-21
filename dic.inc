<?php

/**
 * Register namespaces with the class loader
 *
 * @return boolean if autoloading was successful
 */
function _dic_register($map = array()) {
  if (function_exists('drupal_classloader')) {
    $loader = drupal_classloader();

    if (0 === count($map)) {
      // autoload composer namespaces

      // This file is created by composer when installing the dependencies of the logging-component with it.
      // To use it with the already installed classloader the namespace is registered.
      $map = require DRUPAL_ROOT . '/../vendor/composer/autoload_namespaces.php';
    }

    // changes in api of Classloader between 2.0.x and 2.2.x
    if (method_exists($loader, 'addPrefixes')) {
      $loader->addPrefixes($map);
    } else {
      $loader->registerNamespaces($map);
    }

    return true;
  }

  return false;
}

/**
 * Initializes the project kernel
 *
 * @param bool $ensureBootable Weather the kernel should be bootable
 *
 * @return \Drupal\Dic\ProjectKernel
 */
function _dic_kernel($ensureBootable = false) {
  static $kernel = null;

  if (is_null($kernel)) {
    _dic_register();

    // initialize the kernel
    $environment = variable_get('dic_environment', 'prod');
    $debug       = variable_get('dic_debug', false);
    $rootDir     = variable_get('dic_root_dir', false);
    if (!$rootDir) {
      $rootDir = DRUPAL_ROOT . '/' . variable_get('file_public_path', '');
    }
    $configDir   = variable_get('dic_config_dir', false);
    if (!$configDir) {
      $configDir = $rootDir . '/sites/default';
    }
    $kernel = new \Drupal\Dic\ProjectKernel($environment, $debug, $rootDir, $configDir);
  }
  elseif (true === $ensureBootable) {
    // clone the kernel to make sure it will be (re-)booted
    $kernel = clone $kernel;
  }

  return $kernel;
}

/**
 * Retrieves bundles from other modules and handles autoloading for them
 *
 * @retrun array an array of bundles
 */
function _dic_get_autoloaded_bundles($debug = false) {
  static $bundle_info = array();

  if (0 === count($bundle_info)) {
    // do not load from cache in debug mode
    if (false === $debug) {
      // get bundle info from cache (variables)
      $bundle_info = cache_get('dic:bundle_info');
      // bundle info cache may be empty and returns no array
      if (!is_array($bundle_info)) {
        $bundle_info = array();
      }
    }
    if (0 === count($bundle_info)) {
      // fetch bundle information from modules
      $bundle_info = module_invoke_all('dic_bundle_info');
      // cache bundle info
      cache_set('dic:bundle_info', $bundle_info);
    }
  }

  $bundles = array();
  if (isset($bundle_info['bundles']) && is_array($bundle_info['bundles'])) {
    $bundles = $bundle_info['bundles'];

    // initialize autoloading if necessary
    if (isset($bundle_info['autoload']) && is_array($bundle_info['bundles'])) {
      if (false === _dic_register($bundle_info['autoload'])) {
        drupal_set_message('Autoloading bundles failed!', 'error');
        return array();
      }
    }
  }

  return $bundles;
}
