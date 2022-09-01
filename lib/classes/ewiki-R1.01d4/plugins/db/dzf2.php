<?php

/*
   (lacks a fancy name)
   
   This plugin implements a more advanced flat-file database backend,
   which is designed to be faster than the older db/flat_files and to
   use the compressed/serialized format per default. Additionally it
   works case-insensitive (ONLY!) even on Unix filesystems and the
   filename encoding is engaged per default.
   (summary: this database backend is optimized but inconfigureable)
*/


#-- configuration settings
// EWIKI_DBFILES_DIRECTORY (like with plugins/db/flat_files)
define("EWIKI_DBFF_ACCURATE", 1);   # makes FIND call return image sizes
define("DZF2_HIT_COUNTING", 1);     # enables hit-counting


#-- hard-coded settings (don't try to change this)
define("EWIKI_CASE_INSENSITIVE", "_always");
$ewiki_plugins["database"][0] = "dzf2";


function dzf2($FUNC, &$args, $f1=0, $f2=NULL)
{
   #-- often used values
   $dir = EWIKI_DBFILES_DIRECTORY;
   $gz = EWIKI_DBFILES_GZLEVEL;

   #-- return value
   $r = array();

   switch ($FUNC) {

      case "GET":
        $n = $args["version"];
	if (!$n) {
           $n = dzf2_lastver($args["id"]);
	}
        if ($n) {
           $dbfile = dzf2_fn($args['id'].".$n");
           if ($f = gzopen($dbfile, "rb")) {
              $r = unserialize(gzread($f, 1<<21-1));
              gzclose($f);
           }
           if (DZF2_HIT_COUNTING && !$f1) {
              $r["hits"] = dzf2_hit($args["id"]);
           }
        }
	break;


      case "OVERWRITE":
      case "WRITE":
        $n = $args["version"];
        $lastver = dzf2_lastver($args['id']);
        $dbfile = dzf2_fn($args['id'].'.'.$n);
        if (!$n || ($n <= $lastver) && ($FUNC=="WRITE")) {
           echo "\nERROR in ewiki/db/dzf2: cannot overwrite existing entry for '$dbfile'.\n";
        }
        else {
           if ($f = gzopen($dbfile, "wb$gz")) {
              $r = gzwrite($f, serialize($args));
              gzclose($f);
              dzf2_setver($args['id'], $n);
           }
           dzf2_cache_add($args['id'], $n);
        }
        break;


      case "HIT":
        if (DZF2_HIT_COUNTING) {
           dzf2_hit($args['id'], +1);
        }
	break;


      case "FIND":
        foreach ($args as $id) {
           $fn = dzf2_fn($id);
           if (file_exists($fn)) {
              $r[$id] = 1;
              if (EWIKI_DBFF_ACCURATE && ((strpos($id,EWIKI_IDF_INTERNAL)===0) || strpos($id,":"))) {
                 $uu = array("id"=>$id);
                 $uu = dzf2("GET", $uu, $f1+1);
                 if ($uu["meta"]) {
                    $r[$id] = $uu["meta"];
                 }
              }
           }
        }
        break;


      case "GETALL":
        $r = new ewiki_dbquery_result($args);
        $r->entries = dzf2_all();
        break;


      case "SEARCH":
        $field = implode("", array_keys($args));
        $value = implode("", $args);
        $r = new ewiki_dbquery_result($args);
        foreach (dzf2_all() as $id) {
           if ($field=="id") {
              $uu = array("id"=>$id);
           }
           else {
              $uu = dzf2("GET", array("id"=>$id));
           }
           if (stristr($uu[$field], $value)) {
              $r->add($uu);
           }
        }
        break;


      case "INIT":
        if (!is_writeable($dir) || !is_dir($dir)) {
           mkdir($dir)
           or die("\nERROR in ewiki/db/dzf2: 'database' directory '$dir' is not writable!\n");
        }
        for ($c=97; $c<=122; $c++) { @mkdir($dir.'/'.chr($c)); }
        for ($c=48; $c<=57; $c++) { @mkdir($dir.'/'.chr($c)); }
        @mkdir($dir."/@");
        break;


      #-- used by tools/ -----------------------------------------------

      case "DELETE":
         $id = $args["id"];
         $ver = $args["version"];
         $fn = dzf2_fn($id);
         unlink("$fn.$ver");
         if (!dzf2_lastver($id)) {
            @unlink("$fn");
            @unlink("$fn.hits");
            dzf2_all("_PURGE");
         }
         break;
   }

   return($r);
}




