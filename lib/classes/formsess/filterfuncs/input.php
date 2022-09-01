<?php
/**
 * Smarty prefilter: FS Input
 * Transforms all the <FS:input .../> (but not input file) tags to {fs} smarty functions
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: input.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function _fs_filter_input(&$fs_filter) {

  // copy the properties so that we can modify it without affecting the main object
  $properties = $fs_filter->_tag_properties;

  // store the tag type in the filter
  $fs_filter->_store_tag($properties['name'], 'input_' . $properties['type']);

  // the value is mandatory for a radio button
  if (($properties['type'] == 'radio') && (!isset($properties['value']) || ($properties['value'] == ''))) {
    $fs_filter->_trigger_error("The value is mandatory for a <fs:input type=\"radio\"> tag", "input_radio");
    return false;
  }
  
  // type - Special case for the checkbox (the value is mandatory for an array syntax, or it is optionnal)  
  elseif ($properties['type'] == 'checkbox') {
    if (preg_match('!\[(.+)\]$!', $fs_filter->_tag_name, $m_offset)) {
      $properties['cb_offset'] = $m_offset[1];
    } elseif (substr($fs_filter->_tag_name, -2) == '[]') {
      $properties['undef_index'] = "true";
    }
  }
  
  // use the tag properties to generate the function call
	$string = sprintf('{fs_input_%s name="%s"', $properties['type'], $properties['name']);
	unset($properties['type'], $properties['name']);
  foreach ($properties as $name => $value) {
  	$string .= sprintf(' %s="%s"', $name, $value);
	}
	$string .= '}';
  
  return $string;
}
?>