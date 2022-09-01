<?php
include_once 'Validate.php';
/**
 * Validation callback: the given field has to be a valid email address
 *
 * @param array    $fields  list of field names / values to test
 * @param FormSess $fs      FormSess object used for callback
 * @param array    $param   Additionnal parameters
 * @param string   $message Optionnal error message
 * @return bool true if the field contains a valid email address, or false
 * @version $Id: email.php,v 1.3 2003/05/19 20:34:30 katana Exp $
 */
function fs_validation_callback_email($fields, $param, &$fs, $message = false) {
  if (!Validate::email($fields[0]['value'])) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} is not a valid email address"));
    return false;
  } else {
    return true;
  }
}
?>