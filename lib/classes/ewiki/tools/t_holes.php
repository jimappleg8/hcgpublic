<?php
  include("t_config.php");
?>
<html>
<head>
<title>strip old versions of ewiki pages</title>
</head>
<body BGCOLOR="#778899">
<h3>create page version holes</h3>
<?php


define("N_PAGE_VERSIONS", 1);


  if (empty($_REQUEST["range"])) {

     echo '
This tool can be used to remove old page versions from the database, if
they just slow down your wiki. For a db_flat_files/db_fast_files powered
ewiki you could just delete the files from the database directory.
<br><br>
<form action="t_holes.php" method="POST">
<table border="0" cellpadding="2" cellspacing="3">
';

     $result = ewiki_database("GETALL", array());
     while ($row = $result->get()) {

        if (($n=$row["version"]) >= N_PAGE_VERSIONS) {

           $id = $row["id"];

           echo '<tr>';
           echo "<td bgcolor=\"#DDDDEE\">$id (#$n)</td>";
           $n2 = $n - 10;
           echo '<td bgcolor="#EEDDDD"> <input type="checkbox" name="id['.$id.']" value="1">'.
                ' delete versions ' .
                '<input name="range['.$id.']" value="2-'.$n2.'" size="7"> </td>';
           echo "</tr>\n";

        }

     }

     echo "</table>\n";

     echo '<br><input type="submit" value="strip page versions"><br>';

  }
  else {

     echo "purging page versions:<br>";

     $range = $_REQUEST["range"];

     foreach ($_REQUEST["id"] as $id => $go) {
        if ($go) {

           if (preg_match('/^\s*(\d+)[\s-._:]+(\d+)\s*$/', $range[$id], $uu)) {

              $n0 = $uu[1];
              $n2 = $uu[2];
              echo "'$id' versions $n0..$n2<br>";

              for ($v=$n0; $v<=$n1; $v++) {

                 ewiki_database("DELETE", array("id"=>$id, "version"=>$v));

              }
              
           }
           else {

              echo "wrong range param for '$id'!<br>";

           }

        }
     }

  }



?>