<?php
/**
 * Formsess Validation callback: compares X fields to check if they have the same value
 *
 * @param array  $fields fields to compare
 * @param array  $param  optionnal parameters (useless in that callback)
 * @param string $message Error message triggered when the validation is not passed
 * @return bool true if all the fields are identical, false otherwise
 * @version $Id: identical.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function fs_validation_callback_identical($fields, $params, &$fs, $message = false) {

  // we can't compare less than 2 fields
  if (!is_array($fields) or (count($fields) < 2)) {
    $fs->_trigger_error("[FSValidator:identical]: two fields are needed to run the validator");
    return true;
  }

  // We just compare every field to the first one
  for ($i = 1, $count =  count($fields); $i < $count; $i++) {

    // different
    if ($fields[$i]['value'] != $fields[0]['value']) {
      $fs->trigger_error($fields[$i]['name'], ($message ? $message : "{$fields[$i]['name']} has to be identical to {$fields[0]['name']}"));
      return false;
    }
  }
  
  return true;
}
?>

