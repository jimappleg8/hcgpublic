<?php
/**
 * Formsess JSValidation callback: field not empty
 * Valid for textarea, input_text and input_password  
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: js.empty.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_empty($fields, $params, &$fs, $message = false) {
  
  $fname = $fields[0]['name'];

  // check the field type
  $type = $type = $fs->get_field_type($fname);
  if (($type != 'input_text') && ($type != 'textarea') && ($type != 'input_password')) {
    $fs->_trigger_error("[FSValidator:empty]: $fname can not be validated by this validator (type = $type)");
    return false;
  }
  
  $emptyvalue = isset($params['emptyvalue']) ? str_replace("'", "\\'", $params['emptyvalue']) : '';
  $code = "\n// validation for $fname\n";
  $message = str_replace("'", "\\'", ($message ? $message : "$fname cannot be empty"));
  $string .= <<< EOF

// checks if $fname has been filled
if (tf['$fname'].value == '$emptyvalue') {
  alert('$message');
  tf.$fname.focus();
  return false;
}

EOF;

  return $string;
}
?>
