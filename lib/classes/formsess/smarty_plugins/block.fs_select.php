<?php
/**
 * Smarty Function FS Select
 * Displays a form tag <input type="text"
 * Smarty Params:
 *   - string $name     field name
 *   - string $selected default selected field(s)
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: block.fs_select.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function smarty_block_fs_select($params, $content, &$smarty) {

    $fs =& $GLOBALS['__fs_current'];

		// opening the tag: this select becomes the current one, on top of the stack
    if ($content == null) {
      $params['name'] = str_replace('[]', '', $params['name']);
      $fs->setType($params['name'], 'select');
      $ct = array_push($fs->_select_stack, $params['name']);
      $fs->current_select =& $fs->_select_stack[ $ct - 1 ];

      // @todo add methods to access the data
      $fs->_current_select_ismultiple = isset($params['multiple']) ? true : false; 
    } // if ($content == null)


    // closing tag, delete this select from the stack
    else {
      array_pop($fs->_select_stack);
      if (count($fs->_select_stack)) {
        $fs->current_select =& $fs->_select_stack[ count($fs->_select_stack) - 1 ];
      } else {
        $fs->current_select = false;
      }
      echo $content;
    } // else: if ($content == null)


    return;
}


?>
