<?php
/**
 * Smarty prefilter include: ifErrormessage
 * Transforms all the <FS:ifErrormessage .../> tags to {if} smarty tags
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: iferrormessage.php,v 1.1 2003/05/11 20:46:20 katana Exp $
 */
function _fs_filter_iferrormessage(&$fs_filter) {

  // opening tag
  if (!$fs_filter->_is_closing_tag) {
    $string = sprintf('{php}if ($GLOBALS[\'__fs_current\']->get_errors(\'%s\')) : {/php}', $fs_filter->_tag_properties['field']);
  }
  
  // closing tag
  else {
    $string = '{php}endif;{/php}';
  }

  return $string;
}
?>