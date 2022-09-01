<?php
/**
 * Project PointComma - Error Handling Lib - error.php
 * 
 * This lib provide a custom error handling that replace the one of php
 * It is used in conjonction with the php function trigger_user_error()
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 24 feb 2005
 * @version 0.1
 * 
 */

// set the error reporting level for this script
if ($pcConfig['debug']['active']) {
   error_reporting(E_ALL);
} else {
   error_reporting(E_ALL & ~E_NOTICE);
}

// error handler function
function pcErrorHandler($errno, $errstr, $errfile, $errline, $errContext)
{
  global $pcConfig,$pcLocalizedErrorMessages;
  
  //check if there is a translation for the error message:
  if (isset($pcLocalizedErrorMessages[$errstr])) {
    $errstr = $pcLocalizedErrorMessages[$errstr];
  }
  
  $errorMsgStack = new messageStack('error');
  
    switch ($errno) {     
      // fatal user generated error
      case FATAL:
        // push the error in the stack
        $errorMsgStack->push(array(FATAL,$errstr));
        
        // File the Debug API
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(\$errContext,'pcErrorHandling', 'User generated Fatal error Handled',9,\$errstr)");
        
        // display it in fatal mode
        pcErrorDisplay(FATAL);
        
        // stop the runtime execution
        exit(1);
        break;
      
      case ERROR:
        $errorMsgStack->push(array(ERROR,$errstr));       
        
        // File the Debug API       
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(\$errContext,'pcErrorHandling', 'User generated error Handled',8,\$errstr)");        
        break;
        
      case WARNING:
        $errorMsgStack->push(array(WARNING,$errstr));
        
        // File the Debug API
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(\$errContext,'pcErrorHandling', 'User generated warning Handled',4,\$errstr)");   
        break;
      
      case E_NOTICE:
        if ($pcConfig['debug']['active']) {
          $errorMsgStack->push(array('',"PHP Notice error: [$errno] $errstr   $errfile  $errline<br />\n"));
        }
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(\$errContext,'pcErrorHandling', 'PHP generated notice Handled',2,\$errstr)");
      break;
      
      case E_ERROR:
      case E_WARNING:
      default:
        if ($pcConfig['debug']['active']) {
          $errorMsgStack->push(array('',"Unkown error type: [$errno] $errstr   $errfile  $errline<br />\n"));
        }
        // File the Debug API
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(\$errContext,'pcErrorHandling', 'PHP generated error/warning Handled',9,\$errstr)");
      break;      
    }
}

//Replace the default php ErrorHandler
set_error_handler('pcErrorHandler');

function pcErrorDisplay($intBehavior = ERROR)
{
   $stackError = new messageStack('error');
  
   switch ($intBehavior) {
      // Fatal error display mode, display the last error message in the stack and exit
    case FATAL:
      $strErrorMessage = $stackError->resetStack();     
      $smartyErrorDisplay = new HCG_Smarty;
      $smartyErrorDisplay->assign('strErrorMessage',$strErrorMessage[1]);
      // added by Jim Applegate
      $smartyErrorDisplay->setTplPath('pcerror_fatal.tpl');
      $smartyErrorDisplay->display('pcerror_fatal.tpl');
      exit(1);
      break;
      
    // Normal Display mode: Display only user generated warning and empty the stack    
    case ERROR:
    // Verbose Display mode: Display user generated warning and notice
    case WARNING:
      $arrayErrorMsg = array();
      $arrayWarningMsg = array();
      $arrayMiscMsg = array();
      
      //SORT the ERROR msg from the WARNING message and the others
      while ($stackError->size()) {
        $msgError = $stackError->pop();
        
        if ($msgError[0]==ERROR) {
          $arrayErrorMsg[] = $msgError[1];
        }
        elseif ($msgError[0]==WARNING) {
          $arrayWarningMsg[] = $msgError[1];
        }
        else {
          $arrayMiscMsg[]= $msgError[1];
        }
      }
      
      //only display the error msg (delete the other)
      if ($intBehavior == ERROR) {
        $arrayWarningMsg= array();
        $arrayMiscMsg= array();
      }
      
      //display it!
      $smartyErrorDisplay = new HCG_Smarty;
      $smartyErrorDisplay->assign('arrayErrorMessage',$arrayErrorMsg);
      $smartyErrorDisplay->assign('arrayWarningMessage',$arrayWarningMsg);
      $smartyErrorDisplay->assign('arrayMiscMessage',$arrayMiscMsg);
      // added by Jim Applegate
      $smartyErrorDisplay->setTplPath('pcerror_user.tpl');
      $smartyErrorDisplay->display('pcerror_user.tpl');      
      break;  
  }
}

?>