<?php
/**
 * Project PointComma - Debug routine - debug.php
 *
 * This is a custom debugging routine that aims at logging every step in pc. It
 * is not really clean because its require to use the assertion in a way they
 * are not supposed to but its pretty efficient in terms of efficiency.
 *
 * It is not necessary and could be replaced if needed...
 *
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 23 feb 2005
 * @version 0.1
 *
 */

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// Create a handler function
function _pcAssertionHandler($file, $line, $code)
{
}

// Set up the callback
//assert_options(ASSERT_CALLBACK, '_pcAssertionHandler');

function pcDebugInfo($arrayDefinedVars , $strPackage, $strDebugComment, $debugLevel = 0, $ExtraDebugComment = '')
{
   global $pcConfig;

   //only Consider the information higher or equal to the ErrorLevel set
   if ($pcConfig['debug']['errorLevel']<=$debugLevel) {

      //get the function that called debuginfo
      if (function_exists('debug_backtrace')) {
			// PHP version > 4.3
         $arrayTemp = debug_backtrace();
         $arrayFunction = array_pop ($arrayTemp);
      } else {
         // Old version of PHP
         // TODO: remove
         $arrayTemp = array();
	     $arrayFunction = array('file' => false, 'line' => false);
      }

      //Lighten the trace to avoid useless variables
      unset($arrayTemp[0]) ;
		
      $intArraySize = sizeof($arrayTemp);
		
      if ($intArraySize !== 0) {
         //only keep args for the first call
         for ($i=1; $i < $intArraySize; $i++) {
            unset($arrayTemp[$i]['args']);
         }
		
         // warning Nasty bug without this line in the debugger and 
         //  possible exhausted memory problem
         // required to suppress the variable context from memory that 
         //  could the whole application if debug is on!!!!!
         if ($arrayTemp[$intArraySize]['function'] == "pcerrorhandler") {
            unset($arrayTemp[$intArraySize]['args'][4]);
         }
         if ($arrayTemp[$intArraySize]['function'] == "pcdebuginfo") {
            unset($arrayTemp[$intArraySize]['args'][0]);
         }
      }

      // Follow the evolution of variable:

      // TODO: implement it
		
      // Stock the results
      $debugMsgStack = new messageStack('debug');   
      $debugMsgStack->push(array(
         'time'=> time(),
         'file'=> $arrayFunction['file'],
         'line' => $arrayFunction['line'],
         'trace' => $arrayTemp,
         'debugLevel' => $debugLevel,
         'package' => $strPackage,
         'comment' => $strDebugComment,
         'extracomment' => $ExtraDebugComment,
         'variables' => ''));

      /*                                
      echo '<br>'.$strPackage.'::'.$strDebugComment;
      $arrayTemp = debug_backtrace();
      echo '<pre>';
      var_dump(debug_backtrace());
      echo '</pre>';
      //while ($array= array_pop ($arrayTemp)) {
      //  echo '<br>'.$array["file"].'--'.$array["line"];
      //}
      echo '<br>';
      echo '<br>';

      $clearance = unserialize(CLEARANCE);

      $errorLine = $clearance['userName'].','.$_SERVER['REMOTE_ADDR']."<pre>".var_dump(debug_backtrace())."</pre>".microtime()."<br><br>\n";

      $errFile = fopen($pcConfig['includePath'].$pcConfig['file'],'a+');
      fwrite($errFile, $errorLine);
      fclose($errFile);*/
   }
   return $debugLevel;
}

?>
