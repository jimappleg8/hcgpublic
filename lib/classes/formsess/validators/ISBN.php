<?php
include_once 'Validate.php';
/**
 * Validation callback: checks if the field contains a valid ISBN
 *
 * @param array    $fields  list of field names / values to test
 * @param FormSess $fs      FormSess object used for callback
 * @param array    $param   Additionnal parameters
 * @param string   $message Error message
 * @return bool true if the field contains a valid ISBN, false otherwise
 * @version $Id: ISBN.php,v 1.2 2003/05/19 21:17:53 katana Exp $
 */
function fs_validation_callback_ISBN($fields, $format, &$fs, $message = false) {
  if (!Validate::ISBN($fields[0]['value'])) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} is not a valid ISBN"));
    return false;
  } else {
    return true;
  }
}
?>