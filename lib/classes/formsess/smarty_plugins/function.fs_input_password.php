<?php
/**
 * Smarty Function fs_input_password
 * Displays a form tag <input type="password"
 * 
 * Smarty Params:
 *  string $name field name
 *  string $add  additionnal data to display in the input tag
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_input_password.php,v 1.2 2003/04/20 10:43:26 katana Exp $
 */
function smarty_function_fs_input_password($params, &$smarty) {
		$fs =& $GLOBALS['__fs_current'];

    $name = $params['name']; unset($params['name']);
    $add = '';
    foreach ($params as $key => $val) {
			$add .= "$key=\"$val\" ";
		}
    
    // value
    if ($value = $fs->get_value($name)) {
      $value = 'value="' . $value . '" ';
    }    
    
    echo '<input type="password" name="' . $name . '" ' . $value. $add . ' />';

    $fs->setType($name, 'password');
    
    return;
}


?>
