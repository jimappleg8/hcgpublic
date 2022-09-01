<?php
/**
 * Smarty Function fs_input_errormessage
 * Displays one error message for a field
 * Smarty Params:
 *  string $field field name
 *  string $form  form name
 *  string $index error message index [optionnal, default = 0]
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_errormessage.php,v 1.4 2003/08/12 12:39:32 katana Exp $
 */
function smarty_function_fs_errormessage($params, &$smarty) {
  $formname = (isset($params['form']) ? ('fs_' . $params['form']) : $formname = '__fs_current'); 
  
  $fs =& $GLOBALS[$formname];
  
  $index = isset($params['index']) ? $params['index'] : 0;
  
  $errors = $fs->get_errors($params['field']);
  if ($errors && isset($errors[$index])) {
    return "{$errors[$index]['message']}<br />";    
  }
  
  return;
}


?>
