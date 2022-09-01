<?php

include("../config.php");



if (!empty($_REQUEST["fetch"]))  {

   $ext = $_REQUEST["fetch"];
   header("Content-Type: application/octet-stream");
   header("Content-Disposition: attachment; filename=\"ewiki_transfer.$ext\"");
   ob_start("ob_gzhandler");

   echo "EWBF00000025";

   $result = ewiki_database("GETALL", array());
   while ($row = $result->get()) {

      $id = $row["id"];
      for ($v=$row["version"]; $v>0; $v--) {

         $row = ewiki_database("GET", array("id"=>$id, "version"=>$v));

         if ($_REQUEST["textonly"]
             && (EWIKI_DB_F_TEXT != ($row["flags"] & EWIKI_DB_F_TYPE)) )
         {
            continue;
         }

         if ($row && ($row = serialize($row))) {
             echo "\n" . strlen($row) . "\n" . $row;
         }

      }
   }
}
elseif (!empty($_FILES["data"])) {

#error_reporting(E_ALL);
   $i = gzopen($_FILES["data"]["tmp_name"], "rb");

   $n = 0;

   while ($i && !gzeof($i)) {

      /*stripCRLF*/ gzgets($i, 4096);
      $count = gzgets($i, 4096);
      if (($count === false) || (($count = trim($count)) <= 0)) {

         if (gzeof($i)) {
            gzclose($i);
            die("finished reading $n entries");
         }
         else {
            die("file broken (zero count block) after $n entries");
         }
      }

      $row = gzread($i, $count);
      $row = unserialize($row);

      if (ewiki_database("WRITE", $row)) {
         echo $row["id"] .".". $row["version"] . " &nbsp;\n";
      }

      $n++;
   }

}
else {

   ?><html><head><title>make binary backup of whole database</title></head>
     <body bgcolor="#778899"><h3>database dump</h3>
     If you cannot make use of the <b>ewikictl</b> cmdline utility, and need
     a way to transfer the whole database from one server to another, you
     can make a downloadable binary dump using this util.

     <h4>generate dump</h4>
     <a href="<?php echo "$PHP_SELF?fetch=dat.gz"; ?>">download dump</a><br>
     <a href="<?php echo "$PHP_SELF?textonly=1&fetch=dat.gz"; ?>">text pages only dump</a><br>

     <h4>reinsert dump</h4>
     <form action="<?php echo $PHP_SELF; ?>" method="POST" enctype="multipart/form-data">
       <input type="file" name="data">
       <br> <input type="submit" value="upload">
     </form>
     </body></html>
   <?php

}

?>