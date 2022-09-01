<?php
include_once 'Validate.php';
/**
 * Validation callback: checks if the field contains a valid date
 *
 * @param array    $fields  list of field names / values to test
 * @param FormSess $fs      FormSess object used for callback
 * @param array    $param   Additionnal parameters
 * @param string   $message Optionnal error message to replace the default one
 * @return void
 * @version $Id: date.php,v 1.2 2003/05/19 20:29:26 katana Exp $
 */
function fs_validation_callback_date($fields, $format, &$fs, $message = false) {
  if (!Validate::date($fields[0]['value'], $format)) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} is not a valid date"));
    return false;
  } else {
    return true;
  }
}
?>
