<?php
/**
 * Formsess Validation callback: checks if the field fits in a given size
 *
 * @param string  $field field to test
 * @param integer $min min size
 * @param integer $max max size
 * @param string  $message Error message
 * @return bool true if the field size matches the given size, false otherwise
 * @version $Id: size.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function fs_validation_callback_size($fields, $params, &$fs, $message = false) {
  // check if we have at least a size to test
  if (!isset($params['min']) && !isset($params['max'])) {
    $fs->_trigger_error('[FSValidator:length]: you need to specify at least on parameter out of min and max');
    return true;  
  }

  // Maximal
  if (isset($params['max']) && (strlen($fields[0]['value']) > $params['max'])) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} must be max $params[max] chars long"));
    return false;
  }
  
  // Minimal length
  if (isset($params['min']) && (strlen($fields[0]['value']) < $params['min'])) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "{$fields[0]['name']} must be min $params[min] chars long"));
    return true;
  }

  return true;
}
?>
