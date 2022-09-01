<?php

#  this is a replacement for the ewiki.php internal MySQL database access
#  interface; this one saves all WikiPages in so called "flat files", but
#  there are now two different formats you can choose from:
#    * rfc822-style (or say message/http like),
#      which leads to files you can edit with any available text editor
#    * in a compressed and faster 'binary' format,
#      which supports more functionality (hit counting)
#      enable with EWIKI_DB_FAST_FILES set to 1
#  As this plugin can read both, you are free to switch at any time.
#
#  To enable it, just include() this plugin __before__ the main/core
#  ewiki.php script using:
#
#       include("plugins/db_flat_files.php");
#
#  Alternatively (if you only will use the file database), you could go
#  to the bottom of the "ewiki.php" script and replace the 'ewiki_database'
#  function with the one found herein, BUT take care to rename it from
#  'ewiki_database_files' to just  'ewiki_database', else it won't work!
#
#  db_flat_files
#  -------------
#  The config option EWIKI_DBFILES_DIRECTORY must point to a directory
#  allowing write access for www-data (the user id, under which webservers
#  run usually), use 'chmod 757 dirname/' (from ftp or shell) to achieve this
#
#  db_fast_files
#  -------------
#  Some versions of PHP and zlib do not work correctly under Win32, so
#  you should disable it either in the php.ini, or via .htaccess:
#    php_option disable_functions "gzopen gzread gzwrite gzseek gzclose"
#
#  db_fast_files` code was contributed_by("Carsten Senf <ewiki@csenf.de>");


#-- choose flat file format
define("EWIKI_DB_FAST_FILES", 0);
define("EWIKI_DBFF_ACCURATE", 0);


#-- plugin registration
$ewiki_plugins["database"][0] = "ewiki_database_files";



