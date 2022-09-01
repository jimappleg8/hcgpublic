<?php
/**
 * Smarty Block Function FS Start
 * Starts up a form session block
 * 
 * Smarty Params:
 *  string $name   formsession name
 *  string $method data method, POST or GET
 *
 * @package formsess
 * @subpackage smarty_plugins
 */
function smarty_block_fs_form($params, $content, &$smarty) {

		// in the opening tag, set the current fs to the given one
		if ($content == null) {
			extract($params);

			if (!isset($GLOBALS["__fs_$name"])) {
				$smarty->trigger_error("fs: no form session object for '$name' found");
				return;
			}
			$GLOBALS['__fs_current'] =& $GLOBALS["__fs_$name"]; // reference
			$GLOBALS['__fs_name']    = $name;                   // name


		// closing tag, delete the globals
		} else {
			unset($GLOBALS["__fs_current"]); // reference
			unset($GLOBALS['__fs_name']);    // name
			echo $content;
		}

    return;
}


?>
