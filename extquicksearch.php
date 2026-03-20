<?php

require_once 'extquicksearch.civix.php';

function extquicksearch_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if (strtolower($apiRequest['entity']) == 'contact' && strtolower($apiRequest['action']) == 'getquick') {
    $wrappers[] = new CRM_Extquicksearch_APIWrapper();
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function extquicksearch_civicrm_config(&$config) {
  _extquicksearch_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_validateForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_validateForm
 */
function extquicksearch_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  // This is a mild (but documented, at https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_validateForm/)
  // misuse of hook_civicrm_validateForm() to alter form values before saving.
  if ($formName == 'CRM_Admin_Form_Setting_Search') {
    $data = &$form->controller->container();
    $formBaseName = $form->getAttribute('name');
    // Unset the 'current_employer' option, because it WILL break quicksearch.
    $extquicksearch_is_quicksearch_current_employer = CRM_Utils_Array::value('current_employer', $data['values'][$formBaseName]['contact_autocomplete_options'], 0);
    unset($data['values'][$formBaseName]['contact_autocomplete_options']['current_employer']);
    // Now save the 'current_employer' option value as our own setting, which
    // we'll use later to affect the quicksearch output.
    Civi::settings()->set('extquicksearch_is_quicksearch_current_employer', $extquicksearch_is_quicksearch_current_employer);
  }
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function extquicksearch_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Admin_Form_Setting_Search') {
    // In extquicksearch_civicrm_validateForm(), we prevented the "Current Employer"
    // option from being saved in the contact_autocomplete_options option group.
    // So by default it should ALWAYS be "off" here. Retrieve the correct
    // value from our extension setting, and set the default value so it appears
    // correctly in the form.
    $contact_autocomplete_options_default = CRM_Utils_Array::value('contact_autocomplete_options', $form->_defaultValues);
    if ($contact_autocomplete_options_default) {
      $contact_autocomplete_options_default['current_employer'] = Civi::settings()->get('extquicksearch_is_quicksearch_current_employer');
      $defaults = array(
        'contact_autocomplete_options' => $contact_autocomplete_options_default,
      );
      $form->setDefaults($defaults);
    }
  }
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function extquicksearch_civicrm_install() {
  _extquicksearch_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function extquicksearch_civicrm_enable() {
  _extquicksearch_civix_civicrm_enable();
}

function _extquicksearch_get_search_config() {
  $config = CRM_Core_BAO_Setting::getItem('com.joineryhq.extquicksearch', 'search_config');
  foreach ($config as $fieldId => &$fieldCriteria) {
    if (in_array('default', $fieldCriteria)) {
      // Newer civicrm versions use 'sort_name' instead of 'default'.
      $fieldCriteria[] = 'sort_name';
    }
  }
  return $config;
}
