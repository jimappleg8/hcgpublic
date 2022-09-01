<?
// dbUtils 1.1  by Jonathan Hilgeman

// Released: 8-16-2001
// Homepage: http://www.SiteCreative.com
// Location: http://www.SiteCreative.com/projects/dbUtils.php
// Description: A free collection of odd functions to manage mySQL databases.

// Define Your Servers Here - Add as Many As You Like!

    //$db["ServerOne"]["user"] = "root";		# mySQL Username
    //$db["ServerOne"]["pass"] = "password";	# mySQL Password
    //$db["ServerOne"]["host"] = "localhost";	# mySQL Hostname
    //$db["ServerOne"]["port"] = 3306;		# mySQL Server port
    //$db["ServerOne"]["time"] = 10;		# Time-out in seconds

    //$db["ServerTwo"]["user"] = "root";
    //$db["ServerTwo"]["pass"] = "password";
    //$db["ServerTwo"]["host"] = "localhost";
    //$db["ServerTwo"]["port"] = 3306;
    //$db["ServerTwo"]["time"] = 10;
        

// __________________
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
//  BEGIN TASKS HERE
// __________________
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯

// Sample Tasks:

// ### Move "OldTable" and "AnotherTable" from one database to another
// dbTableMove("ServerOne","FromDatabase","ToDatabase","OldTable";
// dbTableMove("ServerOne","FromDatabase","ToDatabase","AnotherTable";

// ### Copy a database from one server to another
// dbSpecificCopy("ServerOne","ServerTwo","LocalDatabase","RemoteDatabase");

// ### Copy a table from one database to another
// dbTableCopy("ServerOne","FromDatabase1","ToDatabase2","Table");

// ### Rename a database
// dbRename("ServerOne","OriginalDatabaseName","NewDatabaseName");

// ### Copy ALL databases and ALL tables from one server to another (mirror a server)
// dbCopy("ServerOne","ServerTwo","/.*/","/.*/");

// ### Copy ALL databases and all tables that have the letters "old" in their names from one server to another
// dbCopy("ServerOne","ServerTwo","/.*/","/old*/");

// ### Copy the "JustMine" database and ALL tables within it from one server to another
// dbCopy("ServerOne","ServerTwo","/JustMine/","/.*/");

// ### Copy the "JustMine" database and just the "Records" table within it from one server to another
// dbCopy("ServerOne","ServerTwo","/JustMine/","/Records/");

// __________________
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯
//  END OF TASK LIST
// __________________
// ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯


/**
 * Moves a single table from one database to another
 * Example:
 *   dbTableMove("ServerOne","FromDatabase","ToDatabase","TableName"
 */
function dbTableMove($Host, $FromDatabaseName, $ToDatabaseName, $dbTableName)
{
   // Connect to Database
   $dbLink = dbConnect($Host);

   // Construct Query to Send to Receiving Server

   // Table Definitions
   $SendQuery["Drop"] = "DROP TABLE IF EXISTS $dbTableName;";
   $SendQuery[] = ReturnCreateTable($FromDatabaseName, $dbTableName, $dbLink);

   // Data Inserts            
   $TableInserts = ReturnTableInserts($FromDatabaseName, $dbTableName, $dbLink);
                        
   if (count($TableInserts))
   {
      foreach ($TableInserts as $InsertString)
      {
         $SendQuery[] = $InsertString;
      }
   }

   // Send All Queries to Receiving Server
   foreach ($SendQuery as $Query)
   {
      print $Query . "<BR>";
      mysqli_select_db($dbLink, $ToDatabaseName);
      $dbResult = mysqli_query($dbLink, $Query) or die(mysqli_error() . " - Line 152 - $Query");
   }

   // Send Drop Table to Source Database
   mysqli_select_db($dbLink, $FromDatabaseName);
   $dbResult = mysqli_query($dbLink, $SendQuery["Drop"]) or die(mysqli_error() . " - Line 156 - $Query");

   // Success!
   return 1;
        
}

/**
 * Copies a single database from one server to another
 * Example:
 *   dbSpecificCopy("ServerOne","ServerTwo","LocalDatabase","RemoteDatabase");
 */
