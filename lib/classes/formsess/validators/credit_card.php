<?php
include_once 'Validate.php';
/**
 * Validation callback: checks if the field contains a credit card number
 *
 * @param array    $fields list of field names / values to test
 * @param FormSess $fs     FormSess object used for callback
 * @param array    $param  Additionnal parameters
 * @param string   $message optionnal error message used in place of the default one
 * @return bool true if the field's value is a CCN, false otherwise
 * @version $Id: credit_card.php,v 1.2 2003/05/19 20:12:16 katana Exp $
 */
function fs_validation_callback_credit_card($fields, $param, &$fs, $message = false) {
  if (!Validate::creditCard($fields[0]['value'])) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} is not a valid credit card number"));
    return false;
  } else {
    return true;
  }
}
?>