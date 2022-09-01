<?php
/**
 * Smarty prefilter: FS Input
 * 
 * Transforms all the <FS:textarea ...>...</fs:textarea> tags to {fs} smarty functions
 * 
 * @package formsess
 * @subpackage smarty_plugins
 */
function _fs_filter_textarea(&$fs_filter) {

  $string = '{fs_textarea';
  
  // store the tag type in the filter
  $fs_filter->_store_tag($fs_filter->_tag_properties['name'], 'textarea');

  foreach ($fs_filter->_tag_properties as $name => $value) {
    $string .= sprintf(' %s="%s"', $name, $value);
  } 
  
  $string .= '}';
  
  return $string;
}
?>