<?php
/**
 * This file registers CiviCRM entities.
 * Lifecycle events in this extension will cause these registry records to be
 * automatically inserted, updated, or deleted from the database as appropriate.
 * For more details, see "hook_civicrm_managed" (at
 * https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_managed/) as well
 * as "API and the Art of Installation" (at
 * https://civicrm.org/blogs/totten/api-and-art-installation).
 */

return array(
  array(
    'module' => 'com.joineryhq.extquicksearch',
    'name' => 'extquicksearch.optionvalue.contact_autocomplete_options.current_employer',
    'entity' => 'OptionValue',
    'params' => array(
      'version' => 3,
      'option_group_id' => 'contact_autocomplete_options',
      'label' => 'Current Employer',
      'value' => 'current_employer',
      'name' => 'current_employer',
      'is_active' => 1,
    ),
  ),
  array(
    'module' => 'com.joineryhq.extquicksearch',
    'name' => 'extquicksearch.optionvalue.contact_reference_options.current_employer',
    'entity' => 'OptionValue',
    'params' => array(
      'version' => 3,
      'option_group_id' => 'contact_reference_options',
      'label' => 'Current Employer',
      'value' => 'current_employer',
      'name' => 'current_employer',
      'is_active' => 1,
    ),
  ),
);
