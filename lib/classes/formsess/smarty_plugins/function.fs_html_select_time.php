<?php
if (!function_exists('smarty_function_html_select_time')) require $this->_get_plugin_filepath('function','html_select_time');


/**
 * Smarty Function fs_html_select_time
 * Displays a Smarty html_select_time field
 * Smarty Params:
 *  string $name field name
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_html_select_time.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function smarty_function_fs_html_select_time($params, &$smarty) {

  $fs =& $GLOBALS['__fs_current'];
  
  $seldate = $fs->get_value($params['name']);
  
  $fs->setType($params['name'], 'time');
  
  $params['time'] = $fs->get_value($params['name']);
  $params['prefix'] = $params['name'];
  unset($params['name']);
  
  return smarty_function_html_select_time($params, $smarty);
}


?>
