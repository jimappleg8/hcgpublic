<?php
/**
 * Formsess JSValidation callback: checks if the field fits in a given size
 *
 * @param string  $field field to test
 * @param integer $min min size
 * @param integer $max max size
 * @param string  $message Error message
 * @return bool true if the field size matches the given size, false otherwise
 * @version $Id: js.size.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_size($fields, $params, &$fs, $message = false) {
  $fname = $fields[0]['name'];

  // check the field type. Empty only applies to text and textarea
  $type = $fs->get_field_type($fname);
  if (($type != 'input_text') && ($type != 'textarea')) {
    $fs->_trigger_error("[FSValidator:identical]: $fname can not be validated by this validator (type = $type)");
  } 
  
  // check if we have at least a size to test
  if (!isset($params['min']) && !isset($params['max'])) {
    $fs->_trigger_error('[FSValidator:length]: you need to specify at least on parameter out of min and max');
    return true;  
  }

  $string = ''; 
  // Maximal
  if (isset($params['max'])) {
    $string .= "if (tf['$fname'].value.length > $params[max]) {\n" .
               "  alert('" . str_replace("'", "\\'", ($message ? $message : "$fname must be max $params[max] chars long")) . "');\n" .
               "  tf['$fname'].focus();\n" .
               "  return false;\n" .
               "}\n";
  }
    
  // Minimal length
  if (isset($params['min'])) {
    $string .= "if (tf['$fname'].value.length < $params[min]) {\n" .
               "  alert('" . str_replace("'", "\\'", ($message ? $message : "$fname must be max $params[min] chars long")) . "');\n" .
               "  tf['$fname'].focus();\n" .
               "  return false;\n" .
               "}\n";
  }

  return $string;
}
?>
