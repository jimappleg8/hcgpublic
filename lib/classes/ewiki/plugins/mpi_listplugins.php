<?php
 # Hans B Pufal (http://www.aconit.org/hbp/CCC/Ewiki/index.php)


$ewiki_plugins["mpi"]["listplugins"] = "ewiki_mpi_listplugins";


function ewiki_mpi_listplugins ($action, $args=array())
{

   global $ewiki_plugins;

   if ($action == "desc")
      return "Displays list of available plugins";

   if ($action == "doc")
      return "
      <p>The <b>plugins</b> plugin provides a list of the intrinsic and the added plugins.
      <p>Each plugin is named, its parameters are described and a help text is shown.
      ";

   if ($action != 'html')
      return ' [ <b>listplugins</b> cannot do "' . $action . '". ] ';


   $o = "<table>\n";
   foreach ($ewiki_plugins["mpi"] as $name=>$pf)
   {

      $o .= '<tr valign=top><td>' .
            '<a href="'.ewiki_script("mpi/$name").'">'.$name.'</a>' .
            "</td>\n<td>&nbsp;:&nbsp;</td>\n<td>&nbsp;" . $pf("desc") .
            "</td>\n<td>" . $pf('doc') .
            "</td>\n</tr>\n";

   }

   return $o .= "</table>\n";
}


?>