function dzf2_fn($id)
{
   $id = ewiki_lowercase($id);

   $c0 = $id[0];
   if (($c0>="a") && ($c0<="z") || ($c0>="0") && ($c0<="9")) {
      $letter = $c0;
   }
   else {
      $letter = "@";
   }

   return(  EWIKI_DBFILES_DIRECTORY . "/$letter/" . rawurlencode($id)  );
}



function dzf2_lastver($id, $count_through=0) {
   $ver = NULL;
   $fn = dzf2_fn($id);
   if (file_exists($fn) && ($f = fopen($fn, "rb"))) {
      $ver = 0 + trim(fgets($f, 10));
      fclose($f);
   }
   return($ver);
}



function dzf2_setver($id, $ver) {
   $fn = dzf2_fn($id);
   if ($f = fopen($fn, "wb")) {
      fwrite($f, "$ver", 10);
      fclose($f);
   }
   else {
      echo "\nERROR in ewiki/db/dzf2: could not write version cache file for '$id'\n";
   }
}



function dzf2_all($rewrite=0) {
   $dir = EWIKI_DBFILES_DIRECTORY;
   $fn = "$dir/CACHE";

   #-- generate cache
   if (!file_exists("$dir/CACHE") || $rewrite) {
      $r = dzf2_all_walk();
      $f = fopen($fn, "wb");
      flock($f, LOCK_EX);
      fwrite($f, "00000027_ewiki_DZF2_DATABASE_CACHE_FILE (DO NOT EDIT!)\n" . implode("\n", $r) . "\n");
   }
   #-- read
   else {
      $f = fopen($fn, "r"); flock($f, LOCK_SH);
      $r = explode("\n", fread($f, 1<<21-1));
      flock($f, LOCK_UN); fclose($f);
      unset($r[0]);
      array_pop($r);
   }

   return($r);
}


function dzf2_cache_add($id, $n) {
   $dir = EWIKI_DBFILES_DIRECTORY;
   $fn = "$dir/CACHE";
   if (($n != 1) && ($f = fopen($fn, "ab"))) {
      flock($f, LOCK_EX);
      fwrite($f, ewiki_lowercase($id) . "\n");
      flock($f, LOCK_UN);
      fclose($f);
   }
}



function dzf2_all_walk() {
   $dir = EWIKI_DBFILES_DIRECTORY;
   $r = array();
   $main = opendir($dir);
   while ($sub = readdir($main)) {
      if ((strlen($sub)==1) && ($sub[0]!=".") && is_dir("$dir/$sub")) {
         $sub = $dir . "/" . $sub;
         $dh = opendir($sub);
         while ($fn = readdir($dh)) {
            if (($fn[0] != ".") && (strpos($fn, ".hits") != strlen($fn)-5)) {
               $fs = filesize($sub ."/". $fn);
               if ($fs && ($fs < 10)) {
                  $r[] = rawurldecode($fn);
         }  }  }
      }
   }
   return($r);
}




function dzf2_hit($id, $add=0)
{
   $dbfile = dzf2_fn($id) . ".hits";

   #-- open, read
   if ($fr = @fopen($dbfile, "r")) {
      flock($fr, LOCK_SH);
      $r = trim(fgets($fr, 10));
   }
   else {
      $r = 0;
   }
   #-- update
   if ($add) {
      if ($fr){
         flock($fr, LOCK_EX);
      }
      $r += $add;
      $fw = fopen($dbfile, "w");
      fwrite($fw, "$r");
      fclose($fw);
   }
   #-- close, return value
   if ($fr) {
      flock($fr, LOCK_UN);
      fclose($fr);
   }
   return($r);
}


?>