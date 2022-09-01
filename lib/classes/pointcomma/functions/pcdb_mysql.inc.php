<?php

// pcbd_mysql.inc.php
//
// This is simple enough that it certainly could be converted to
// use ADODB if desired.
//

//----------------------------------------------------------------
// pcdb_connect()
//
//----------------------------------------------------------------
function pcdb_connect($dbdomain, $dblogin, $dbpass, $dbname)
{
   // no debugging in the connection because the session is not started
   global $pcdb_base;
   $pcdb_base = @mysql_connect($dbdomain, $dblogin, $dbpass);
   if (!$pcdb_base) {
      trigger_error("Problem on the server: the connection to the database failed",FATAL);
      return false;
   } else {
      $isSuccess = mysql_select_db($dbname);
      return $isSuccess;
   } 
}


//----------------------------------------------------------------
// pcdb_select()
//
//----------------------------------------------------------------
function pcdb_select($query, $depleted=false, $depleted=false)
{
   global $pcdb_base,$pcConfig;
	
   //depleted vars
   $depleted=false;
	
   $qHandle = @mysql_query($query, $pcdb_base);
   assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'dbaccess',1)");
   if (!$qHandle) {
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'selectfailed',4,'Query: ['.\$query.'], SQL response: '.mysql_error())");
      return false;
   }
   if ($tempResult = mysql_num_rows($qHandle)) {
      for ($i=0; $i<$tempResult; $i++) {
         $returnArray[] = mysql_fetch_array($qHandle, MYSQL_ASSOC);
      }
      mysql_free_result($qHandle);
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'selectsuccess',2,'Query: ['.\$query.']')");
      return $returnArray;
   } else {
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'selectnoresults',3,'Query: ['.\$query.']')");
      return 0;
   }
}


//----------------------------------------------------------------
// pcdb_insert()
//
//----------------------------------------------------------------
function pcdb_insert($query, $depleted=false, $depleted=false)
{
   global $pcdb_base,$pcConfig;
  
   //depleted vars
   $depleted=false;
	
  $qHandle = @mysql_query($query, $pcdb_base);
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'dbaccess',1)");
  if (!$qHandle) {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'insertfailed',4,'Query: ['.\$query.'], SQL response: '.mysql_error())");
    return false;
  }
  $returnInsert = @mysql_insert_id();
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'dbaccess',1)");
  if (!$returnInsert) {
		// nasty bug in the mysql framework when the table do not have a autoincremental index it returned false now it return 0
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'The insertion failed or no autoincrement field is present',3,'Query: ['.\$query.'], SQL response: '.mysql_error())");
    return 0;
  } else {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'insertsuccess',2,'Query ['.\$query.'],  Returned ID: '.\$returnInsert)");
    return $returnInsert;
  }
}


//----------------------------------------------------------------
// pcdb_query()
//
//----------------------------------------------------------------
function pcdb_query($query, $depleted=false, $depleted=false)
{
   global $pcdb_base,$pcConfig;
	
   //depleted vars
   $depleted=false;
	 
  $qHandle = @mysql_query($query, $pcdb_base);
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'dbaccess',1)");
  if (!$qHandle) {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'queryfailed',4,'Query: ['.\$query.'], SQL response: '.mysql_error())");
    return false;
  } else {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'querysuccess',2,'Query: ['.\$query.']')");
    return true;
  }
}


//----------------------------------------------------------------
// pcdb_update()
//
//----------------------------------------------------------------
function pcdb_update($query, $depleted=false, $depleted=false)
{
   global $pcdb_base,$pcConfig;
	
   //depleted vars
   $depleted=false;
	
   $qHandle = @mysql_query($query, $pcdb_base);
  $affectedRows = mysql_affected_rows();
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'updatesuccess',2,'Query: ['.\$query.'], SQL response: '.mysql_error())");
  if (!$qHandle) {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'queryfailed',4,'Query: ['.\$query.'], SQL response: '.mysql_error())");
    return -1;
  } else {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDbManagement', 'updatesuccess',2,'Query ['.\$query.'],  Modified ID:'.count(\$affectedRows))");
    return $affectedRows;
  }
}

?>