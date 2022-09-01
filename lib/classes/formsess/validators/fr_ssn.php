<?php
require_once 'Validate/FR.php';
/**
 * Validation callback: checks if the field contains a french social security number
 *
 * @param array    $fields  field to test
 * @param FormSess $fs      FormSess object used for callback
 * @param array    $param   Additionnal parameters
 * @param string   $message Error message
 * @return bool true if the field contains a french social security number, false otherwise
 * @version $Id: fr_ssn.php,v 1.2 2003/05/19 21:44:56 katana Exp $
 */
function fs_validation_callback_fr_ssn($fields, $param, &$fs, $message = false) {
  if (!Validate_FR::ssn($fields[0]['value'])) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} is not a valid credit card number"));
    return false;
  } else {
    return true;
  }
}
?>