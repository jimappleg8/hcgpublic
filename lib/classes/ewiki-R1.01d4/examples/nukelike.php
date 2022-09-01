<?php

 define("EWIKI_SCRIPT", "nukelike.php?name=");
 define("EWIKI_CONTROL_LINE", 1);

 include("../config.php");

 $content = ewiki_page();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- This file is exceptionally distributed under the GPL license -->
<html>
<head>
 <TITLE>Nuke-embedded ErfurtWiki: <?php  echo($ewiki_title);  ?></TITLE>
 <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<STYLE TYPE="text/css">
<!--
FONT,TD,BODY	{FONT-FAMILY: Verdana,Helvetica; FONT-SIZE: 12px}
A:link          {BACKGROUND: none; COLOR: #000000; TEXT-DECORATION: underline}
A:active        {BACKGROUND: none; COLOR: #000000; TEXT-DECORATION: underline}
A:visited       {BACKGROUND: none; COLOR: #000000; TEXT-DECORATION: underline}
A:hover         {BACKGROUND: none; COLOR: #000000; TEXT-DECORATION: underline}
.content 	{BACKGROUND: none; COLOR: #000000;}
.option 	{BACKGROUND: none; COLOR: #000000; FONT-SIZE: 14px; FONT-WEIGHT: bold; FONT-FAMILY: Verdana, Helvetica; TEXT-DECORATION: none}
-->
</STYLE>
</head>
<body bgcolor="#505050" text="#000000" link="#363636" vlink="#363636" alink="#d5ae83">
<br>
<table cellpadding="0" cellspacing="0" width="99%" border="0" align="center" bgcolor="#ffffff">
<tr>
<td bgcolor="#ffee99" valign="middle" align="center" width="300" height="90" style="height:90px">
<h1><a href="nukelike.php"><big><big>ErfurtWiki</big></big></a></h1>
</td>
<td bgcolor="#999999">&nbsp;</td>
<td bgcolor="#cfcfbb" align="center">
<center><form action="about:blank" method="post"><font class="content" color="#000000"><b>Search </b>
<input type="text" name="query" size="14"></font></form></center></td>
<td bgcolor="#cfcfbb" align="center">
<center><form action="about:blank" method="get"><font class="content"><b>Topics </b>
<select name="topic"onChange='submit()'>
<option value="">All Topics</option>
<option  value="2">ErfurtWiki</option>
<option  value="5">PHPWiki</option>
</select></font></form></center></td>
<td bgcolor="#cfcfbb" valign="top">&nbsp;</td>
</tr></table>
<table cellpadding="0" cellspacing="0" width="99%" border="0" align="center" bgcolor="#fefefe">
<tr>
<td bgcolor="#000000" colspan="4" style="height:1px"><hr color="#000000" noshade style="display:none"></td>
</tr>
<tr valign="middle" bgcolor="#dedebb">
<td width="15%" nowrap>&nbsp;&nbsp;<b>Example-4 &nbsp;&nbsp;
<?php

   if (($name = $_REQUEST["name"]) or ($name = EWIKI_PAGE_INDEX)) {
      echo '<A HREF="example-4.php?name=edit/' . $name . '">EditThisPage</A>';
   }

?>
</b>
</td>
<td align="center" height="20" width="70%" nowrap>
&nbsp; ErfurtWiki
&nbsp; inside
&nbsp; a
&nbsp; NukePortal
&nbsp; -
&nbsp; looks
&nbsp; much
&nbsp; nicer
&nbsp; with 
&nbsp; images
&nbsp; ;-&gt;
</td>
<td width="15%">&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td bgcolor="#000000" colspan="4" style="height:1px"><hr color="#000000" noshade style="display:none"></td>
</tr>
</table>

<!-- FIN DEL TITULO -->
<table width="99%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" align="center"><tr valign="top"><td bgcolor="#ffffff">&nbsp;</td></tr></table>
<table width="99%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" align="center"><tr valign="top">
<td bgcolor="#ffffff">&nbsp;</td>

<td bgcolor="#ffffff" width="150" valign="top">
<table border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" width="150"><tr><td>
<table border="0" cellpadding="3" cellspacing="0" bgcolor="#dedebb" width="100%"><tr><td align=left>
<font class="content" color="#363636"><b>Main Menu</b></font>
</td></tr></table></td></tr></table>
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="150">
<tr valign="top"><td bgcolor="#ffffff">
<font class="content"><strong><big>·</big></strong> <a href="example-4.php">Home</a><br>
<strong><big>·</big></strong> <a href="README">README</a><br>
<strong><big>·</big></strong> <a href="http://freshmeat.net/projects/ewiki">fm project page</a><br>
<br><b>Other Options:</b><small><br><br></small>
<b><big>·</big></b> <a href="../example-1.php">squirrel theme</a><br>
<?php
   #-- the example layouts menu
   if ($dh = opendir("examples")) while ($fn = readdir($dh)) {
      if (strpos($fn, ".php")) {
         echo "<b><big>·</big></b> <a href=\"$fn\">" . strtok($fn, ".") . "</a><br>\n";
      }
   }
?>
</font></td></tr></table>
<br>


<table border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" width="150"><tr><td>
<table border="0" cellpadding="3" cellspacing="0" bgcolor="#dedebb" width="100%"><tr><td align=left>
<font class="content" color="#363636"><b>Who is online</b></font>
</td></tr></table></td></tr></table>
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="150">
<tr valign="top"><td bgcolor="#ffffff">
<center><font class="content">Currently <b>1</b> User(s)<br><br>
But even if you're an unregistered user, you can edit all the pages inside the
wiki!
</font></center>
</td></tr></table>
<br>
</td>

<td>&nbsp;&nbsp;</td>

<td width="100%">

<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#cfcfbb"><tr><td>
<table width="100%" border="0" cellspacing="1" cellpadding="8" bgcolor="#efefef"><tr><td>
<font class="content">
At the bottom of every page you'll find a link to edit it - if
you can make any contributions, just do.
</font></td></tr></table></td></tr></table>
<br>

<table border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" width="100%"><tr><td>
<table border="0" cellpadding="3" cellspacing="0" bgcolor="#cfcfbb" width="100%"><tr><td align="left">
<font class="option" color="#363636"><b>
<?php 

  #-- print the title inside a NukeTitleBox
  #
  echo $ewiki_title;


?>
</b></font>
</td></tr></table>
</td></tr></table>

<?php

  #-- here we'll strip the <h1>title</h1> from
  #   the already fetched wiki page, as we've
  #   printed the title alread (see above)
  #

  list($uu, $content) = explode("\n", $content, 2);

  echo $content;


?>

</td>

<td>&nbsp;&nbsp;</td>

<td valign="top" width="150">
<table border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" width="150"><tr><td>
<table border="0" cellpadding="3" cellspacing="0" bgcolor="#dedebb" width="100%"><tr><td align=left>
<font class="content" color="#363636"><b>Newest Wiki Pages</b></font>
</td></tr></table></td></tr></table>
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="150">
<tr valign="top"><td bgcolor="#ffffff">

<?php

   $content = ewiki_page(EWIKI_PAGE_NEWEST);
   $content = substr($content, strpos($content, "\n"));
   echo $content;

?>

</td></tr></table>
<br>


<table border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" width="150"><tr><td>
<table border="0" cellpadding="3" cellspacing="0" bgcolor="#dedebb" width="100%"><tr><td align=left>
<font class="content" color="#363636"><b>Login</b></font>
</td></tr></table></td></tr></table>
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="150">
<tr valign="top"><td bgcolor="#ffffff">
There is no login necessary for a Wiki page!
However you can integrate Nuke's authentication and push the user names to
ewiki (so no IP must be saved in the database).
</td></tr></table>
<br>


<table border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" width="150"><tr><td>
<table border="0" cellpadding="3" cellspacing="0" bgcolor="#dedebb" width="100%"><tr><td align=left>
<font class="content" color="#363636"><b>Information</b></font>
</td></tr></table></td></tr></table>
<table border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="150">
<tr valign="top"><td bgcolor="#ffffff">
<br>
<a href="http://phpnuke.org">PhpNuke</a>
</td></tr></table>
<br>


</td><td bgcolor="#ffffff">&nbsp;&nbsp;</td>
</tr>
</table>


<br>
<br>
<br>
<br>

    </body>

    </html>