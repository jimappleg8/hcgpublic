<?php
/**
 * Formsess JSValidation callback: compares X fields to check if they have the same value
 *
 * @param array  $fields fields to compare
 * @param array  $param  optionnal parameters (useless in that callback)
 * @param string $message Error message triggered when the validation is not passed
 * @return bool true if all the fields are identical, false otherwise
 * @version $Id: js.identical.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_identical($fields, $params, &$fs, $message = false) {

  // we can't compare less than 2 fields
  if (!is_array($fields) or (count($fields) < 2)) {
    $fs->_trigger_error("[FSValidator:identical]: two fields are needed to run the validator");
    return true;
  }

  
  $fname1 = $fields[0]['name'];
  $fname2 = $fields[1]['name'];
  $string = "if (tf['$fname1'].value != tf['$fname2'].value) {\n" .
            "  alert('" . str_replace("'", "\\'", ($message ? $message : "$fname1 is different from $fname2")) . "');\n" .
            "  tf['$fname1'].focus();\n" .
            "  return false;\n" .
            "}\n";
  return $string;
}
?>

