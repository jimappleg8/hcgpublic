<?php
/**
 * Smarty prefilter: FS HTML Select Date
 * Transforms a <fs:htmlSelectDate ... /> tag to a {fs_html_select_date ...} function
 * 
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: html_select_date.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function _fs_filter_html_select_date(&$fs_filter) {

  // store the tag type in the filter
  $fs_filter->_store_tag($fs_filter->_tag_properties['name'], 'html_select_date');

  $string = '{fs_html_select_date';
  foreach ($fs_filter->_tag_properties as $name => $value) {
    if (!$value or ($value == 'false')) {
      $format = ' %s=%s';
    } else {
      $format = ' %s="%s"';
    }
    $string .= sprintf(' %s="%s"', $name, $value);
  }
  $string .= "}\n";
  
  return $string;
}
?>