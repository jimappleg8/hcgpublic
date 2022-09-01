<?php

/*
   Beware, this is a fun plugin!  It is supposed to work, but not
   recommended for seriously big installations.  This database backend
   stores all your pages in a ZIP file, it requires the standard util
   "zip" ('pkzip.exe' may not work).

   You must set EWIKI_DB_ZIP and EWIKI_TMP to writable locations (the
   directory in which the single ZIP file resides must be world-
   writable).
   This database plugin is _CASE_INSENSITIVE always, hit counting isn't
   done. Eventually you even have to create an empty ZIP file yourself.
   And this will only run in a UNIX environment!
*/

define("EWIKI_DB_ZIP", "/tmp/database.zip");
define("EWIKI_CASE_INSENSITIVE", "always");


$ewiki_plugins["database"][0] = "ewiki_db_zip";
function ewiki_db_zip($FUNC, &$args, $f1=0, $f2=0)
{
   $tmp = EWIKI_TMP;
   $util = "zip ";
   $util_un = "unzip ";
   $util_get = "unzip -q -C -p ";
   $util_add = "zip -j -q -u ";
   $zip = EWIKI_DB_ZIP;
   $QUIET = "2>/dev/null";

   $r = array();


   switch ($FUNC)
   {

      case "GET":
        $ver = $args["version"];
        $fn = ewiki_db_fn($args["id"]);
        if (!$ver) {
           $ver = 0 + trim(`$util_get $zip $fn $QUIET`);
        }
        if ($ver) {
           $fn .= ".$ver";
           $r = `$util_get $zip $fn `;
           $r = unserialize($r);
        }
        break;


      case "OVERWRITE":
      case "WRITE":
        $fn = "$tmp/" . ewiki_db_fn($args["id"]);
        $fn2 = "$fn." . $args["version"];
        if ($f = fopen($fn2, "w")) {
           fwrite($f, serialize($args));     // unsafe, to say mildly
           fclose($f);
           if ($f = fopen($fn, "w")) {
              fwrite($f, $args["version"]);
              fclose($f);
              #-- add to zip
              $r = `$util_add $zip $fn2 $fn QUIET`;
              $r = !$r;
              @unlink($fn);
           }
           @unlink($fn2);
        }
        break;


      case "FIND":
        foreach ($args as $id) if ($id) {
           $r[$id] = 0;
           $fn = ewiki_db_fn($id);
           if ($ver = `$util_get $zip $fn $QUIET`) {
              $r[$id] = 1;
           }
        }
        break;


      case "GETALL":
        $r = new ewiki_dbquery_result($args);
        foreach (explode("\n", `$util_un -l $zip | cut -b 29-290`) as $id) {
           if (!strpos($id, ".") || !preg_match('/\.\d+$/', $id)) {
              $r->entries[] = rawurldecode($id);
           }
        }
        break;


      case "SEARCH":
        $field = array_keys($args);
        $field = array_shift($field);
        $field = array_shift($args);
        $result = ewiki_db_zip("GETALL", $uu);
        $r = new ewiki_dbquery_result($args);
        while ($row = $result->get()) {
           $row = ewiki_db_zip("GET", $row);
           if (stristr($row[$field], $value)) {
              $r->add($row);
           }
        }
        break;


      case "INIT":
        if (!filesize($zip) || !file_exists($zip)) {
           touch("$tmp/_");
           `$util -q -j -m $zip $tmp/_`;
        }
        if (!is_writeable($zip)) {
           echo "error db_zip: $zip is not writeable!\n";
        }
        break;
   }

   return($r);
}



function ewiki_db_fn($id)
{
   $id = ewiki_lowercase($id);
   return(rawurlencode($id));
}


?>