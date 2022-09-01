<?php
/**
 * Formsess JSValidation confirm: displays a confirm box with a messages
 *
 * @param array    $fields  field to test
 * @param formsess $fs      FormSess object handling the form
 * @param array    $params  Parameters sent to the callback
 * @param string   $message Error message used in place of the default one
 * @return void
 * @version $Id: js.confirm.php,v 1.2 2003/08/12 12:39:32 katana Exp $
 */
function fs_jsvalidation_callback_confirm($fields, $params, &$fs, $message = false) {
  
  $message = str_replace("'", "\\'", ($message ? $message : "$fname cannot be empty"));
  $string .= <<< EOF

// confirmation message
if (!confirm('$message')) {
  return false;
}

EOF;

  return $string;
}
?>
