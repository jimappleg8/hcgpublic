<?php
/**
 * Smarty prefilter: FS Validate
 * Handles validators from templates.
 * Stores the validation request for further usage for the prefilter class,
 * and adds the smarty function so that the formsess object can store the server side validation request
 *
 * @package formsess
 * @subpackage smarty_plugins
 * @version $Id: validate.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function _fs_filter_validate(&$fs_filter) {
  $string = '';

  $fs_filter->_validators[] = array_merge(
    $fs_filter->_tag_properties,
    array(
      'line' => $fs_filter->_get_line_number(),
      'tag'  => $fs_filter->_tag_name
    )
  );

  return $string;
}
?>