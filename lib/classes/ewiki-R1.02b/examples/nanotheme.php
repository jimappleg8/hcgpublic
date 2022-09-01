<?php

 #-- open database connection,
 #   and load ewiki.php 'library'
 define("EWIKI_SCRIPT", "nanotheme.php?id=");
 include("config.php");

 #-- this is the actual call to generate the output for the current wiki
 #   page, but we buffer it now and print its output later
 $ewiki_page = ewiki_page();

?><?php

 #-- this is special to this example
 #   illustrates how to use the ewiki_format() function standalone
 if (($f = @$_REQUEST["parsefile"]) == "README") {
    $R = implode("%%%", file($ewiki_title=$f));
    $ewiki_page = ewiki_format($R, 1);
 }

?>
<HTML>
<HEAD>
 <TITLE>ErfurtWiki: <?php  echo($ewiki_title);   ?></TITLE>
 <STYLE TYPE="text/css">
 <!--
 a {text-decoration:underline;}
 //   .box { border:#770000 solid 1px; padding:2px; }
 // input,textarea,input:file { border:2px #000000 solid; background-color:#B4D3D7; }
 //-->
 </STYLE>
 <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
</HEAD>
<BODY BGCOLOR="#608A8E" TEXT="#000000" LINK="#204A4E" VLINK="#204A4E" ALINK="#204A4E" LEFTMARGIN="10" TOPMARGIN="10" MARGINWIDTH="10" MARGINHEIGHT="10">

<TABLE BGCOLOR="#FFFFFF" ALIGN="center" CELLSPACING="0" CELLPADDING="17"
STYLE="-moz-border-radius:10px; border:2px #000000 solid;">

<TR><TD COLSPAN="2" VALIGN="TOP" STYLE="margin-bottom:0px; padding-bottom:0px;">
<H1 style="padding:0px; margin:0px; font:50px/50px bold Arial,Helvetica,sans-serif; font-stretch:extra-expanded; color:#426063;">
<IMG SRC="../squirrel.jpeg" ALT=":@" WIDTH="64" HEIGHT="64" ALIGN="BOTTOM">
ErfurtWiki
</H1>
<HR STYLE="color:#629093; height:1px; margin:0px; padding:0px;" WIDTH="100%" NOSHADE COLOR="#629093">
</TD></TR>

<TR>

<TD WIDTH="190" VALIGN="TOP">

<table width="98%" cellspacing="1" cellpadding="1" bgcolor="#000000">
<tr><td bgcolor="#608A8E" align="center"><font color="#FFFFFF"><b>ErfurtWiki</b></font></td></tr>
<tr><td bgcolor="#F0F0F0">
» <A HREF="./README">README</A><BR>
» <A HREF="http://erfurtwiki.sourceforge.net/">project site</A><BR>
» <A HREF="http://freshmeat.net/projects/ewiki">fm project page</A><BR>
» <A HREF="http://sourceforge.net/projects/erfurtwiki">sf project page</A><BR>
» <A HREF="http://erfurtwiki.sourceforge.net/downloads/">downloads/</A><BR>
</td></tr></table>

<BR>
<table width="98%" cellspacing="1" cellpadding="1" bgcolor="#000000">
<tr><td bgcolor="#608A8E" align="center"><font color="#FFFFFF"><b>examples</b></font></td></tr>
<tr><td bgcolor="#F0F0F0">
» <a href="../example-1.php">squirrel theme</a><br>
<?php
   #-- the example layouts menu
   if ($dh = opendir("examples")) while ($fn = readdir($dh)) {
      if (strpos($fn, ".php")) {
         echo "» <a href=\"$fn\">" . strtok($fn, ".") . "</a><br>\n";
      }
   }
?>
</td></tr></table>

<BR>
<table width="98%" cellspacing="1" cellpadding="1" bgcolor="#000000">
<tr><td bgcolor="#608A8E" align="center"><font color="#FFFFFF"><b>db tools</b></font></td></tr>
<tr><td bgcolor="#F0F0F0">
» <A HREF="./tools/ewiki_flags.php">set page flags</A><BR>
» <A HREF="./tools/ewiki_backup.php">backup util</A><BR>
» <A HREF="./tools/ewiki_remove.php">page deletion</A><BR>
» <A HREF="./tools/ewiki_backdown.php">backdown util</A><BR>
» <A HREF="./tools/ewiki_convertdb.php">convert db</A><BR>
» <A HREF="./tools/ewiki_holes.php">make holes</A><BR>
» <A HREF="./tools/checklinks.php">check links</A><BR>
</td></tr></table>

<BR>
<table width="98%" cellspacing="1" cellpadding="1" bgcolor="#000000">
<tr><td bgcolor="#608A8E" align="center"><font color="#FFFFFF"><b>internal pages</b></font></td></tr>
<tr><td bgcolor="#F0F0F0">
<?php
  foreach ($ewiki_plugins["page"] as $id=>$pf) {
     echo '» <A HREF="?id=' . $id . '">' . $id . '</A><BR>' . "\n";
  }
?>
</td></tr></table>

<BR>
<?php
  if (function_exists("calendar_exists") && calendar_exists()) {
     echo calendar();
  }
?>

</TD><TD WIDTH="560" VALIGN="TOP" STYLE="line-height:133%"><?php

 #-- output previously generated page
 echo($ewiki_page);

?>
</TD><?php

  if (file_exists($sf = "sftail.php")) {
     echo '<TD WIDTH="150" VALIGN="top">';
     echo '<table width="98%" cellspacing="1" cellpadding="1" bgcolor="#000000"><tr><td bgcolor="#608A8E" align="center"><font color="#FFFFFF"><b>sourceforge.net</b></font></td></tr><tr><td bgcolor="#F0F0F0">';
     $sfsummary = "full";
     include($sf);
     echo '</td></tr></table>';
     echo '</TD>';
  }

?>
</TR>
</TABLE>
<br>
<center><small><small>
This theme was 'borought' from <a href="http://nanoweb.si.kz/">http://nanoweb.si.kz/</a> - home of the Nanoweb HTTP Server (completely written in PHP); check it out for optimal performance and programming flexibility.</small></small></center>
</BODY>
</HTML>