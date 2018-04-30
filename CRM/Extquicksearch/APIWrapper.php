<?php

class CRM_Extquicksearch_APIWrapper {
  /**
   * the wrapper contains a method that allows you to alter the parameters of the api request (including the action and the entity)
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * alter the result before returning it to the caller.
   */
  public function toApiOutput($apiRequest, $result) {
    $values = $result['values'];

    $search_context = $apiRequest['params']['field_name'] ?: 'default';
    $fields = _extquicksearch_get_search_config();

    foreach ($fields as $field_id => $contexts) {
      if (in_array($search_context, $contexts)) {
        self::appendResultValues($field_id, $apiRequest['params']['name'], $values);
      }
    }
    self::removeDuplicateValues($values);
    self::sortValues($values);
    self::limitValues($values);

    self::appendCurrentEmployerData($values);

    $result['values'] = $values;
    $result['count'] = count($values);

    return $result;
  }

  private static function sortValues(&$values) {
    $sortkeys = array();
    foreach ($values as $value) {
      $sortkeys[] = strtolower($value['sort_name']);
    }
    array_multisort($sortkeys, $values);
  }

  private static function limitValues(&$values) {
    $search_autocomplete_count = CRM_Core_BAO_Setting::getItem(NULL, 'search_autocomplete_count');
    $values = array_slice($values, 0, $search_autocomplete_count);
  }

  private static function appendResultValues($field_id, $search_string, &$values) {
    // Ensure the custom field exists. If it doesn't, API contact.get will return
    // ALL contacts, so in that case just do nothing and return.
    $count = civicrm_api3('customField', 'getCount', array(
      'id' => $field_id,
    ));
    if (!$count) {
      return;
    }

    // Figure out which columns should display in the results.
    $optionColumns = CRM_Core_OptionGroup::values('contact_autocomplete_options', FALSE, FALSE, FALSE, NULL, 'name');
    $setOptions = CRM_Utils_Array::explodePadded(Civi::settings()->get('contact_autocomplete_options'));
    $setOptionColumns = array(1 => 'sort_name');
    foreach ($setOptions as $setOption) {
      if (!empty($optionColumns[$setOption])) {
        $setOptionColumns[] = $optionColumns[$setOption];
      }
    }

    // Fetch data for contacts who match on this custom field.
    $api_params = array(
      'custom_' . $field_id => array('LIKE' => '%' . $search_string . '%'),
      'sequential' => 1,
      'return' => $setOptionColumns,
    );
    $extra_result = civicrm_api3('contact', 'get', $api_params);

    foreach ($extra_result['values'] as $value) {
      // Define an array of column values to be displayed in results.
      $data = array();
      // Populate that display array with the actual values.
      foreach ($setOptionColumns as $column) {
        // If the column "state_province" should be displayed, display "state_province_name"
        // instead; this is what the contact.getQuick API does, and we want to
        // match that.
        if ($column == 'state_province') {
          $column = 'state_province_name';
        }
        // Only append to the display array if there's actually a value.
        if (!empty($value[$column])) {
          $data[] = $value[$column];
        }
      }
      $values[] = array(
        'id' => $value['id'],
        'sort_name' => $value['sort_name'],
        'email' => $value['email'],
        'data' => implode(' :: ', $data),
      );
    }
  }

  /**
   * If so configured, append the current employer for each returned contact.
   * @param array $values The result values that will be returned by the API.
   */
  private static function appendCurrentEmployerData(&$values) {
    if (Civi::settings()->get('extquicksearch_is_quicksearch_current_employer')) {
      foreach ($values as &$value) {
        $contact = civicrm_api3('Contact', 'getSingle', array(
          'id' => $value['id'],
          'return' => array('current_employer'),
        ));
        $currentEmployer = CRM_Utils_Array::value('current_employer', $contact);
        if ($currentEmployer) {
          $value['data'] .= " :: $currentEmployer";
        }
      }
    }
  }

  private static function removeDuplicateValues(&$values) {
    $tmp = array();
    foreach ($values as $value) {
      $tmp[$value['id']] = $value;
    }
    $values = array_values($tmp);
  }

}
