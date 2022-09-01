<?php
/**
 * Validator function file: Syntax
 * 
 * @author Katana <katana@katana-inc.com>
 **/ 

/**
 * Validation callback: the given field has to match a PCRE pattern
 *
 * @param array    $fields list of field names / values to test
 * @param FormSess $fs     FormSess object used for callback
 * @param array    $param  Additionnal parameters
 * @return void
 * @version $id$
 */
function fs_validation_callback_syntax($fields, $param, &$fs, $message = false) {
  $fname  = $fields[0]['name'];
  $fvalue = $fields[0]['value'];

  $syntax_list = array(
    'email'   => '/^[a-z0-9]+([_.-][a-z0-9]+)*@([a-z0-9]+([.-][a-z0-9]+)*)+\.[a-z]{2,4}$/i',
    'login'   => '/^[a-z0-9][a-z0-9\_-]+/i',
    'integer' => '/^[0-9]*/',
  );

  // syntax parameter test
  if (!isset($param['syntax'])) {
    return false;
  }

  $syntax = $param['syntax'];

  // Predefined syntax
  if (isset($syntax_list[$param['syntax']])) {
    $syntax_name = $param['syntax'];
    $syntax = $syntax_list[$syntax];

  // syntaxe custom, on commence par tester si on a des délimiteurs au début et à la fin du pattern.
  // si on ne les a pas on les ajoute
  } elseif (($syntax{0} != $syntax{ strlen($syntax) - 1 }) && ($syntax{0} != $syntax{ strlen($syntax) - 2 })) {
    $syntax = '/' . $syntax . '/';
  }
  
  // Test PCRE sur la syntaxe, et retour en fonction
  if (($fvalue != '') && !preg_match($syntax, $fvalue)) {
    $fs->trigger_error($fname, ($message ? $message : "$fname didn't match the given syntax"));
    return false;
  } else {
    return true;
  }
}

?>