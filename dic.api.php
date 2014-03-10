<?php

/**
 * Provide the dic with bundles
 *
 * @return array
 */
function hook_register_bundle() {
  return array("\\Drupal\\Dic\\Bundle\\DicBundle\\DicBundle");
}
