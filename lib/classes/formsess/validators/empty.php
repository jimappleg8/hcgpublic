<?php
/**
 * Formsess Validation callback: field not empty
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 * @param string   $message Error message used in place of the default one
 * 
 * @return bool true if the field passed the validation, false otherwise
 * @version $Id: empty.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function fs_validation_callback_empty($fields, $params, &$fs, $message = false) {
  
  $emptyvalue = isset($params['emptyvalue']) ? $params['emptyvalue'] : '';
  
  if ($fields[0]['value'] == $emptyvalue) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} cannot be empty"));
    return false;
  } else {
    return true;
  }
}
?>