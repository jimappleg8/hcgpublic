<?php
/**
 * Smarty Function fs_input_hidden
 * Displays a form tag <input type="hidden"
 * 
 * Smarty Params:
 *  string $name field name
 *  string $add  additionnal data to display in the input tag
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_input_hidden.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function smarty_function_fs_input_hidden($params, &$smarty) {

  $fs =& $GLOBALS['__fs_current'];

  // value - if the field was not assigned a value, one is searched in the function parameters
  $name = $params['name']; unset($params['name']);
  if (!$value = htmlentities($fs->get_value($name))) {
    if (!isset($params['value']) || empty($params['value'])) {
      $value = $params['value'];
    }
  }
  if ($value) {
    $value = ' value="' . $value . '" ';
  }
  
  // misc params
  $add = '';
  foreach ($params as $key => $val) {
    $add .= "$key=\"$val\" ";
  }
  
  $fs->setType($name, 'hidden');
  
  echo '<input type="hidden" name="' . $name . '"'. $value . $add . ' />';
  
  return;
}


?>
