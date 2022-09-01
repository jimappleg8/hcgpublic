<?php
/**
 * Smarty Function fs_input_radio
 * Displays a form tag <input type="radio"
 * Smarty Params:
 *  string $name field name
 *  string $add  additionnal data to display in the input tag
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_input_radio.php,v 1.3 2003/05/19 15:49:41 katana Exp $
 **/
function smarty_function_fs_input_radio($params, &$smarty) {
    $fs =& $GLOBALS['__fs_current'];
    $name  = $params['name'];  unset($params['name']);
    $value = $params['value']; unset($params['value']);
		$add = '';
		foreach ($params as $key => $val) {
			$add .= "$key=\"$val\" ";
		}


    // handle checked state: if the posted value == the radio's value,
    // or if the checked parameter is set from the template
    if (!$posted_value = $fs->get_value($name)) {
      if (isset($params['checked'])) {
        $selected = 'checked="" ';
      }
    } elseif ($posted_value == $value) {
      $selected = 'checked="" ';
    } else {
      $selected = '';
    }

    echo '<input type="radio" name="' . $name . '" value="' . $value . '" ' . $selected . $add . ' />';
    
    $fs->setType($name, 'radio');
    
    return;
}
?>
