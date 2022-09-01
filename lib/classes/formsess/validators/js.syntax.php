<?php
/**
 * Formsess JSValidation callback: checks if a field value matches a given syntax
 * Valid for input_text, input_password, textarea
 * 
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 *                           - syntax: name of the syntax to use or a regexp (PCRE)
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: js.syntax.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_syntax($fields, $params, &$fs, $message = false) {
  
  $fname = $fields[0]['name'];
  
  // type check
  $ftype = $fs->get_field_type($fname);
  if (($ftype != 'input_text') && ($ftype != 'input_password') && ($ftype != 'textarea')) {
 		$fs->_trigger_error("[FSValidator:syntax]: $fname can not be validated by this validator (type input_text, input_password or textarea expected, $ftype found)");
    return false;
  }

  $syntax_list = array(
    'email'   => '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/',
    'login'   => '/^[a-z0-9][a-z0-9\_-]+/i',
    'integer' => '/^[0-9]*/',
  );

  // syntax parameter test
  if (!isset($params['syntax'])) {
 		$fs->_trigger_error("[FSValidator:syntax]: You have to select the syntax to validate against");
    return false;
  }
  
  $syntax = $params['syntax'];
  
  // Predefined syntax
  if (isset($syntax_list[$syntax])) {
    $syntax_name = $syntax;
    $syntax = $syntax_list[$syntax_name];

  // syntaxe custom, on commence par tester si on a des délimiteurs au début et à la fin du pattern.
  // si on ne les a pas on les ajoute
  } elseif (($syntax{0} != $syntax{ strlen($syntax) - 1 }) && ($syntax{0} != $syntax{ strlen($syntax) - 2 })) {
    $syntax = '/' . $syntax . '/';
  }

  $message = str_replace("'", "\\'", ($message ? $message : "$fname must be max $params[max] chars long"));

  // JS Code
  $string = <<< EOJ

// syntax validation for $fname
var reg = $syntax;
if ((tf['$fname'].value != '') && (!reg.test(tf['$fname'].value))) {
  alert('$message');
  tf.$fname.focus();
  return false;
}

EOJ;
  return $string;
}
?>
