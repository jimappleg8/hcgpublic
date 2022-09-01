<?php
/**
 * Formsess Validation callback: item selected in dropdown
 * Valid for select, select_multiple
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 *                           - emptyvalue: value considered as empty one
 *                           - count: required number of selected options [default = 1]
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: selected.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_validation_callback_selected($fields, $params, &$fs, $message = false) {
  $emptyvalue = isset($params['emptyvalue']) ? $params['emptyvalue'] : '';
  $selcount   = isset($params['count'])      ? $params['count']      : 1;
  
  $fname = $fields[0]['name'];
  $type = $fs->getType($fname);
  if (($type != 'select') && ($type != 'select_multiple')) {
 		$fs->_trigger_error("[FSValidator:selected]: $fname can not be validated by this validator (type = $type)");
    return false;
  } else {
    $count = 0;
    if (is_array($fields[0]['value'])) {
      // array (multiple), count the items different from the emptyvalue
      foreach ($fields[0]['value'] as $value) {
        if ($value != $emptyvalue) $count++;
      }
    } else {
      // unique value, just test if it's different from the emptyvalue
      if ($fields[0]['value'] != $emptyvalue) $count++;        
    }
    
    if ($count < $selcount) {
      $fs->trigger_error($fields[0]['name'], ($message ? $message : "$count option(s) has to be selected in {$fields[0]['name']}"));
      return false;
    } else {
      return true;
    }
    
  }
}
?>
