<?php

/*
   This plugin provides the database abstraction layer for
   standard-SQL compliant relational databases, for which
   interfaces in either ADOdb, PEAR::DB or PHP's dbx extension
   exist.
   You could establish a database connection thru one of these
   db wrappers yourself and put it into the global $db var, but
   it is sometimes better to use the "anydb_connect()" function.

   Currently this plugin is mainly used (and only tested with)
   the PostgreSQL database. You should not use this with MySQL
   (even if it still works).

   Notes:
   - you should use the anydb_connect() when possible or else assign
     your PEAR::DB, ADOdb or dbx connection handle to the global '$db'
   - sqlite is currently probably supported by PEAR::DB only
   - dbx is rather memory exhaustive ("emalloc() unable to allocate
     1.7 gigabytes"...) - but maybe just a bug in my ver
   - dbx is otherwise a very good thing, but now not very suitable
     for the newer ewiki database layer
   - ADOdb does not work with PHP5
   - ewiki uses the Latin-1 charset exclusively, your database needs
     to know this (createdb -E LATIN1 wikidb)
   - Else you could enable EWIKI_DB_UTF8 for Postgres "UNICODE" databases,
     where "SET NAMES" doesn't work.
   - there is no _DB_F_BINARY support for PostgreSQL, so please use
     plugins/db/binary_store meanwhile
   

   See also:
   - [http://php.weblogs.com/adodb] for ADOdb
   - [http://pear.php.net/] for PEAR::DB
   - [http://www.php.net/manual/en/ref.dbx.html] for dbx()
*/


#-- open db link here, if not already done, example:
/*
  include(".../adodb/adodb.inc.php")
    or  include("DB.php")
    or  dl("dbx.so");

  $db = anydb_connect("localhost", "root", "$password", "test", "mysql");
*/


#-- config
define("EWIKI_DB_UTF8", false);


#-- plugin registration
$ewiki_plugins["database"][0] = "ewiki_database_anydb";




