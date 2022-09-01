<?php
/**
 * Smarty prefilter: FS Input
 * Transforms all the <FS:select ...>...</fs:select> tags to {fs_select} smarty functions
 * 
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: select.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function _fs_filter_select(&$fs_filter) {
  
  // opening tag
  if (!$fs_filter->_is_closing_tag) {

    // copy the properties so that we can modify it without affecting the main object
    $properties = $fs_filter->_tag_properties;
  
    // store the tag type in the filter
    $fs_filter->_store_tag($properties['name'], isset($properties['multiple']) ? 'select_multiple' : 'select');

    // the select string itself
    $string = '<select ';
  
    $fsname = $properties['name'];
    if (isset($properties['multiple'])) {
      $string .= ' multiple="yes"';
      unset($properties['multiple']);
    } else {
      $string .= '';
    }
  
    foreach ($properties as $name => $value) {
      $string .= sprintf(' %s="%s"', $name, $value);
    } 
    $string .= ">\n"; 
    // The FS function
    $string .= sprintf('{fs_select name="%s" %s}', $fsname, $multiple);
  }
  
  // closing tag
  else {
    $string = "{/fs_select}\n</select>";
  }
  
  return $string;
}
?>