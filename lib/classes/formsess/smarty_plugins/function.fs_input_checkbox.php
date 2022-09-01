<?php
/**
 * Smarty Function fs_input_checkbox
 * Displays a form tag <input type="checkbox"
 * 
 * Smarty Params:
 *  string $name field name
 *  string $add  additionnal data to display in the input tag
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: function.fs_input_checkbox.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function smarty_function_fs_input_checkbox($params, &$smarty) {
		$fs =& $GLOBALS['__fs_current'];
    
    $name = $params['name']; unset($params['name']);
		if (isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    }
    // cb_offset: the getname is the name without the offset within the brackets at the end
    if (isset($params['cb_offset'])) {
      $getname = substr($name, 0, (strlen($name) - 2 - strlen($params['cb_offset'])));
      $cb_offset = $params['cb_offset'];
      unset($params['cb_offset']);
    }

    // undef index: the getname is the name without the empty brackets at the end
    if (isset($params['undef_index'])) {
      $getname = substr($name, 0, strlen($name) - 2);
      $undef_index = true;
    }
    
    if (!isset($getname)) {
      $getname = $name;
    }
    $item_value = $fs->get_value($getname);
    
    $checked = '';
    if (is_array($item_value)) {
      if (isset($undef_index)) {

        // the value parameter is mandatory if the checkbox' name is an undefined array (e.g. [])
        if (!isset($value)) {
          return "[fs:checkbox] if you want an undefined array, give a value !";
        }

        // loop over every value in the array in order to find the one for this checkbox
        for ($i = 0, $count = count($item_value); $i < $count; $i++) {
          if ($item_value[$i] == $value) {
            $checked = ' checked="" ';
          }
        }

      } // if (substr($name, -1, 2) == '[]')
      else {
        if ($item_value[$cb_offset] == $value) {
          $checked = ' checked="" ';
        }
      } // if (substr($name, -1, 2) == '[]')
    } // if (is_array($item_value))
    else {
      if ((isset($value) && ($item_value == $value)) or (!isset($value) && $item_value == 'on')) {
        $checked = ' checked="" ';
      }
    } // if (is_array($item_value))


    $value = isset($value) ? ('value="' . $value . '" ') : '';


    // additionnal items
    $add = '';
		foreach ($params as $key => $val) {
			$add .= "$key=\"$val\" ";
		}
    
    $fs->setType($name, 'checkbox');
    
    echo '<input type="checkbox" name="' . $name . '" ' . $value . $checked . $add . ' />';
    return;
}
?>
