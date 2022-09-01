<?php
/**
 * Smarty prefilter: FS HTML Select Time
 * Transforms a <fs:html_select_time ... /> tag to a {fs_html_select_date ...} function
 * 
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: html_select_time.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function _fs_filter_html_select_time(&$fs_filter) {

  // store the tag type in the filter
  $fs_filter->_store_tag($fs_filter->_tag_properties['name'], 'html_select_time');

  $string = '{fs_html_select_time';
  foreach ($fs_filter->_tag_properties as $name => $value) {
    if (!$value or ($value == 'false')) {
      $format = ' %s=%s';
    } else {
      $format = ' %s="%s"';
    }
    $string .= sprintf($format, $name, $value);
  }
  
  $string .= "}\n";
  
  return $string;
}
?>