<?php
/**
 * Formsess JSValidation callback: checks if an uploaded file has a valid extension
 * Valid for input_file
 * 
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 *                           - extensions: comma separated list of extensions (jpg,png,bmp)
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: js.hasextension.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_hasextension($fields, $params, &$fs, $message = false) {
  
  if (!isset($params['extensions'])) {
 		$fs->_trigger_error("[FSValidator:hasExtension]: You need to provide at least one extension");
    return false;
  }

  $fname = $fields[0]['name'];
  if ($fs->get_field_type($fname) != 'input_file') {
 		$fs->_trigger_error("[FSValidator:hasExtension]: $fname can not be validated by this validator (type input_file expected)");
    return false;
  }

  $regexp = '(' . str_replace(',', '|', $params['extensions']) . ')';
  $message = $message ? str_replace("'", "\\'", $message) : "File extension for $fname has to be one of " . $params['extensions'];

  // empty value
  $string = <<< EOJ

// extension validation for $fname
var reg = new RegExp("$regexp");
if ((tf['$fname'].value != '') && (!reg.test(tf['$fname'].value))) {
  alert('$message');
  tf.$fname.focus();
  return false;
}

EOJ;
  return $string;
}
?>
