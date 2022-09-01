<?php
/**
 * Project PointComma - Smarty Plugin - pcsmarty_plugin.php
 *
 * The pc lib to be used as a smarty plugin
 *
 * Now that implements:
 * 	-	User Error Message dispaying
 *
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 28 feb 2005
 * @version 0.1
 *
 */

/**
 * Plugin Function to display error
 *
 * It is a "pluginified" version of pcErrorDisplay function
 * Implemented in a tricky way using private function of smarty, could lead to
 * problem in future version of smarty
 *
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 28 feb 2005
 * @version 0.2
 * @package pcErrorHandling
 * @param $params (boolean to display or not in verbose mode)
 * @return display error
 */
function smarty_function_display_error($params, &$smarty) {
global $pcConfig;
require_once($pcConfig['includePath'].$pcConfig['classFolder'].'pcmessage_stack.php');

$stackError = new messageStack('error');

$arrayErrorMsg = array();
$arrayWarningMsg = array();
$arrayMiscMsg = array();

//SORT the ERROR msg from the WARNING message and the others
while ($stackError->size()) {
  $msgError = $stackError->pop();

  if ($msgError[0]==E_USER_ERROR) {
    $arrayErrorMsg[] = $msgError[1];
  }
  elseif ($msgError[0]==E_USER_WARNING) {
    $arrayWarningMsg[] = $msgError[1];
  }
  else {
    $arrayMiscMsg[]= $msgError[1];
  }
}

  //get the verbose params
  $boolVerbose = (isset($params['verbose'])) ? (bool)$params['verbose'] : false;

  //only display the error msg (delete the other)
  if (!$boolVerbose) {
    $arrayWarningMsg= array();
    $arrayMiscMsg= array();
  }

  //display it!
  $smarty->assign('arrayMiscMessage',$arrayMiscMsg);
  $smarty->assign('arrayErrorMessage',$arrayErrorMsg);
  $smarty->assign('arrayWarningMessage',$arrayWarningMsg);
  $smarty->_smarty_include(array('smarty_include_tpl_file'=>'pcerror_user.tpl','smarty_include_vars'=>''));  
}
?>
