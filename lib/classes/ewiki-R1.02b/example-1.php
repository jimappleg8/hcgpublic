<?php

  #-- load lib, override settings
  $ewiki_config["qmark_links"] = "0u+";
  include("config.php");
  
  #-- get current page
  $CONTENT = ewiki_page();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title><?php echo EWIKI_NAME . ":" . $ewiki_title; ?></title>
<link rel="stylesheet" href="wiki.css" type="text/css">
<?php
  #-- per-page stylesheets
  include("fragments/css.php");
?>
<link rel="stylesheet" href="example-1.css" type="text/css">
<?php
  #-- <meta> headers
  include("fragments/head/core.php");
  include("fragments/head/meta.php");
?>
</head>
<body text="#010203" bgcolor="#FECE8A">

<h1 class="site-title"><nobr><a href="."><img src="tlogo.png" width="150" height="100" border="0" alt="ewiki" align="middle"></a>
<?php echo str_replace("Wiki", "<span>Wiki</span>", EWIKI_NAME); ?>
</nobr></h1>

<table border="0" cellpadding="0" cellspacing="0" width="85%">
<tr>
 <td valign="top" width="200" class="main-menu" style="width:200px;" bgcolor="#FEAA56">

 <div class="main-menu">
  <h4>ewiki info</h4>
   <a href="?id=README">README</a><br />
   <a href="./CHANGES">ChangeLog</a><br />
   <a href="http://erfurtwiki.sourceforge.net/">project site</a><br />
   <a href="http://freshmeat.net/projects/ewiki">fm project page</a><br />
   <a href="http://ewiki.berlios.de/">secondary site</a><br />
 </div>

 <?php
      include("fragments/blocks/mainmenu.php");
 ?>

 <div class="main-menu">
  <h4>useful tools</h4>
   <a href="./tools/">database / admin</a><br />
   <a href="./tools/t_commander/">WikiCommander</a><br />
   <a href="?PlugInstall">PlugInstall</a><br />
   <a href="?TextUpload">TextUpload</a><br />
   <a href="?RecentChanges">RecentChanges</a><br />
 </div>

 <div class="main-menu">
  <h4>sample layouts</h4>
   <?php
      #-- the example layouts menu
      if ($dh = opendir("examples")) while ($fn = readdir($dh)) {
         if (strpos($fn, ".php") && !strpos($fn, "onfig.php")) {
            echo "<a href=\"examples/$fn/?id=$ewiki_id\">" . strtok($fn, ".") . "</a><br />\n";
         }
      }
   ?>  
 </div>

 <div class="main-menu">
  <h4>page plugins</h4>
   <?php
     foreach ($ewiki_plugins["page"] as $id=>$pf) {
        echo '<a href="' . ewiki_script("", $id) . '">' . $id . '</a><br />' . "\n";
     }
   ?>
 </div>
 
<br>
<br>
<br>
</td>
<td valign="top" width="100%" bgcolor="#FEAA56">
<?php

  #-- output page
  echo $CONTENT;

?>
<br><br>
</td>
</tr>
</table>
<p class="site-footer" align="center">
<small>
... <br>
... <br>
<br>
&nbsp;<br>
&nbsp;
</small>
</p>
</body></html>
  