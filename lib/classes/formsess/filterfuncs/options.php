<?php
/**
 * Smarty prefilter: FS Options
 * Transforms all the <FS:options.../> tags to {fs_options} smarty functions
 * 
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: options.php,v 1.2 2003/05/16 12:11:37 katana Exp $
 */
function _fs_filter_options(&$fs_filter) {

  // copy the properties so that we can modify it without affecting the main object
  $properties = $fs_filter->_tag_properties;

  $string = '{fs_options';
  
  // the select string itself
  if (isset($properties['options']) && !empty($properties['options'])) {
    if (isset($properties['values'])) unset($properties['values']);
    if (isset($properties['output'])) unset($properties['output']);
    $string .= ' options=' . $properties['options'];
    unset($properties['options']);
  }
  
  // values + output
  else {
    if (isset($properties['options'])) unset($properties['options']);
    $string .= sprintf(' values=%s output=%s', $properties['values'], $properties['output']);
    unset($properties['values']); unset($properties['output']);
  }
  
  //this code handles the "selected" property 
  foreach ($properties as $name => $value) {
    $string .= sprintf(' %s="%s"', $name, (empty($value) ? '' : $value));
  }
  $string .= "}";
  
  return $string;
}
?>