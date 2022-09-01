<?php
require_once 'Validate.php';
/**
 * Validation callback: checks if the field contains a valid url
 *
 * @param array $fields  field names / values to test
 * @param array $params  Additionnal parameters
 *   - bool $params[check_domain] if true, the domain in the URL will be cheked. 
 * @param FormSess $fs      FormSess object used for callback
 * @param string   $message error message
 * @return bool true if the given field contains an URL, false otherwise
 * @version $Id: url.php,v 1.2 2003/05/19 22:02:37 katana Exp $
 */
function fs_validation_callback_url($field, $params &$fs, $message = false) {
  $check_domain = (isset($params['check_domain']) && $params['check_domain']) ? true : false;
  if (!Validate::url($fields[0]['value'], $check_domain)) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} is not a valid URL"));
    return false;
  } else {
    return true;
  }
}
?>
