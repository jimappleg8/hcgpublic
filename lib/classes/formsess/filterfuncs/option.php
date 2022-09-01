<?php
/**
 * Smarty prefilter: FS Option
 * Transforms all the <FS:option...>...</FS:option> tags to {fs_options} smarty functions
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: option.php,v 1.3 2003/08/12 12:39:32 katana Exp $
 */
function _fs_filter_option(&$fs_filter) {
  
  // opening tag
  if (!$fs_filter->_is_closing_tag) {

    // copy the properties so that we can modify it without affecting the main object
    $properties = $fs_filter->_tag_properties;

    $string = '{fs_option ';
    
    // default select
    if (isset($properties['selected'])) $string .= ' selected="yes"';
    
    foreach ($properties as $name => $value) {
      if (($name == 'selected') && empty($value)) {
        $value = 'yes';
      }
        $string .= sprintf(' %s="%s"', $name, ($value == '') ? '' : $value);
      }
    $string .= "}";
  }
  
  // closing tag
  else {
  }

  return $string;  
}
?>