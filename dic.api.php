<?php

/**
 * Provide the dic with information about bundles
 *
 * That is the bundles classes and their autoloading paths (optional)
 * If no autoloading info is given, the bundles have to take care of autoloading themselves
 *
 * @return array
 */
function hook_dic_bundle_info() {
  return array(
    'bundles' => array(
      "\\Drupal\\Dic\\Bundle\\DicBundle\\DicBundle"
    ),
    'autoload' => array(
      '\\Drupal\\Dic\\' => array(DRUPAL_ROOT . '/' . drupal_get_path('module', 'dic') . '/src'),
    )
  );
}
