<?php
/**
 * Formsess Validation callback: checks if an uploaded file has a valid extension
 * Valid for input_file
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 *                           - extensions: comma separated list of extensions (jpg,png,bmp)
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: hasextension.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_validation_callback_hasextension($fields, $params, &$fs, $message = false) {
  $fname = $fields[0]['name'];
  $type = $fs->get_type($fname);
  if ($type != 'input_file') {
 		$fs->_trigger_error("[FSValidator:hasExtension]: $fname can not be validated by this validator (type = $type, input_file expected)");
    return false;
  }
  
  if (!isset($params['extensions'])) {
 		$fs->_trigger_error("[FSValidator:hasExtension]: You need to provide at least one extension");
    return false;
  }
  
  if (!isset($fields[0]['value']['name']) or ($fields[0]['value']['name'] == '')) {
    return;
  }
  $regexp = '/' . str_replace(',', '|', $params['extensions']) . '$/i';
  if (!preg_match($regexp, $fields[0]['value']['name'])) {
    $fs->trigger_error($fields[0]['name'], ($message ? $message : "File extension for $fname has to be one of " . $params['extensions']));
    return false;
  }
  return true;
}
?>
