<?php
if (!function_exists('smarty_function_html_options')) require $this->_get_plugin_filepath('function','html_options');


/**
 * Smarty Function fs_options
 * Displays a list of <option...> tags with the correct value selected
 * Smarty Params:
 *  string $name field name
 *  string $add  additionnal data to display in the input tag
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_option.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function smarty_function_fs_option($params, &$smarty) {

  $fs =& $GLOBALS['__fs_current'];
  $newparams = array();
  
  // selected item
  if (!$newparams['selected'] = $fs->get_value($fs->current_select)) {
    if (isset($params['selected'])) {
      $newparams['selected'] = $params['value'];
    }
  }
  
  // values + output
  $newparams['values'] = array($params['value']);
  $newparams['output'] = array($params['output']);
  
  return smarty_function_html_options($newparams, $smarty);
}


?>
