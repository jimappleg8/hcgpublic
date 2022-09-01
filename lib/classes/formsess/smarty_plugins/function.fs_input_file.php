<?php
/**
 * Smarty Function fs_input_text
 * 
 * Displays a form tag <input type="file" with the hidden field for the max file size
 * 
 * Smarty Params:
 *   string $name field name
 *   string $add  additionnal data to display in the input tag
 * 
 * @package formsess
 * @subpackage smarty_plugins
 */
function smarty_function_fs_input_file($params, &$smarty) {
  $fs = &$GLOBALS['__fs_current']; 

  // special params
  $name = $params['name'];
  unset($params['name']);
  if (isset($params['maxfilesize'])) {
    $size = $params['maxfilesize'];
    unset($params['maxfilesize']);
  } 

  // misc params
  $add = '';
  foreach ($params as $key => $val) {
    $add .= "$key=\"$val\" ";
  } 

  $fs->set_type($name, 'input_file');

  if (isset($size)) {
    echo '<input type="hidden" name="MAX_FILE_SIZE" value="' . $size . '" />' . "\n";
  } 
  echo '<input type="file" name="' . $name . '" ' . $add . ' />' . "\n";

  return;
} 

?>
