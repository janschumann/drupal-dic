<?php

/**
 * Returns the dic helper
 *
 * @return \Drupal\Dic\DicHelper
 */
function _dic_get_helper($re_initialize = false) {
  static $helper;

  if (!($helper instanceof \Drupal\Dic\DicHelper) || $re_initialize) {
    // load dic parameters @see dic_menu()
    $rootDir     = variable_get('dic_root_dir', '');
    if ('' == $rootDir) {
      $rootDir = DRUPAL_ROOT . '/' . variable_get('file_public_path', '') . '/dic';
    }
    $environment = variable_get('dic_environment', 'prod');
    $debug       = (boolean) variable_get('dic_debug', 0);
    $configDir   = variable_get('dic_config_dir', '');
    if ('' == $configDir) {
      $configDir = DRUPAL_ROOT . '/sites/default';
    }

    // initialize kernel and helper
    $kernel = new \Drupal\Dic\ProjectKernel($environment, $debug, $rootDir, $configDir);
    $helper = new \Drupal\Dic\DicHelper($kernel);

    // set a classloader if it exists (otherwise bundles have to take care of autoloading themselves)
    if (function_exists('drupal_classloader')) {
      $helper->setClassLoader(drupal_classloader());
    }

    // provide the dic with bundle info
    $bundleInfo = _dic_get_bundle_info(!$kernel->isDebug());
    if (!empty($bundleInfo)) {
      $helper->setBundleInfo($bundleInfo);
    }
  }

  return $helper;
}

/**
 * Fetch bundle info from other modules
 *
 * @param boolean $debug
 *
 * @return array
 */
function _dic_get_bundle_info($load_from_cache = true) {
  $bundle_info = array();

  if ($load_from_cache) {
    $cache = cache_get('dic:bundle_info');
    if (is_object($cache)) {
      $bundle_info = $cache->data;
    }
  }

  if (empty($bundle_info)) {
    // fetch bundle information from modules
    $bundle_info = module_invoke_all('dic_bundle_info');
    cache_set('dic:bundle_info', $bundle_info);
  }

  return $bundle_info;
}