function ewiki_database_anydb($ACTION, $args=array(), $sw1, $sw2) {

# echo "ewiki_db(<b>$ACTION</b>, <tt>";var_export($args);echo "</tt>)<br>\n";

   #-- global $db connection var
   $db = & $GLOBALS["db"];     # change here, if you're using another name
                               # for the database connection handle
   $table=EWIKI_DB_TABLE_NAME;

   #-- result
   $r = array();

   #-- utf8
   $args = ewiki_db_encode($args);

   #-- proceed
   switch ($ACTION) {

      case "GET":
         $id = "'" . anydb_escape_string($args["id"]) . "'";
         if (@$args["version"]) {
            $AND_VERSION = "AND (version=".$args["version"].")";
         }
         $result = anydb_query("
             SELECT * FROM $table
             WHERE (pagename=$id) $AND_VERSION  ORDER BY version DESC  LIMIT 1", $db
         );
         if ($result && ($r = anydb_fetch_array($result, "_ASSOC_ONLY=1"))) {
            $r["id"] = $r["pagename"];
            unset($r["pagename"]);
         }
         break;



      case "HIT":
         anydb_query("UPDATE $table SET hits=(hits+1) WHERE pagename='" . anydb_escape_string($args["id"]) . "'");
         break;



      case "OVERWRITE":
         anydb_query("DELETE FROM $table WHERE (pagename='"
                   . anydb_escape_string($args["id"]) . "') AND ("
                   . "version=$args[version])");

      case "WRITE":
         $args["pagename"] = $args["id"];
         unset($args["id"]);

         $sql1 = $sql2 = "";
         foreach ($args as $index => $value) {
            if (is_int($index)) {
               continue;
            }
            $a = ($sql1 ? ', ' : '');
            $sql1 .= $a . $index;
            $sql2 .= $a . "'" . anydb_escape_string($value) . "'";
         }

         $result = anydb_query(
             "INSERT INTO $table ($sql1) VALUES ($sql2)"
         );

         return($result ?1:0);
         break;



      case "FIND":
         $where = array();
         if ($list = array_values($args)) {
            foreach ($list as $id) {
               if (strlen($id)) {
                  $r[$id] = 0;
                  $where[] = "(pagename='".anydb_escape_string($id)."')";
               }
            }
         }
         if ($where && ($where = implode(" OR ", $where)) ) {
            $result = anydb_query(
               "SELECT pagename AS id, meta FROM $table WHERE $where"
            );
            while ($result && ($row = anydb_fetch_array($result))) {
               $id = EWIKI_DB_UTF8 ? ewiki_db_decode($row[0]) : $row[0];
               $r[$id] = strpos($row[1], 's:5:"image"') ? $row[1] : 1;
            }
         }
         break;



      case "GETALL":
         $result = anydb_query("SELECT pagename AS id, ".
            implode(", ", $args) .
            " FROM $table " .
            " ORDER BY id, version DESC"
         );
         $r = new ewiki_dbquery_result($args);
         $drop = "";
         while ($result && ($row = anydb_fetch_array($result))) {
            if (EWIKI_DB_UTF8) { $row = ewiki_db_decode($row); }
            $i = EWIKI_CASE_INSENSITIVE ? strtolower($row["id"]) : $row["id"];
            if ($i != $drop) {
               $drop = $i;
               $r->add($row);
            }
         }
         break;



      case "SEARCH":
         $field = implode("", array_keys($args));
         $content = strtolower(implode("", $args));
         if ($field == "id") { 
            $field = "pagename";
         }
         $result = anydb_query("SELECT pagename AS id, version, flags" .
            (EWIKI_DBQUERY_BUFFER && ($field!="pagename") ? ", $field" : "") .
            " FROM $table " .
            " WHERE POSITION('".anydb_escape_string($content)."' IN LOWER($field)) > 0" .
            " ORDER BY id, version DESC "
         );
         $r = new ewiki_dbquery_result(array("id","version",$field));
         $drop = "";
         while ($result && ($row = anydb_fetch_array($result))) {
            if (EWIKI_DB_UTF8) { $row = ewiki_db_decode($row); }
            $i = EWIKI_CASE_INSENSITIVE ? strtolower($row["id"]) : $row["id"];
            if ($i != $drop) {
               $drop = $i;
               $r->add($row);
            }
         }
         break;



      case "DELETE":
         $id = anydb_escape_string($args["id"]);
         $version = $args["version"];
         anydb_query("DELETE FROM $table WHERE pagename='$id' AND version=$version");
         break;



      case "INIT":
         anydb_query("CREATE TABLE $table (
            pagename VARCHAR(160)  NOT NULL,
            'version' INTEGER  DEFAULT 0  NOT NULL,
            flags INTEGER  DEFAULT 0,
            content TEXT  DEFAULT '',
            refs TEXT  DEFAULT '',
            meta TEXT  DEFAULT '',
            author VARCHAR(100)  DEFAULT 'ewiki',
            created INTEGER   DEFAULT ".time().",
            lastmodified INTEGER  DEFAULT 0,
            hits INTEGER  DEFAULT 0     )
         ");
#            PRIMARY KEY (pagename, 'version') )
         anydb_query("
            ALTER TABLE ONLY ewiki
               ADD CONSTRAINT id PRIMARY KEY (pagename, 'version');
         ");
         break;


      default:
         echo("anydb-nyi($ACTION)");

   }

   #-- utf8
   if (is_array($r)) {
      $r = ewiki_db_decode($r);
   }

   return($r);
}



function ewiki_db_encode($a=NULL) {
   if (EWIKI_DB_UTF8) {
      if (is_array($a)) foreach ($a as $i=>$v) {
         $a[$i] = utf8_encode($v);
      }
      else $a = utf8_encode($a);
   }
   return($a);
}

function ewiki_db_decode($a=NULL) {
   if (EWIKI_DB_UTF8) {
      if (is_array($a)) foreach ($a as $i=>$v) {
         $a[$i] = utf8_decode($v);
      }
      else $a = utf8_decode($a);
   }
   return($a);
}




#----------------------------------------------------------------------------



if (!function_exists("anydb_connect")) {
#############################################################################
###                                                                       ###
###   anydb access wrapper wrapper                                        ###
###                                                                       ###
#############################################################################


define("ANYDB_PEAR", 21);
define("ANYDB_ADO",  22);
define("ANYDB_DBX",  23);
define("ANYDB_PG",   51);
define("ANYDB_MY",   52);


function anydb_connect($host="localhost", $user="", $pw="", $dbname="test", $dbtype="mysql") {
   global $anydb_handle;
   class_exists("DB")
     and ($db = DB::connect("$dbtype://$user:$pw@$host/$dbname"))
     and (is_a($db, "db_common"))
     and ($db->setFetchMode(DB_FETCHMODE_ASSOC) or true)
   or function_exists("newadoconnection")
     and ($db = NewAdoConnection($dbtype))
     and ($db->connect($host, $user, $pw, $dbname))
     and ($db->setFetchMode(ADODB_FETCH_ASSOC) or true)
   or ($dbtype[0]=="p") and function_exists("pg_connect")
     and ($db = pg_connect("dbname=$dbname user=$user password=$pw"))
   or function_exists("mysql_connect")
     and ($db = mysql_connect($host, $user, $pw))
     and (mysql_query("USE $dbname"))
   or function_exists("dbx_connect")
     and ($db = dbx_connect($dbtype, $host, $dbname, $user, $pw))
   or ($db = false);

   if ($anydb_handle = $db) {
      $charset = EWIKI_DB_UTF8 ? "UTF8" : "ISO-8859-1";
      @anydb_query("SET NAMES '$charset'");  #-- not all databases support this
   }
   return($db);
}


function anydb_handle($db=NULL) {
   global $anydb_handle;
   if (!empty($db)) {
      $anydb_handle=$db;
   }
   return($anydb_handle);
}


function anydb_type($obj=NULL) {
   if (is_object($obj)) {
      if (is_a($obj, "db_common") || is_a($obj, "db_result")) {
         return(ANYDB_PEAR);
      }
      elseif (is_a($obj, "adoconnection") || is_a($obj, "adorecordset")) {
         return(ANYDB_ADO);
      }
      elseif (is_a($obj, "stdclass")) {
         return(ANYDB_DBX);
      }
   } 
   elseif (is_resource($obj) && ($type = strtok(get_resource_type($obj), " "))) {
      if ($type == "pgsql") {
         return(ANYDB_PG);
      }
      elseif ($type == "mysql") {
         return(ANYDB_MY);
      }
   }
}


function anydb_query($sql, $db="") {
   $db = anydb_handle($db);
   $type = (anydb_type($db));
   $res = false;
   if ($type == ANYDB_PEAR) {
      $res = $db->query($sql);
      if (DB::isError($res)) { $res = false; }
   }
   elseif ($type == ANYDB_ADO) {
      $res = $db->Execute($sql);
   }
   elseif ($type == ANYDB_DBX) {
      $res = dbx_query($db, $sql, DBX_RESULT_ASSOC);
   }
   elseif ($type == ANYDB_PG) {
      $res = pg_query($db, $sql);
   }
   elseif ($type == ANYDB_MY) {
      $res = mysql_query($sql, $db, MYSQL_ASSOC);
   }
   return($res);
}



function anydb_fetch_array(&$res, $assoc_only=0) {
   $r = false;
   $type = anydb_type($res);
   if ($type == ANYDB_PEAR) {
      $r = $res->fetchRow(DB_FETCHMODE_ASSOC);
      if (is_object($r)) {
         $r = false;
      }
   }
   elseif ($type == ANYDB_ADO) {
      $r = $res->FetchRow();
      #<ok>  $r = obj || false
   }
   elseif ($type == ANYDB_DBX) {
      $r = array_shift($res->data);
      #<ok>#  $r == obj || 1 || false
   }
   elseif ($type == ANYDB_PG) {
      $r = pg_fetch_assoc($res);
   }
   elseif ($type == ANYDB_MY) {
      $r = mysql_fetch_array($res, $db);
   }
   #-- make numeric indicies, if wanted
   $n = 0;
   if (!$assoc_only && is_array($r) && count($r)) {
      foreach ($r as $i=>$d) {
         if (!is_int($i)) {
            $r[$n++] = &$r[$i];
         }
      }
   }
   return($r);
}



function anydb_escape_string($s, $db="") {
   $db = anydb_handle($db);
   $type = (anydb_type($db));
   if ($type == ANYDB_PEAR) {
      $s = $db->quoteString($s);
   }
   elseif ($type == ANYDB_ADO) {
      $s = $db->qStr($s);
      if ($s[0] = "'") {
         $s = substr($s, 1, strlen($s) - 2);
      }
   }
   elseif ($type == ANYDB_DBX) {
      $s = dbx_escape_string($db, $s);
   }
   elseif ($type == ANYDB_PG) {
      $s = pg_escape_string($s);
   }
   elseif ($type == ANYDB_MY) {
      $s = mysql_escape_string($s);
   }
   else {
      $s = addslashes($s);
   }
   return($s);
}


#############################################################################
###                                                                       ###
#############################################################################
}


?>