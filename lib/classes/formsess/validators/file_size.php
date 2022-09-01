<?php
/**
 * Validation callback: checks if a file size fits a given range
 * Valid for input_file
 *
 * @param array    $field  field name
 * @param array    $params Additionnal parameters
 *   - int $params[max] max size for the file (in bytes)
 *   - int $params[min] min size for the file (in bytes)
 * at least one of min or max has to be set.
 * k or m can be prepended to the size in order to define the size in kilo or megabytes
 * @param FormSess $fs FormSess object used for callback
 * @param $message error message
 * @return bool true if the file fits in the give size, false otherwise
 * @version $Id: file_size.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function fs_validation_callback_file_size($fields, $params, &$fs, $message = false) {

  $fname  =  $fields[0]['name'];
  $fvalue =& $fields[0]['value'];
  
  // check the field type
  $type = $fs->get_type($fname);
  if ($type != 'input_file') {
 		$fs->_trigger_error("[FSValidator:file_size]: $fname can not be validated by this validator (type = $type, input_file expected)");
    return false;
  }
  
  // make sure we have at least one parameter
  if (!isset($params['min']) && !isset($params['max'])) {
    $fs->_trigger_error('[FSValidator:file_size] You have to specify one of min or max file size');
    return true;
  }
  
  // check if the parameters are both valid
  if (isset($params['min']) && !preg_match('/^([0-9]+)(k|m)?/i', $params['min'], $m)) {
    $fs->_trigger_error('[FSValidator:file_size] The given min size parameter is not valid');
    return true;
  } elseif (isset($params['min'])) {
    $params['min'] = getsize($m);   
  }
  if (isset($params['max']) && !preg_match('/^([0-9]+)(k|m)?/i', $params['max'], $m)) {
    $fs->_trigger_error('[FSValidator:file_size] The given max size parameter is not valid');
    return true;
  } elseif (isset($params['max'])) {
    $params['max'] = getsize($m);   
  }

  // min file size
  if (isset($params['min'])) {
    if ($fvalue['size'] < $params['min']) {
      $fs->trigger_error($fname, ($message ? $message : "$fname is under the min file size ({$params['min']} bytes)"));
      return false;
    }
  }
  
  // max file size
  if (isset($params['max'])) {
    if ($fvalue['size'] > $params['max']) {
      $fs->trigger_error($fname, ($message ? $message : "$fname is over the max file size ({$params['max']} bytes)"));
      return false;
    }
  }
  
  return true;
}

/**
 * Computes the size parameter given the array returned by preg_match
 * 
 * @param array $p array returned by preg_match (1 => digit, 2 => k/m)
 * @return int the size 
 **/
function getsize($p) {
  if (isset($p[2])) {
    if ($p[2] == 'k') {
      $multiplier = 1024;
    } else {
      $multiplier = 1048576;
    }
  } else {
    $multiplier = 1;
  }
  
  return ($p[1] * $multiplier);
} 
?>
