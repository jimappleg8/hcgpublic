<?php
/**
 * Smarty Function fs_input_text
 * Displays a form tag <input type="text"
 * Smarty Params:
 *   string $name field name
 *   string $add  additionnal data to display in the input tag
 * 
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_input_text.php,v 1.1.1.1 2003/04/20 09:42:17 katana Exp $
 */
function smarty_function_fs_input_text($params, &$smarty) {
  $fs = &$GLOBALS['__fs_current']; 

  // value - if the field was not assigned a value, one is searched in the function parameters
  $name = $params['name'];
  unset($params['name']);
  if (!$value = htmlentities($fs->get_value($name))) {
    if (!isset($params['value']) || empty($params['value'])) {
      $value = $params['value'];
    } 
  } 
  if ($value) {
    $value = ' value="' . $value . '" ';
  } 

  // additionnal parmeters
  $add = '';
  foreach ($params as $key => $val) {
    $add .= " $key=\"$val\"";
  } 

  echo '<input type="text" name="' . $name . '"' . $value . $add . ' />'; 

  // record the field type
  $fs->setType($name, 'text');

  return;
} 

?>
