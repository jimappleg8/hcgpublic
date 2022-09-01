<?php
/**
 * Smarty prefilter function: FS Form
 * Handles the echo of a form tag
 *
 * @param fs_filter $fs_filter Formsess filter object
 * 
 * @package formsess
 * @subpackage smarty_plugins
 */
function _fs_filter_form(&$fs_filter) {
  
  // copy the properties so that we can modify it without affecting the main object
  $properties = $fs_filter->_tag_properties;

  // closing form tag
  if ($fs_filter->_is_closing_tag) {
    $line_back = $this->_current_line; // backup the line number     

    // also replace the ||fs_form_<name>|| tag added at the beggining of the form with any required validation
    $fs_filter->_parse_validation();

    // since the tag name and line number is changed during the validation parsing, restore them here
    $fs_filter->_tag_name = 'form';
    $fs_filter->_current_line = $line_back; // backup the line number     
    $string = "</form>\n{/fs_form}";
  }

  // opening form tag  
  else {
    // if the fs object was not created yet, make it now
    $fsname = sprintf('$GLOBALS[\'fs_%s\']', $properties['name']);
    $string  = sprintf("{php}if (!isset(%s)) %s = new formsess('%s');{/php}", $fsname, $fsname, $properties['name']);
    $string .= sprintf("<!--_fs_js_form_%s /-->", $properties['name']);
    $string .= sprintf('{fs_form method="%s" name="%s"}%s', $properties['method'], $properties['name'], "\n");
    $string .= sprintf("<!--_fs_check_%s /-->", $properties['name']);
    $string .= '<form';
    
    // additionnal parameters to <form>
    // add the temp string to the onLoad parameter (if there is one) for JSValidation
    $onSubmitFound = false; $onSubmitString = sprintf('<!--_fs_js_onSubmit_%s /-->', $properties['name']);
    $fs_filter->_form_multipart_formdata = false;
    foreach ($properties as $name => $value) {
      if ($name == 'onsubmit') { // onSubmit string
        $value = $onSubmitString . $value; 
        $onSubmitFound = true;
      }
      elseif (strtolower($name) == 'enctype') { // enctype, in order to check for multipart/form-data if a file field is used
        if (strtolower($value) == 'multipart/form-data') {
          $fs_filter->_form_multipart_formdata = true;
        }
      }
      $string .= sprintf(' %s="%s"', $name, $value);
    }
    if (!$onSubmitFound) $string .= sprintf(' onSubmit="%s"', $onSubmitString); 
    $string .= '>';
  }

  return $string;
}
?>