function dbSpecificCopy($FromHost,$ToHost,$FromDatabaseName,$ToDatabaseName)
{
   // Connect to Databases
   $dbLinkOne = dbConnect($FromHost);
   $dbLinkTwo = dbConnect($ToHost);
        
   // Get all Table Names
   $dbList = mysqli_query($dbLinkOne, 'SHOW TABLES FROM '.$FromDatabaseName);
                
   while ($dbRow = mysqli_fetch_array($dbList)) {
      $TableName = $dbRow[0];
      $dbTableNames[] = $TableName;
   }
           
   // Construct Query to Send to Receiving Server
                        
   // Create Databases
   $SendQuery[] = "CREATE DATABASE IF NOT EXISTS $ToDatabaseName;";

   // Table Definitions
   foreach ($dbTableNames as $dbTableName) {
      $SendQuery[] = "DROP TABLE IF EXISTS $dbTableName;";
      $SendQuery[] = ReturnCreateTable($FromDatabaseName, $dbTableName, $dbLinkOne);
   }
            
   // Data Inserts            
   foreach ($dbTableNames as $dbTableName) {
      $TableInserts = ReturnTableInserts($FromDatabaseName, $dbTableName, $dbLinkOne);
                        
      if (count($TableInserts)) {
         foreach ($TableInserts as $InsertString) {
            $SendQuery[] = $InsertString;
         }
      }
   }

   // Send All Queries to Receiving Server
        
   foreach ($SendQuery as $Query)
   {
      if (substr($Query,0,15) == "CREATE DATABASE")
      {
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - $Query");
      }
      else
      {
         mysqli_select_db($dbLinkTwo, $ToDatabaseName);
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - $Query");
      }
   }
        
   // Success!
   return 1;
}


/**
 * Copies a single table from one database to another 
 * Example:
 *    dbTableCopy("ServerOne","FromDatabase1","ToDatabase2","Table");
 */
function dbTableCopy($Host,$FromDatabaseName,$ToDatabaseName,$dbTableName)
{
   // Connect to Database
   $dbLink = dbConnect($Host);

   // Construct Query to Send to Receiving Server
                        
   // Table Definitions
   $SendQuery[] = "DROP TABLE IF EXISTS $dbTableName;";
   $SendQuery[] = ReturnCreateTable($FromDatabaseName, $dbTableName, $dbLink);
            
   // Data Inserts            
   $TableInserts = ReturnTableInserts($FromDatabaseName, $dbTableName, $dbLink);
                        
   if (count($TableInserts)) {
      foreach ($TableInserts as $InsertString) {
         $SendQuery[] = $InsertString;
      }
   }
                
   // Send All Queries to Receiving Server
   foreach ($SendQuery as $Query)
   {
      mysqli_select_db($dbLink, $ToDatabaseName);
      $dbResult = mysqli_query($dbLink, $Query) or die(mysqli_error() . " - Line 97 - $Query");
   }
        
   // Success!
   return 1;
}


/**
 * Rename a single database
 * Example:
 *    dbRename("ServerOne","OriginalDatabaseName","NewDatabaseName");
 */
function dbRename($Host,$DatabaseName,$NewDatabaseName)
{
   // Connect to Database
   $dbLink = dbConnect($Host);

   // Get all Tables
   $dbList = mysqli_query($dbLink, 'SHOW TABLES FROM '.$DatabaseName) or die("No Database by that Name, or No Tables in Database!");
            
   while ($dbRow = mysqli_fetch_array($dbList)) {
      $TableName = $dbRow[0];
      $dbTables[] = $TableName;
   }

   // Construct Query to Send to Receiving Server
                        
   // Create Databases
   $SendQuery[] = "CREATE DATABASE IF NOT EXISTS $NewDatabaseName;";

   // Table Definitions
   foreach ($dbTables as $dbTableName) {
      $SendQuery[] = "DROP TABLE IF EXISTS $dbTableName;";
      $SendQuery[] = ReturnCreateTable($DatabaseName, $dbTableName, $dbLink);
   }
            
   // Data Inserts            
   foreach ($dbTables as $dbTableName) {
      $TableInserts = ReturnTableInserts($DatabaseName, $dbTableName, $dbLink);
                        
      if (count($TableInserts)) {
         foreach ($TableInserts as $InsertString) {
            $SendQuery[] = $InsertString;
         }
      }
   }
                
   // Drop Original Database
   $SendQuery[] = "DROP DATABASE IF EXISTS $DatabaseName;";

   // Send All Queries to Receiving Server
   foreach ($SendQuery as $Query)
   {
      if (substr($Query,0,15) == "CREATE DATABASE")
      {
         $dbResult = mysqli_query($dbLink, $Query) or die(mysqli_error() . " - Line 93 - $Query");
      }
      else
      {
         mysqli_select_db($dbLink, $NewDatabaseName);
         $dbResult = mysqli_query($dbLink, $Query) or die(mysqli_error() . " - Line 97 - $Query");
      }
   }
        
   // Success!
   return 1;
}


