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
    dsm($values, 'values on ' . __LINE__);
    
    $search_context = $apiRequest['params']['field_name'] ?: 'default';
    $fields = _extquicksearch_get_search_config();
    dsm($fields, '$fields');
    foreach ($fields as $field_id => $contexts) {
      if (in_array($search_context, $contexts)) {
        self::appendResultValues($field_id, $apiRequest['params']['name'], $values);
      }
    }
    dsm($values, 'values on ' . __LINE__);
    self::removeDuplicateValues($values);
    dsm($values, 'values on ' . __LINE__);
    self::sortValues($values);
    dsm($values, 'values on ' . __LINE__);
    self::limitValues($values);
    dsm($values, 'values on ' . __LINE__);

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
    dsm(__FUNCTION__);
    $search_autocomplete_count = CRM_Core_BAO_Setting::getItem(NULL, 'search_autocomplete_count');
    dsm($search_autocomplete_count, '$search_autocomplete_count');
    $values = array_slice($values, 0, $search_autocomplete_count);
  }

  private static function appendResultValues($field_id, $search_string, &$values) {
    $api_params = array(
      'custom_' . $field_id => array('LIKE' => '%'. $search_string .'%'),
      'sequential' => 1,
      'return' => array("sort_name", "email"),
    );
    $extra_result = civicrm_api3('contact', 'get', $api_params);
    foreach ($extra_result['values'] as $value) {
      $data = array(
        $value['sort_name'],
        $value['email'],
      );
      $values[] = array(
        'id' => $value['id'],
        'sort_name' => $value['sort_name'],
        'email' => $value['email'],
        'data' => implode(' :: ', $data),
      );
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
