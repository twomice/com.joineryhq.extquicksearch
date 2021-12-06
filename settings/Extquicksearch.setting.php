<?php

use CRM_Extquicksearch_ExtensionUtil as E;

return array(
  'extquicksearch_is_quicksearch_current_employer' => array(
    'group_name' => 'Extquicksearch Settings',
    'group' => 'extquicksearch',
    'name' => 'extquicksearch_is_quicksearch_current_employer',
    'type' => 'Int',
    'add' => '4.7',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('Show Current Employer in Quick Search results?'),
    'title' => E::ts('Current Employer'),
    'help_text' => '',
    'html_type' => 'Select',
    'html_attributes' => array(),
    'quick_form_type' => 'Element',
  ),
);
