<?php
if (!function_exists('smarty_function_html_options')) require $this->_get_plugin_filepath('function','html_options');


/**
 * Smarty Function fs_options
 * Displays a list of <option...> tags with the correct value selected
 * Smarty Params:
 *   string $name field name
 *   string $add  additionnal data to display in the input tag
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_options.php,v 1.2 2003/04/20 10:46:22 katana Exp $
 */
function smarty_function_fs_options($params, &$smarty) {
	$fs = &$GLOBALS['__fs_current'];
  $newparams = array();
  
  // selected item
  // @todo replacing this with only one call to $fs->get_value should be possible, assuming the typing of select has been implemented
  if (!$newparams['selected'] = $fs->get_value($fs->current_select)) {
    if (isset($params['selected']) && !empty($params['selected'])) {
      if ($fs->_current_select_ismultiple) {
        $newparams['selected'] = explode('|', $params['selected']);
      } else {
        $newparams['selected'] = $params['selected'];
      }
    } // if (isset($params['selected']))
  } // if (!$selected = $fs->get_value($fs->current_select))
  

	// values + output
	if (isset($params['values']) && isset($params['output'])) {
		$newparams['values'] = $params['values'];
		$newparams['output'] = $params['output'];
	} // if (isset($params['values']) && isset($params['output']))


  // options
  elseif (isset($params['options'])) {
		$newparams['options'] = $params['options'];
	} // elseif (isset($params['options']))
	
  return smarty_function_html_options($newparams, $smarty);
}
?>