function ewiki_database_files ($FUNC, $args=array(), $sw1=0, $sw2=0) {

   $r = array();

   switch (trim($FUNC)) {

      case "GET":
	if (! ($n = @$args["version"])) {
		$n = ewiki_database_files("LASTVER", $args["id"]);
	}
	if ($n && ($dbfile = ewiki_database_files("FN", $args['id'].".$n"))) {

                if ($fp = @gzopen($dbfile, "rb")) {

			$ct = gzread($fp, 1 << 21 -1);
			gzclose($fp);

			if (substr($ct,0,2) == "a:") {
				$r = unserialize($ct);
			}

			if (empty($r)) {
				$p = strpos($ct, "\012\015\012");
				$p2 = strpos($ct, "\012\012");
				if ((!$p2) || ($p) && ($p < $p2)) {
					$p = $p + 3;
				}
                                else {
					$p = $p2 + 2;
                                }
				$r["content"] = substr($ct, $p);
				$ct = substr($ct, 0, $p);

				foreach (explode("\012", $ct) as $h) {
					if ($h = trim($h)) {
						$r[trim(strtok($h, ":"))] = str_replace(EWIKI_DBFILES_NLR, "\n", trim(strtok("\000")));
					}
				}
			}
                }
	}
	break;


      case "OVERWRITE":
      case "WRITE":
	if (! ($n = $args["version"])) {
		$n = 1 + ewiki_database_files("LASTVER", $args['id']);
	}
        $dbfile = ewiki_database_files("FN", $args['id'].".$n");
	if (($FUNC=="OVERWRITE") || !file_exists($dbfile))
        {
		if (EWIKI_DB_FAST_FILES) {
			$val = serialize($args);
			if (($fp = gzopen($dbfile, "wb".EWIKI_DBFILES_GZLEVEL))) {
				gzwrite($fp, $val);
				gzclose($fp);
			}
			return(true);
		}
		else {
			$content = $args["content"];
			unset($args["content"]);
			$headers = "";
			foreach ($args as $hn=>$hv) {
				$headers .= $hn . ": " . str_replace("\n", EWIKI_DBFILES_NLR, $hv) . "\015\012";
			}
			unset($args);
			if ($fp = fopen($dbfile, "wb")) {
				flock($fp, LOCK_EX);
				fputs($fp, $headers . "\015\012" . $content);
				flock($fp, LOCK_UN);
				fclose($fp);
				return(true);
			}
		}
         }
         break;


      case "HIT":
	if (EWIKI_DB_FAST_FILES) {
		$dbfile = ewiki_database_files("FN", $args['id'].".1");
		if ($fp = gzopen($dbfile, "rb")) {
			$r = unserialize(gzread($fp, 1 << 20));
			gzclose($fp);
			if ($r) {
				$r["hits"] += 1;
				if ($fp = gzopen($dbfile, "wb".EWIKI_DBFILES_GZLEVEL)) {
					gzwrite($fp, serialize($r));
					gzclose($fp);
				}
			}
		}
	}
	else {
		#nop
	}
	break;


      case "FIND":
         foreach ($args as $id) {
            $r[$id] = file_exists( ewiki_database_files("FN", $id.".1") ) ? 1 : 0;
            if (EWIKI_DBFF_ACCURATE && $r[$id] && strpos($id, "://")) {
               $uu = ewiki_database_files("GET", array("id"=>$id));
               ($uu["meta"]) and ($r[$id]=$uu["meta"]);
            }
         }
         break;


      case "GETALL":
         $r = new ewiki_dbquery_result($args);
         foreach (ewiki_database_files("ALLFILES") as $id) {
            $r->entries[] = $id;
         }
         break;


      case "SEARCH":
         $field = implode("", array_keys($args));
         $content = implode("", array_values($args));
         $r = new ewiki_dbquery_result(array($field));
         foreach (ewiki_database_files("ALLFILES") as $id) {
            $page = ewiki_database_files("GET", array("id"=>$id));
            if ((stristr($page[$field], $content)!==false) || ($content == $page[$field])) {
               $r->add($page);
            }
         }
         break;


      case "INIT":
         if (!is_writeable(EWIKI_DBFILES_DIRECTORY) || !is_dir(EWIKI_DBFILES_DIRECTORY)) {
            mkdir(EWIKI_DBFILES_DIRECTORY)
            or die("db_flat_files: »database« directory '".EWIKI_DBFILES_DIRECTORY."' is not writeable!\n");
         }
         break;


      #-- db_plugin internal ---------------------------------------------- 


      case "LASTVER":    // helper subfunction
         $find = ewiki_database_files("FN", "$args", "_NOPATH=1");
         $find_n = strlen($find);
         $n = 0;
         if ($find_n) {
            $dh = opendir(EWIKI_DBFILES_DIRECTORY);
            while ($fn = readdir($dh)) {
               if ( (strpos($fn, $find) === 0) &&     //@FIXME: empty delimiter
                    ($dot = strrpos($fn, ".")) && ($dot == $find_n) &&
                    ($uu = substr($fn, ++$dot)) && ($uu > $n)  )
               {
                  $n = $uu;
               }
         }  }
         return($n);


      case "ALLFILES":
         $dh = opendir(EWIKI_DBFILES_DIRECTORY);
         while ($fn = readdir($dh)) {
            if (is_file(EWIKI_DBFILES_DIRECTORY . "/" . $fn)) {
               $id = ewiki_database_files("ID", $fn);
               if (($dot = strrpos($id, ".")) && (substr($id, $dot+1) >= 1)) {
                  $file = substr($id, 0, $dot);
                  $r[$file] = $file;
               }
            }
         }
         closedir($dh);
         break;


      case "DSR":  // convert from database id/name to a valid filename
      case "FN":
         if (!EWIKI_DBFILES_ENCODE) {
            $args = strtr($args, '/:', '\\:');
         } else {
            $args = urlencode($args);
         }
         return(($sw1?"":EWIKI_DBFILES_DIRECTORY.DIRECTORY_SEPARATOR) . $args);

      case "ID":
         if (!EWIKI_DBFILES_ENCODE) {
            $args = strtr($args, '\\:', '/:');
         } else {
            $args = urldecode($args);
         }
         return($args);


      #-- used by tools/ -----------------------------------------------

      case "DELETE":
         $fn = ewiki_database_files("FN", $args["id"].".".$args["version"]);
         @unlink($fn);
         break;
   }

   return($r);
}




 #-- fake zlib
 if (!function_exists("gzopen")) {

    function gzopen($fp, $mode) {
       $mode = preg_replace('/[^carwb+]/', '', $mode);
       return(fopen($fp, $mode));
    }

    function gzread($fp, $len) {
       return(fread($fp, $len));
    }

    function gzwrite($fp, $string) {
       return(fwrite($fp, $string));
    }

    function gzseek($fp, $arg2) {
       return(fseek($fp, $arg2));
    }

    function gzclose($fp) {
       return(fclose($fp));
    }

 }



?>