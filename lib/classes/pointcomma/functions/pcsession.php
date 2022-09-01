<?php
/**
 * Project PointComma - Session related lib - session.php
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * 
 */
 
//Define the function needed to session_set_save_handler
//Provide Mysql session management

// TODO: encrypt communication between Mysql and User
// I.E. http://people.cs.uchicago.edu/~ido/session_include_php.txt

// TODO: add semaphor to avoid session overwriting when a single user has session on two pages in the same time.

/**
 * Session openner for mysql session management
 * 
 * The session is created only when vars need to be registered
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * @package pcMysqlSession
 * @access private
 * @return bool 
 */
function _pcMysqlOpenSession() {
  return true;
}

/**
 * Session closer for mysql session management
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * @package pcMysqlSession
 * @access private
 * @return bool 
 */
function _pcMysqlCloseSession() {
  return true;
}

/**
 * Session read for mysql session management
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * @package pcMysqlSession
 * @access private
 * @var string sessionId
 * @return bool false in case of error, data if the session has been sucessfully
 * read
 */
function _pcMysqlReadSession($strSessionId) {
  global $pcdb_base,$pcConfig;
  
  //get the data if the session is alive
  $qHandle = mysql_query('SELECT strSessionData FROM `' . addslashes($pcConfig['dbPrefix']) . "sessions` WHERE ((strSessionId LIKE '" . addslashes($strSessionId) . "') and (intDeathTime > '" . time() . "'))", $pcdb_base);
  
  if ($qHandle and ($tempResult = mysql_num_rows($qHandle))) {
    $arraySessionData = array();
    $arraySessionData = mysql_fetch_array($qHandle, MYSQL_ASSOC);
    
    mysql_free_result($qHandle);
  
    if (!$arraySessionData['strSessionData']) {
      //db query failure or bad session key or new session
      return false;
    } else {
      return $arraySessionData['strSessionData'];
    } 
  } 
  //an error occured 
  return false; 
}

/**
 * Session Write for mysql session management
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * @package pcMysqlSession
 * @access private
 * @var string sessionId
 * @var string data
 * @return int the Session Id 
 */
function _pcMysqlWriteSession($strSessionId, $strSessionData) {
  global $pcConfig;
  
  // Get the local configuration of php
  if (!$intSessionLifeTime = get_cfg_var('session.gc_maxlifetime')) {
    $intSessionLifeTime = 1440;
  }
  
  //Calculate Sessions death Time
  $intDeathTime = $intSessionLifeTime + time();
  
  $arraySessionData = pcdb_select('SELECT strSessionId FROM `' . addslashes($pcConfig['dbPrefix']) . "sessions` WHERE strSessionId Like '" . addslashes($strSessionId) . "'");
  
  if ($arraySessionData[0]['strSessionId']) {
    return pcdb_update('UPDATE `' . addslashes($pcConfig['dbPrefix']) ."sessions` SET intDeathTime = '" . addslashes($intDeathTime) . "' , strSessionData = '" . addslashes($strSessionData) ."' WHERE strSessionId LIKE '" .  addslashes($strSessionId) . "'");
  } else {
    return pcdb_insert('INSERT into `' . addslashes($pcConfig['dbPrefix']) ."sessions` values ('". addslashes($strSessionId) ."' , '". addslashes($intDeathTime) . "' , '" . addslashes($strSessionData) . "')");
    
  }    
}

/**
 * Session Destroy for mysql session management
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * @package pcMysqlSession
 * @access private
 * @var string sessionId
 * @return bool 
 */
function _pcMysqlDestroySession($strSessionId) {
  global $pcConfig;
  return pcdb_query('DELETE FROM `' . addslashes($pcConfig['dbPrefix']) ."sessions` where strSessionId LIKE '". addslashes($strSessionId) ."'");  
}

/**
 * Session Garbage collector
 * 
 * Destroy the sessions that are too old > SESSION_LIFE_TIME
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.1
 * @package pcMysqlSession
 * @access private
 * @return bool true
 */
function _pcMysqlGarbageCollector() {
  global $pcConfig;
  pcdb_query('DELETE FROM `' . addslashes($pcConfig['dbPrefix']) ."sessions` where intDeathTime < '" . time() . "'");
  return true;
}

//replace the PHP standard session handler
session_set_save_handler('_pcMysqlOpenSession', '_pcMysqlCloseSession', '_pcMysqlReadSession', '_pcMysqlWriteSession', '_pcMysqlDestroySession', '_pcMysqlGarbageCollector');
?>