/**
 * This function will copy databases and tables matching regular expressions.  
 * Example:
 *    dbCopy("ServerOne","ServerTwo","/(.*)/","/(.*)/");                    
 */
function dbCopy($FromHost,$ToHost,$DatabaseRegExp,$TableRegExp)
{
   // Connect to Databases
   $dbLinkOne = dbConnect($FromHost);
   $dbLinkTwo = dbConnect($ToHost);
        
   // Get all Databases Matching Regular Expression
   $dbList = mysqli_query($dbLinkOne, 'SHOW DATABASES');
            
   while ($dbRow = mysqli_fetch_array($dbList)) {
      $dbName = $dbRow["Database"];
      if (preg_replace($DatabaseRegExp,"",$dbName) != $dbName) {
         $dbMatches[] = $dbName;
      }
   }
              
   // Get all Tables Matching Regular Expression (Only in Matched DBs)
   foreach ($dbMatches as $dbName) {
      $dbList = mysqli_query($dbLinkOne, 'SHOW TABLES FROM '.$dbName);
      while ($dbRow = mysqli_fetch_array($dbList)) {
         $TableName = $dbRow[0];
         if (preg_replace($TableRegExp,"",$TableName) != $TableName) {
            $dbToTransfer["$dbName"][] = $TableName;
         }
      }
   }
            
   // Construct Query to Send to Receiving Server
                        
   // Create Databases
   foreach ($dbToTransfer as $dbName => $dbTables) {
      $SendQuery["$dbName"][] = "CREATE DATABASE IF NOT EXISTS $dbName;";
   }

   // Table Definitions
   foreach ($dbToTransfer as $dbName => $dbTables) {
      foreach ($dbTables as $dbTableName) {
         $SendQuery["$dbName"][] = "DROP TABLE IF EXISTS $dbTableName;";
         $SendQuery["$dbName"][] = ReturnCreateTable($dbName, $dbTableName, $dbLinkOne);
      }
   }
            
   // Data Inserts            
   foreach ($dbToTransfer as $dbName => $dbTables) {
      foreach ($dbTables as $dbTableName) {
         $TableInserts = ReturnTableInserts($dbName, $dbTableName, $dbLinkOne);
         if (count($TableInserts)) {
            foreach ($TableInserts as $InsertString) {
               $SendQuery["$dbName"][] = $InsertString;
            }
         }               
      }
   }

   // Send All Queries to Receiving Server
   
   foreach ($SendQuery as $DataBase => $QueryArray) {
      foreach ($QueryArray as $Query) {
         if (substr($Query,0,15) == "CREATE DATABASE")
         {
            $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 184 - $Query");
         }
         else
         {
            mysqli_select_db($dbLinkTwo, $DataBase);
            $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 188 - $Query");
         }
      }
   }
   // Success!
   return 1;
}


/**
 * This function will copy multiple tables (bsed on a regular expression)
 * between databases on two different servers
 */
function dbCopyRegEx($FromHost, $ToHost, $FromDb, $ToDb, $TableRegEx)
{
   // Connect to Databases
   $dbLinkOne = dbConnect($FromHost);
   $dbLinkTwo = dbConnect($ToHost);
   
   // Get all tables matching regular expression
   $tableList = mysqli_query($dbLinkOne, 'SHOW TABLES FROM '.$FromDb);
   while ($tableRow = mysqli_fetch_array($tableList))
   {
      $tableName = $tableRow[0];
      if (preg_replace($TableRegEx, "", $tableName) != $tableName)
      {
         $tablesToTransfer[] = $tableName;
      }
   }

   // Construct Query to Send to Receiving Server
                        
   // Make sure the database and table exist on the target server
   $SendQuery[] = "CREATE DATABASE IF NOT EXISTS $ToDb;";
   $SendQuery[] = "USE $ToDb;";

   // Table Definitions
   foreach ($tablesToTransfer as $dbTableName)
   {
      $SendQuery[] = "DROP TABLE IF EXISTS $dbTableName;";
      $SendQuery[] = ReturnCreateTable($FromDb, $dbTableName, $dbLinkOne, $ToHost);
   }

            
   // Data Inserts            
   foreach ($tablesToTransfer as $dbTableName)
   {
      $TableInserts = ReturnTableInserts($FromDb, $dbTableName, $dbLinkOne);
      if (count($TableInserts))
      {
         foreach ($TableInserts as $InsertString)
         {
            $SendQuery[] = $InsertString;
         }
      }               
   }


   // Send All Queries to Receiving Server
   foreach ($SendQuery as $Query) {
      if (substr($Query,0,15) == "CREATE DATABASE")
      {
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 354 - $Query");
      }
      else
      {
         mysqli_select_db($dbLinkTwo, $ToDb);
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 355 - $Query");
      }
   }
   
   // Success!
   return 1;
}


