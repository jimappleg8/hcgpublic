<?php
/**
 * Smarty Function fs_input_text
 * Displays a form tag <textarea name="..." ...>...</textarea>
 * 
 * Smarty Params:
 *  string $name field name
 *  string $add  additionnal data to display in the input tag
 *
 * @package formsess
 * @subpackage smarty_plugins
 */
function smarty_function_fs_textarea($params, &$smarty) {

		$fs =& $GLOBALS['__fs_current'];

    $name = $params['name']; unset($params['name']);
		$add = '';
		foreach ($params as $key => $val) {
			$add .= "$key=\"$val\" ";
		}

    echo '<textarea name="' . $name . '" ' . $add . '>' . htmlentities($fs->get_value($name)) . '</textarea>';
    
    $fs->setType($name, 'textarea');

    return;
}
?>
