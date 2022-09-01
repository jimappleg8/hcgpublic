<?php
  # this mpi allows you to insert another wikipage into the current
  # one using <plugin: insert InsertThisWikiPage>
  # ''milky''

  define("EWIKI_MPI_INSERT_TBL", 1);


$ewiki_plugins["mpi"]["insert"] = "ewiki_mpi_insert";


function ewiki_mpi_insert($action="html", $args=array()) {

   switch ($action) {
      case "doc": return("The <b>insert</b> plugin allows you to insert the contents of another WikiPage into the current one.");
      case "desc": return("insert another WikiPage");

      default:
         $prevG = $GLOBALS;
         $o = ewiki_page(implode("", array_keys($args)));
         if (EWIKI_MPI_INSERT_TBL) $o = '<table border="1" cellpadding="5" cellspacing="5"><tr><td>' . $o . '</td></tr></table>';
         $o = '<div class="ew_insert">' . $o . '</div>';
         $GLOBALS = $prevG;
         unset($prevG);
   }

   return($o);
}

?>