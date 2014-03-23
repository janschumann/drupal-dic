<?php

/**
 * Dic settings form
 *
 * @see hook_menu()
 *
 * @return array
 */
function dic_admin_settings_form() {
  $form = array();

  $form['dic_root_dir'] = array(
    '#type' => 'textfield',
    '#title' => t('Root directory'),
    '#default_value' => variable_get('dic_root_dir', DRUPAL_ROOT . '/' . variable_get('file_public_path', '') . '/dic'),
    '#description' => t('The absolute path to store dic cache. Leave empty to use the file_public_path.'),
    '#required' => false,
  );
  $form['dic_environment'] = array(
    '#type' => 'textfield',
    '#title' => t('Environment'),
    '#default_value' => variable_get('dic_environment', 'production'),
    '#description' => t('The environment for with the container should be compiled for.'),
    '#required' => true,
  );
  $form['dic_debug'] = array(
    '#type' => 'radios',
    '#title' => t('Debug mode'),
    '#default_value' => variable_get('dic_debug', 0),
    '#options' => array(0 => t('false'), 1 => t('true')),
    '#required' => true,
  );
  $form['dic_config_dir'] = array(
    '#type' => 'textfield',
    '#title' => t('Configuration directory'),
    '#default_value' => variable_get('dic_config_dir', ''),
    '#description' => t('Where to find configuration files. Leave empty to use the same path as settings.php.'),
    '#required' => false,
  );
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );

  return $form;
}

/**
 * Submit handler for dic settings
 *
 * @param $form
 * @param $form_state
 */
function dic_admin_settings_form_submit($form, $form_state) {
  // flush the cache of the current environment
  _dic_get_helper()->flushCaches();

  // save form values
  foreach ($form_state['values'] as $key => $value) {
    if (strpos($key, 'dic_') === 0) {
      variable_set($key, $value);
    }
  }

  // re-initialize the kernel with current values
  $helper = _dic_get_helper(true);
  // flush the cache of the target environment
  $helper->flushCaches();
  // prepare the new container for the next request
  $helper->getContainer();
}
