<?php
/**
 * Formsess JSValidation callback: item selected in dropdown
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 *                           - emptyvalue: value considered as empty one
 *                           - count: required number of selected options [default = 1]
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: js.selected.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_selected($fields, $params, &$fs, $message = false) {
  
  $emptyvalue = isset($params['emptyvalue']) ? addslashes($params['emptyvalue']) : '';
  $selcount   = isset($params['count'])      ? $params['count']                  : '1';
  $fname = $fields[0]['name'];
  $type = $fs->get_field_type($fname);
  
  // field type not handled by that validator
  if (($type != 'select') && ($type != 'select_multiple')) {
 		$fs->_trigger_error("[FSValidator:selected]: $fname can not be validated by this validator (type = $type)");
    return false;
  } else {
    $message = str_replace("'", "\\'", ($message ? $message : "you have to select an item in $fname")); 
		$code .= <<< EOF

// Checking for selection on $fname
var opt, value, found = 0, i = 0, oSelect = tf['$fname'];
while (opt = oSelect.options[i++]) {
  if (opt.selected) {
    if (opt.value != '$emptyvalue') found++;
  }
}
if (found < $selcount) {
  alert('$message');
  oSelect.focus();
  return false;
}

EOF;
  } // if (($type != 'select') && ($type != 'select_multiple'))

  return $code;
}
?>
