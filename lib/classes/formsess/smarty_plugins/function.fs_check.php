<?php
/**
 * FSCheck Smarty function
 * Stores on the template processing a future call to a server side validation
 * Smarty Params:
 *  string $field field name
 *  string $check validation
 *  string ... any other parameter used by the validator
 *
 * @author Katana <katana@katana-inc.com>
 * @version $Id: function.fs_check.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 **/
function smarty_function_fs_check($params, &$smarty) {
  $formname = (isset($params['form']) ? ('fs_' . $params['form']) : $formname = '__fs_current'); 
  $fs =& $GLOBALS[$formname];

  $check   = $params['check'];   unset($params['check']);
  $message = $params['message']; unset($params['message']);
  
  // extract the field* params to an array
  $fields = array($params['field']); unset($params['field']);
  foreach($params as $p_name => $p_value) {
    $p_value = str_replace('##FS_CBRCKT##', '}', $p_value);
    $params[$p_name] = $p_value;
    if (preg_match('/^field[0-9]+$/', $p_name)) {
      $fields[] = $p_value;
      unset($params[$p_name]);
    }
  }
  
  $fs->addCheck($check, $fields, $params, $message); 
}
?>