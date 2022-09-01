<?php
/**
 * Smarty prefilter: FS Errormessage
 * Transforms all the <FS:errormessage .../> tags to {fs_errormessage} smarty functions
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: errormessage.php,v 1.1 2003/05/11 20:46:20 katana Exp $
 */
function _fs_filter_errormessage(&$fs_filter) {

  $string = '{fs_errormessage';
  foreach ($fs_filter->_tag_properties as $name => $value) {
    $string .= sprintf(' %s="%s"', $name, $value);
  }
  $string .= '}';

  return $string;
}
?>