<?php

 # this is the ewiki database abstraction function utilizing the
 # ADOdb database abstraction layer,
 # you need to open a database connection in yourself, this plugin
 # assumes the connection link is stored in the global '$db' var
 # - ADODB_FETCH_ASSOC will get set as default, if you use this


 #-- register this plugin
 $ewiki_plugins["database"][0] = "ewiki_database_adodb";


#  #-- open db link here, if not already done
#  include(".../adodb/adodb.inc.php");
#  $db = AdoNewConnection("mysql");
#  $db->Connect("localhost", "root", "", "test");
#



function ewiki_database_adodb($ACTION, $args=array()) {

   $db = & $GLOBALS["db"];                  # change here, if another name

   $db->setFetchMode(ADODB_FETCH_ASSOC);    # no better way to achieve this
                                            # with current ADOdb

   $r = array();

   switch ($ACTION) {

      case "GET":
         $id = $db->qstr($args["id"]);
         ($version = 0 + @$args["version"]) and ($version = "AND (version=$version)") or ($version="");
         $result = $db->Execute($s="SELECT * FROM " . EWIKI_DB_TABLE_NAME
            . " WHERE (pagename = $id) $version ORDER BY version DESC"
         );
         if ($result && ($r = $result->fetchRow())) {
            $r["id"] = $r["pagename"];
            unset($r["pagename"]);
         }
         break;


      case "WRITE":
         $args["pagename"] = $args["id"]; unset($args["id"]);
         $sql1 = $sql2 = "";
         foreach ($args as $index=>$value) {
            if (is_int($index)) continue;
            $a = ($sql1 ? ', ' : '');
            $sql1 .= $a . $index;
            $sql2 .= $a . $db->qstr($value);
         }
         $result = $db->Execute("INSERT INTO " . EWIKI_DB_TABLE_NAME .
            " (" . $sql1 . ") VALUES (" . $sql2 . ")"
         );
         return($result);
         break;


      case "FIND":
         $sql = "";
         foreach (array_values($args) as $id) if (strlen($id)) {
            $r[$id] = 0;
            $sql .= ($sql ? " OR " : "") .
                    "(pagename=" . $db->qstr($id) .  ")";
         }
         $result = $db->Execute($s = "SELECT pagename AS id FROM " . EWIKI_DB_TABLE_NAME .
            " WHERE  $sql"
         );
         while ($result && (!$result->EOF) && ($row = $result->FetchRow())) {
              $r[$row["id"]] = $row["meta"] ? $row["meta"] : 1;
         }
         break;


      case "HIT":
         $db->execute("UPDATE " . EWIKI_DB_TABLE_NAME . " SET hits=(hits+1) WHERE pagename=" . $db->qstr($args["id"]) );
         break;


      case "GETALL":
         $result = $db->execute("SELECT pagename AS id, ".implode(", ",$args)
            ." FROM " . EWIKI_DB_TABLE_NAME . " ORDER BY version DESC");
         while ($result && (!$result->EOF) && ($row = $result->FetchRow())) {
            if (empty($r[$id=$row["id"]]))
            $r[$id] = $row;
         }
         break;


      case "SEARCH":
         $field = implode("", array_keys($args));
         $content = strtolower(implode("", $args));
         if ($field == "id") { $field = "pagename"; }
         $result = $db->execute("SELECT pagename AS id, $field FROM " . EWIKI_DB_TABLE_NAME .
            " WHERE LOCATE(" . $db->qstr($content) .
            ", LCASE($field))  ORDER BY version DESC "
         );
         while ($result && (!$result->EOF) && ($row = $result->fetchRow()) && ($id = $row["id"]) && (empty($row[$id]))) { $r[$id] = $row; }
         break;


      case "DELETE":
         $id = $db->qstr($args["id"]);
         $version = $args["version"];
         $db->execute("DELETE FROM TABLE " . EWIKI_DB_TABLE_NAME ."
            WHERE pagename='$id' AND VERSION=$version");
         break;


      case "INIT":
         $db->execute("CREATE TABLE " . EWIKI_DB_TABLE_NAME ."
            (pagename VARCHAR(160) NOT NULL,
            version INTEGER UNSIGNED NOT NULL DEFAULT 0,
            flags INTEGER UNSIGNED DEFAULT 0,
            content MEDIUMTEXT,
            author VARCHAR(100) DEFAULT 'ewiki',
            created INTEGER UNSIGNED DEFAULT ".time().",
            lastmodified INTEGER UNSIGNED DEFAULT 0,
            refs TEXT,
            meta TEXT,
            hits INTEGER UNSIGNED DEFAULT 0,
            PRIMARY KEY id (pagename, version) )
            ");
         break;
    







      default:
         echo("adodb-nyi($ACTION)");

   }

   return($r);
}

?>