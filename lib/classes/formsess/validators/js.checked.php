<?php
/**
 * Formsess JSValidation callback: checks if a checkbox is checked
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: js.checked.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_checked($fields, $params, &$fs, $message = false) {
  
  $fname = $fields[0]['name'];
  $type  = $fs->get_field_type($fname); 
  if (($type != 'input_checkbox') && ($type != 'input_radio')) {
 		$fs->_trigger_error("[FSValidator:checked]: $fname can not be validated by this validator (type input_checkbox or input_radio expected, \"$type\" found)");
    return false;
  }

  $message = str_replace("'", "\\'", ($message ? $message : "$fname has to be checked"));
  $emptyvalue = isset($params['emptyvalue']) ? $params['emptyvalue'] : '';

  $string = <<< EOJ

// checks if $fname is checked
if (tf['$fname'].value == '$emptyvalue') {
  alert('$message');
  return false;
}

EOJ;
  return $string;
}
?>
