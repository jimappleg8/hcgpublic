<?php

/*/

 This plugin can read-access the PhpWiki v1.3.x database tables,
 it is mainly used for conversion from PhpWiki to ErfurtWiki - and you
 should not rely on it for daily work, as the PhpWiki tables will
 probably always become inconsistent due to the rather simple access
 approach used herein.

 The code is mainly based upon the PhpWiki database scheme and some
 experiments with an existing setup (after five hours of PhpWikiSetup).

 * no, I'm not going to implement this for anything else than mysql!
 * and yes, I highly recommend to use it read-only

/*/

 define("PHPWIKI13_WRITEACCESS", 0);

 $ewiki_plugins["database"][0] = "ewiki_database_phpwiki13";
 define("EWIKI_PAGE_INDEX", "WikiWikiWeb");
 



 function ewiki_database_phpwiki13 ($FUNC=0, $args=0) {

    #<off>#    mysql_ping(GLOBALS["db"]);

    $r = array();

    if (($FUNC=="WRITE") && (!PHPWIKI13_WRITEACCESS)) {
       die("The plugins/db_phpwiki13 interface is meant for READ ONLY access to PhpWiki databases. To enable write access you first need to set the PHPWIKI13_WRITEACCESS configuration constant. But beware that this may make your database inconsistent and thus could prevent PhpWiki to reuse it afterwards!\n");
    }


    switch ($FUNC) {


       case "GET":
          ($ver = $args["version"]) and ($ver_sql = " AND version=$ver") or ($ver_sql = "");
          $id = mysql_escape_string($args["id"]);
          if ($result = mysql_query("SELECT * FROM version LEFT JOIN page USING (id) WHERE pagename='$id' $ver_sql ORDER BY version DESC")) {
             $row = mysql_fetch_array($result);

             #-- decode meta data
             $dec1 = unserialize($row["pagedata"]);
             $dec2 = unserialize($row["versiondata"]);
             $dec1["markup"] = $dec2["markup"];
             $r = array(
                "id" => $row["pagename"],
                "version" => $row["version"],
                "content" => $row["content"],
                "author" => $dec2["author"],
                "lastmodified" => $row["mtime"],
                "hits" => $row["hits"],
                "flags" => EWIKI_DB_F_TEXT,
                "created" => ($uu=$dec1["created"])?$uu:0,
                "refs" => "\n",
                "meta" => $dec1
             );

             #-- get flags
             if ($dec1["locked"]=="yes") { 
                $r["flags"] |= EWIKI_DB_F_READONLY;
             }
             if (is_int($flags = $dec1["flags"])) {
                $r["flags"] = $flags;
             }
             unset($r["meta"]["flags"]);
             unset($r["meta"]["created"]);
             unset($r["meta"]["locked"]);
             $r["meta"] = serialize($r["meta"]);

             #-- fetch $refs[]
             $num_id = $row["id"];
             if ($result = mysql_query("SELECT p2.pagename FROM link LEFT JOIN page p1 ON (link.linkfrom=p1.id) LEFT JOIN page p2 ON (p2.id=link.linkto) WHERE p1.id=$num_id")) {
                while ($row = mysql_fetch_array($result)) {
                   $r["refs"] .= $row["pagename"] . "\n";
                }
             }
          }
          break;


       case "HIT":
          mysql_query("UPDATE page SET hits=(hits+1) WHERE pagename='" . mysql_escape_string($args["id"]) . "'");
          break;


       case "FIND":
          $sql = "";      # returns: array("WikiPage"=>exists)
          foreach (array_values($args) as $id) if (strlen($id)) {
             $r[$id] = 0;
             $sql .= ($sql ? " OR " : "") .
                     "(pagename = '" . mysql_escape_string($id) .  "')";
          }
          $result = mysql_query($sql = "SELECT pagename AS id FROM page WHERE $sql");
          while ($result && ($row = mysql_fetch_row($result))) {
             $r[$row[0]] = 1;
          }
          break;


      case "GETALL":
          $args[] = "id";

          foreach (ewiki_database_phpwiki13("ALLFILES") as $pagename) {
             $row = ewiki_database_phpwiki13("GET", array("id"=>$pagename));
             $z = array();
             foreach ($args as $a) {
                $z[$a] = $row[$a];
             }
             $r[$pagename] = $z;
          }
          break;


      case "SEARCH":   #-- taken from db_flat_files
         foreach (ewiki_database_phpwiki13("ALLFILES") as $id) {
            $page = ewiki_database_phpwiki13("GET", array("id"=>$id));
            $check = true;
            foreach ($args as $field=>$content) {
               $check &= (stristr($page[$field], $content)!=false) || ($content == $page[$field]);
            }
            if ($check) {
               $r[] = $page;
            }
         }
         break;


      #-- better not use this

      case "WRITE":

         extract($args);

         #-- num id retrieval
         $id = addslashes($id);
         if (  ($result = mysql_query("SELECT id FROM page WHERE pagename='$id'"))
               and ($row = mysql_fetch_array($result))  ) {
            $num_id = $row["id"];
         }
         else {
            $result = mysql_query("SELECT id FROM page ORDER BY id DESC");
            $row = mysql_fetch_array($result);
            if ($num_id = $row["id"]) {
               $num_id++;
               if (! ($result = mysql_query("INSERT INTO page (id, pagename, hits, pagedata) VALUES ($num_id, '$id', 0, '')")) ) {
                  die("db_phpwiki13: could not create new num_id for page\n");
               }
            }
            else {
               die("db_phpwiki13: could not fetch num_id for page\n");
            }
         }

         #-- split data into parts
         $meta = unserialize($meta);
         ($markup = $meta["markup"]) or ($markup = 2);
         unset($meta["markup"]);
         $versiondata = array(
            "markup" => $markup,
            "author" => $author,
            "author_id" => $author,
         );
         $versiondata = addslashes(serialize($versiondata));

         $pagedata = $meta;
         $pagedata["created"] = $created;
         $pagedata["flags"] = $flags;
         if ($flags & EWIKI_DB_F_READONLY) {
            $pagedata["locked"] = "yes";
         }
         $pagedata = addslashes(serialize($pagedata));

         #-- save content
         $content = addslashes($content);
         $result =
            mysql_query("INSERT INTO version (id, version, mtime, minor_edit, content, versiondata) VALUES ($num_id, $version, $lastmodified, 0, '$content', '$versiondata')")
            &&
            mysql_query("UPDATE recent SET latestversion=$version WHERE id=$num_id")
            &&
            mysql_query("UPDATE page SET pagedata='$pagedata' WHERE id=$num_id")
            &&
            mysql_query("REPLACE INTO nonempty (id) VALUES ($num_id)");
         if (!$result)  {
            break;
         }

         #-- encode $refs[] into relational database
         mysql_query("DELETE FROM link WHERE linkfrom=$num_id");
         $refs = array_unique(explode("\n", $refs));
         foreach ($refs as $pagename) {
            $pagename = addslashes($pagename);
            $row = mysql_fetch_array(mysql_query("SELECT id FROM page WHERE pagename='$pagename'"));
            if ($to = $row["id"]) {
               mysql_query($sql="REPLACE INTO link (linkfrom, linkto) VALUES ($num_id, $to)");
            }
         }

         return(true);
         break;



      #-- fully unsupported stuff

      case "INIT":
          die("You cannot create a PhpWiki v1.3 database using this plugin! Please use the default database structure of ErfurtWiki, you're better off with it!");
          break;

      case "DELETE":
          die("This interface would probably garbage your PhpWiki v1.3 database, so your issued 'DELETE' action will not be executed.");
          break;

      default:
          die("Not all features can be used with PhpWiki v1.3 databases.");
          break;


      #-- helper stuff

      case "ALLFILES":
          $r = array();
          $result = mysql_query("SELECT pagename FROM nonempty NATURAL LEFT JOIN page");
          while ($result && ($row = mysql_fetch_array($result))) {
             $r[] = $row["pagename"];
          }
          break;

    }

#print_r($r);
#echo mysql_error() . " ";

    return($r);

 }



?>