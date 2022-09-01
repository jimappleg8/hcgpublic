<?php
/**
 * Formsess Validation callback: checkbox checked
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 * @param string   $message Error message used in place of the default one
 * @return bool true if the field passed the validation, false otherwise
 * @version $Id: checked.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_validation_callback_checked($fields, $params, &$fs, $message = false) {
  if ($fields[0]['value'] == '') {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} has to be checked"));
    return false;
  } else {
    return true;
  }
}
?>
