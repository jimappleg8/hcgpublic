<?php

#  a database plugin utilizing PHP's "dbm" or "dba" extension
#
#  You must set the EWIKI_DBA constant with the file name, where all
#  pages shall be saved. The filename extension tells which dba database
#  type to use ".db3", ".db2" or ".gdbm" or ".flatfile" may be
#  good choices.
#  The database file will get opened automatically when needed (despite from
#  the most other ewiki db interfaces).
#  EWIKI_DBFILES_GZLEVEL says how much time to spend on compressing the
#  pages content.



#-- plugin registration
$ewiki_plugins["database"][0] = "ewiki_database_dba";




//-- don't forget to strip the '_files' from the function name if you
//   decide to paste it into the main ewiki.php script:
function ewiki_database_dba ($FUNC, $args=0) {

   static $handle;

   #-- open dba connection
   if ((!$handle) && ($FUNC!="CONNECT")) {
      ewiki_database_dba("CONNECT", 0);
   }

   #-- result var
   $r = array();


   switch($FUNC) {


      case "GET":
	if (! ($n = @$args["version"])) {
		$n = ewiki_database("LASTVER", $args["id"]);
	}
        $key = $args["id"].".$n";
        ($r = dba_fetch($key, $handle))
        and
        ($r0 = unserialize(gzuncompress($r))) and ($r = $r0)
        or
	($r = unserialize($r));
	break;


      case "WRITE":
	if (! ($n = $args["version"])) {
		$n = 1 + ewiki_database("LASTVER", $args['id']);
	}
        $key = $args["id"].".$n";
	$r = dba_insert($key, gzcompress(serialize($args)), $handle);
	break;


      case "HIT":
         $key = $args["id"] . ".1";
         $r = unserialize(gzuncompress(dba_fetch($key, $handle)));
         $r["hits"] += 1;
#print_r($r);
         dba_replace($key, gzcompress(serialize($r), EWIKI_DBFILES_GZLEVEL), $handle);
         break;


      case "FIND":
         foreach ($args as $id) {
            $r[$id] = dba_exists("$id.1", $handle) ? 1 : 0;
#######
#<add># support for image width and height by returning the meta block
#######
         }
         break;


      case "GETALL":
         foreach (ewiki_database("ALLFILES") as $id) {
            $page = ewiki_database("GET", array("id"=>$id));
            $row = array("id" => $id);
            foreach ($args as $field) {
               $row[$field] = @$page[$field];
            }
            $r[] = $row;
         }
         break;


      case "SEARCH":
         foreach (ewiki_database("ALLFILES") as $id) {
            $page = ewiki_database("GET", array("id"=>$id));
            $check = true;
            foreach ($args as $field=>$content) {
               $check &= (stristr($page[$field], $content)!=false) || ($content == $page[$field]);
            }
            if ($check) {
               $r[] = $page;
            }
         }
         break;


      case "DELETE":
         dba_delete($args["id"].".".$args["version"], $handle);
         break;


      case "INIT":
         if (!$handle) {
            die("»database« not writable!\n");
         }
         break;


      case "LASTVER":    // helper subfunction
         $n = 1;
         while (dba_exists("$args.$n", $handle)) {
            $n++;
         }
         return(--$n);


      case "ALLFILES":
         $id = dba_firstkey($handle);
         while ($id != false) {
            if (substr($id, -2, 2) == ".1") {
               $r[] = substr($id, 0, strlen($id) - 2);
            }
            $id = dba_nextkey($handle);
         }
         break;


      case "CONNECT":
         $avail = array_reverse(dba_handlers());
         $try = substr(EWIKI_DBA, strrpos(EWIKI_DBA, ".") + 1);
         $try = array_merge(array($try, "gdbm", "ndbm", "db3", "db2", "db4", "dbm", "flatfile"), $avail);
         foreach ($try as $dba_handler) {
            if (in_array($dba_handler, $avail)) {
               foreach (array("w", "c", "n") as $mode) {
                  if ($handle = dba_open(EWIKI_DBA, $mode, $dba_handler)) {
#echo "USING($dba_handler), ";
                     if ($mode != "w") {
                        dba_close($handle);
                        $handle = dba_open(EWIKI_DBA, "w", $dba_handler);
                     }
                     break 2;
                  }
#else echo "!$dba_handler, ";
               }
            }
         }
         return($handle);
         break;

       
      default:
         die("nyi");

   }

   return($r);
}



#-- fake dba_* using dbm_* functions
if (!function_exists("dba_open") && function_exists("dbm_open")) {

   function dba_open($path, $mode, $handler, $a1=0) {
      if ($handler == "dbm") {
         return(dbmopen($path, $mode));
      }
      else return(false);
   }

   function dba_popen($a, $b, $c, $d=0) {
      return(dba_open($a, $b, $c));
   }

   function dba_exists($key, $handle) {
      return(dbmexists($handle, $key));
   }

   function dba_fetch($key, $handle) {
      return(dbmfetch($handle, $key));
   }

   function dba_insert($key, $string, $handle) {
      return(dbminsert($handle, $key, $string));
   }

   function dba_replace($key, $string, $handle) {
      return(dbmreplace($handle, $key, $string));
   }

   function dba_delete($key, $handle) {
      return(dbmdelete($handle, $key));
   }

   function dba_firstkey($handle) {
      return($GLOBALS["dbm_lastkey"] = dbmfirstkey($handle));
   }

   function dba_nextkey($handle) {
      return(dbmnextkey($handle, $GLOBALS["dbm_lastkey"]));
   }

   function dba_close($handle) {
      return(dbmclose($handle));
   }

   function dba_handlers() {
      return(array("dbm"));
   }

}



#-- fake zlib
if (!function_exists("gzcompress")) {

   function gzcompress($string, $uu=0) {
      return($string);
   }

   function gzuncompress($string, $uu=0) {
      return($string);
   }

}

?>