/**
 * This function will copy a single table between databases on two 
 * different servers (no regular expressions) 
 */
function dbCopySimple($FromHost, $ToHost, $FromDb, $ToDb, $Table)
{
   // Connect to Databases
   $dbLinkOne = dbConnect($FromHost);
   $dbLinkTwo = dbConnect($ToHost);
        
   // Construct Query to Send to Receiving Server
                        
   // Make sure the database and table exist on the target server
   $SendQuery[] = "CREATE DATABASE IF NOT EXISTS $ToDb;";
   $SendQuery[] = "USE $ToDb;";

//   echo "FromDb: ".$FromDb."<br />";
//   echo "Table: ".$Table."<br />";
//   echo "dbLinkOne: ".$dbLinkOne."<br />";
   
   // Table Definitions
   $SendQuery[] = "DROP TABLE IF EXISTS $Table;";
   $SendQuery[] = ReturnCreateTable($FromDb, $Table, $dbLinkOne, $ToHost);
            
   // Data Inserts            
   $TableInserts = ReturnTableInserts($FromDb, $Table, $dbLinkOne);
                        
   if (count($TableInserts)) {
      foreach ($TableInserts as $InsertString) {
         $SendQuery[] = $InsertString;
      }
   }
                
   // Send All Queries to Receiving Server
   foreach ($SendQuery as $Query)
   {
      if (substr($Query,0,15) == "CREATE DATABASE")
      {
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 354 - $Query");
      }
      else
      {
         mysqli_select_db($dbLinkTwo, $ToDb);
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 355 - $Query");
      }
   }
   
   // Success!
   return 1;
}


/**
 * This function will copy table records according to a "where" statement.  
 * Example:
 *    dbCopy("ServerOne","ServerTwo","database","table", $where);                    
 */
function dbCopyWhere($FromHost, $ToHost, $FromDb, $ToDb, $Table, $Where)
{
   // Connect to Databases
   $dbLinkOne = dbConnect($FromHost);
   $dbLinkTwo = dbConnect($ToHost);
        
   // Construct Query to Send to Receiving Server
                        
   // Make sure the database and table exist on the target server
   $SendQuery[] = "CREATE DATABASE IF NOT EXISTS $ToDb;";
   $SendQuery[] = "USE $ToDb;";
   $SendQuery[] = ReturnCreateTable($FromDb, $Table, $dbLinkOne, $ToHost);

   // Delete the records matching the $where statement
   $SendQuery[] = "DELETE FROM $Table $Where;";
            
   // Data Inserts            
   $TableInserts = ReturnTableInserts($FromDb, $Table, $dbLinkOne, $Where);
                        
   if (count($TableInserts))
   {
      foreach ($TableInserts as $InsertString)
      {
         $SendQuery[] = $InsertString;
      }
   }
   
//   echo '<pre>'; print_r($SendQuery); echo '</pre>'; exit;
                
   // Send All Queries to Receiving Server
   foreach ($SendQuery as $Query)
   {
      if (substr($Query,0,15) == "CREATE DATABASE")
      {
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 400 - $Query");
      }
      else
      {
         mysqli_select_db($dbLinkTwo, $ToDb);
         $dbResult = mysqli_query($dbLinkTwo, $Query) or die(mysqli_error() . " - Line 401 - $Query");
      }
   }

   // Success!
   return 1;
}


//   ____________
// |¯¯¯¯¯¯¯¯¯¯¯¯¯¯|
// | Subfunctions |
// |______________|
//   ¯¯¯¯¯¯¯¯¯¯¯¯

function dbConnect($Host)
{
   // Bring in db Array
   global $db;
   
//   echo '<pre>'; print_r($db); echo '</pre>'; exit;
            
   // Connect to Databases
   if ($SocketTest = fsockopen($db[$Host]["host"], $db[$Host]["port"], $errno, $errstr, $db[$Host]["time"])) {
      // Server is Responding!
      // Close Socket
      fclose($SocketTest);
      // Try to Connect
      if($dbLink = mysqli_connect($db[$Host]["host"], $db[$Host]["user"], $db[$Host]["pass"], "", $db[$Host]["port"])) {
         // Connection is now active.
         return $dbLink;
      } else {
         // No connection.
         die("Server $Host is responding, but refused your connection.");
      }
   } else {
      // Server is Down!
      die("Server $Host does not appear to be running mySQL on port " . $db[$Host]["port"] . " or is currently down.<br>$errstr");
   }
}
    
