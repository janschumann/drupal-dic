<?php

/**
 * Returns the dic helper
 *
 * @return \Drupal\Dic\DicHelper
 */
function _dic_get_helper() {
  $helper = \Drupal\Dic\DicHelper::getInstance();
  if (!$helper->isInitialized()) {
    _dic_init_helper($helper);
  }

  return $helper;
}

/**
 * Initializes the helper with drupal settings
 *
 * @param \Drupal\Dic\DicHelper $helper
 */
function _dic_init_helper(\Drupal\Dic\DicHelper $helper) {
  $classLoader = drupal_classloader();
  $rootDir     = variable_get('dic_root_dir', false);
  if (!$rootDir) {
    $rootDir = DRUPAL_ROOT . '/' . variable_get('file_public_path', '');
  }
  $environment = variable_get('dic_environment', 'prod');
  $debug       = variable_get('dic_debug', false);
  $configDir   = variable_get('dic_config_dir', false);
  if (!$configDir) {
    $configDir = DRUPAL_ROOT . '/sites/default';
  }
  $helper->initialize($classLoader, $rootDir, $environment, $debug, $configDir);
  $helper->setBundleInfo(_dic_get_bundle_info(!$debug));
}

/**
 * Fetch bundle info
 *
 * @param boolean $debug
 *
 * @return array
 */
function _dic_get_bundle_info($useCache = true) {
  $bundle_info = array();

  if (true === $useCache) {
    // get bundle info from cache (variables)
    $bundle_info = cache_get('dic:bundle_info');
    // bundle info cache may be empty and returns no array
    if (!is_array($bundle_info)) {
      $bundle_info = array();
    }
  }

  if (0 === count($bundle_info)) {
    // fetch bundle information from modules if not loaded from cache
    $bundle_info = module_invoke_all('dic_bundle_info');
    // cache bundle info
    cache_set('dic:bundle_info', $bundle_info);
  }

  return $bundle_info;
}
