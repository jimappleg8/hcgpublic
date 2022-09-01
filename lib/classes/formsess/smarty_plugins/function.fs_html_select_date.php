<?php
if (!function_exists('smarty_function_html_select_date')) require $this->_get_plugin_filepath('function','html_select_date');


/**
 * Smarty Function fs_html_select_date
 * Displays a html_select_date field
 * Smarty Params:
 *  string $name field name
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_html_select_date.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function smarty_function_fs_html_select_date($params, &$smarty) {

  $fs =& $GLOBALS['__fs_current'];
  
  $seldate = $fs->get_value($params['name']);
  
  $fs->setType($params['name'], 'date');
  
  $params['time'] = $fs->get_value($params['name']);
  $params['prefix'] = $params['name'];
  unset($params['name']);
  
  return smarty_function_html_select_date($params, $smarty);
}


?>