/**
 * The Following Function Is Property of the People Who Made phpMyAdmin
 * (www.phpwizard.net). 
 * Slightly modified and Cleaned Up by JH
 */
function ReturnCreateTable($Database, $Table, $dbLink, $Target = "")
{
   // Bring in db Array
   global $db;

   // Start Definition
   $Definition = "CREATE TABLE IF NOT EXISTS $Table (";
   
   if ($Target != "") {
      $engine = $db[$Target]['type'];
   }
        
   // Get Field Definitions
   $dbQuery = "SHOW FIELDS FROM $Table";
   mysqli_select_db($dbLink, $Database);
   $dbResult = mysqli_query($dbLink, $dbQuery);
            
   while ($dbRow = mysqli_fetch_array($dbResult)) {
      $Definition .= "$dbRow[Field] $dbRow[Type]";
                
      if (IsSet($dbRow["Default"]) && (!empty($dbRow["Default"]) || $dbRow["Default"] == "0")) {
         $Definition .= " DEFAULT '" . $dbRow["Default"] . "'";
      }
            
      if ($dbRow["Null"] != "YES") {
         $Definition .= " NOT NULL";
      }
                
      if ($dbRow["Extra"] != "") {
         $Definition .= " $dbRow[Extra]";
      }
                
      $Definition .= ",";
   }
    
   // Get Key (Primary, Unique, Etc...) Definitions    
   $dbQuery = "SHOW KEYS FROM $Table";
   mysqli_select_db($dbLink, $Database);
   $dbResult = mysqli_query($dbLink, $dbQuery);
            
   while ($dbRow = mysqli_fetch_array($dbResult)) {
      $KeyName=$dbRow['Key_name'];
                
      if (($KeyName != "PRIMARY") && ($dbRow['Non_unique'] == 0)) {
         $KeyName="UNIQUE|$KeyName";
      }
                
      if (!isset($index[$KeyName])) {
         $index[$KeyName] = array();
      }
                
      $index[$KeyName][] = $dbRow['Column_name'];
   }
            
   while (list($x, $columns) = @each($index)) {
      $Definition .= ",";
                
      if ($x == "PRIMARY") {
         $Definition .= "PRIMARY KEY (" . implode($columns, ", ") . ")";
      } elseif (substr($x,0,6) == "UNIQUE") {
         $Definition .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
      } else {
         $Definition .= "   KEY $x (" . implode($columns, ", ") . ")";
      }
   }
        
   // End Parentheses
   if ($engine == "") {
      $Definition .= ");";
   } else {
      if ($engine == "MyISAM") {
         $key = "TYPE";
      } else {
         $key = "ENGINE";
      }
      $Definition .= ") ".$key."=".$engine.";";
   }

   // Get rid of repeated parentheses and misplaced commas
   $Definition = str_replace(",,",",",$Definition);
   $Definition = str_replace(",)",")",$Definition);
   
   // Return Definition
   return (stripslashes($Definition));
}


/**
 * The Following Function Is Property of the People Who Made phpMyAdmin
 * (www.phpwizard.net). 
 * Slightly modified and Cleaned Up by JH
 */
function ReturnTableInserts($Database, $Table, $dbLink, $Where = "")
{
   $dbQuery = "SELECT * FROM $Table $Where";
   mysqli_select_db($dbLink, $Database);
   $dbResult = mysqli_query($dbLink, $dbQuery);

   $i = 0;
        
   while ($dbRow = mysqli_fetch_row($dbResult))
   {
      set_time_limit(60);
            
      $Table_list = "(";
            
      for ($j=0; $j<mysqli_num_fields($dbResult);$j++)
      {
         $myfield = mysqli_fetch_field_direct($dbResult, $j);
         $Table_list .= $myfield->name . ", ";
      }
            
      $Table_list = substr($Table_list,0,-2);
      $Table_list .= ")";
            
      if (isset($GLOBALS["showcolumns"])) {
         $InsertText = "INSERT INTO $Table $Table_list VALUES (";
      } else {
         $InsertText = "INSERT INTO $Table VALUES (";
      }
            
      for ($j=0; $j<mysqli_num_fields($dbResult);$j++) {
         if (!isset($dbRow[$j])) {
            $InsertText .= " NULL,";
         } elseif ($dbRow[$j] != "") {
            $InsertText .= " '".addslashes($dbRow[$j])."',";
         } else {
            $InsertText .= " '',";
         }
      }
            
      $InsertText = preg_replace("/,$/", "", $InsertText);
      $InsertText .= ");";
            
      $AllInserts[] = trim($InsertText);
            
      $i++;
   }

   return $AllInserts;
}


?>  