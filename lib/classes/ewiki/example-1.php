<?php

 #-- open database connection,
 #   and load ewiki.php 'library'
 include("config.php");

 #-- this is the actual call to generate the output for the current wiki
 #   page, but we buffer it now and print its output later
 $ewiki_page = ewiki_page();

?><?php

 #-- color scheme
 list($color1, $color2) = //array("992211", "ffcc88");
                          //array("ffaa55", "ffcc88");
                          //array("994411", "ffcc00");
                          array("994411", "ffbb44");
       

?>
<HTML>
<HEAD>
 <TITLE>ErfurtWiki: <?php  echo($ewiki_title);   ?></TITLE>
 <STYLE TYPE="text/css"><!--
   body {
      margin:0px; padding:0px;
   }
   body,td {
      font-family:"Verdana",sans-serif;
      font-size:13px;
   }
   .rbr {
      font-family:"skaterdudes","steelfish","coolvetica","Arial","Helvetica","Lucida";
      background-color:#<?=$color2?>;
      color:#221003;
      padding:3px;
      border-radius:12px 0px 0px 12px;
      -moz-border-radius:12px 0px 0px 12px;
      border:2px solid #000000; border-right:0px;
      margin-top:8px;
      float:right; clear:right;
   }
   .rbr a {
      text-decoration:none;
   }
   .MainMenu {
      border:0px;
      background-color:transparent;
   }
   .MainMenu li, .MainMenu ul {
      min-width:110px;
      list-style-type:none;
      padding-left:0px;
      margin-left:5px;
   }
   .MainMenu li {
      background-color:#ee9933;
      border:1px solid #dd8822;
   }
   .MainMenu ul ul {
      display:none;
   }
   .MainMenu ul:hover ul {
      display:block;
   }
   a:hover {
      background-color:#992233;
      color:#ffffff;
   }
 //--></STYLE>
 <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">
</HEAD>
<BODY BGCOLOR="#<?=$color1?>" TEXT="#000000" LINK="#111199" VLINK="#000033" ALINK="#ff5511" LEFTMARGIN="0"  MARGINWIDTH="0" MARGINHEIGHT="0">

<br><table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td width="200">&nbsp;</td><td bgcolor="#<?=$color2?>" align="right" class="rbr" style="float:none;">
<H1 style="padding:0px; margin:0px; font:50px/50px bold Arial,Helvetica,sans-serif; font-stretch:extra-expanded;">
<IMG SRC="squirrel.jpeg" ALT=":@" WIDTH="48" HEIGHT="48" ALIGN="MIDDLE" style="margin-top:-10px;">&nbsp;ErfurtWiki&nbsp;&nbsp;&nbsp;</H1>
</td></tr></table>

<!--HR STYLE="display:none; color:#dd5522; height:1px; margin:0px; padding:0px;" WIDTH="100%" NOSHADE COLOR="#dd5522"-->

<br><br>

<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr>
<td width="70" bgcolor="#<?=$color2?>" style="border:solid #000000; border-width:2px 0px;">&nbsp;</td>
<td width="450" bgcolor="#<?=$color2?>" style="border:2px solid #000000;
border-left:0px; -moz-border-radius:0px 12px 12px 0px; padding:5px;
min-height:500px;" valign="top">
<br>
<?php

 #-- output previously generated page
 echo($ewiki_page);

?>
<br>
</td>
<td width="20">&nbsp;</td>
<td valign="top">

   <div class="rbr">
   <b>ErfurtWiki</b><br>
   » <A HREF="?id=README">README</A><BR>
   » <A HREF="./CHANGES">ChangeLog</A><BR>
   » <A HREF="http://erfurtwiki.sourceforge.net/">project site</A><BR>
   » <A HREF="http://freshmeat.net/projects/ewiki">fm project page</A><BR>
   » <A HREF="http://erfurtwiki.sourceforge.net/downloads/">downloads/</A><BR>
   </div>

   <?php
     $mm = ewiki_database("GET", array("id"=>"MainMenu"));
     if ($mm = $mm["content"]) {
          $mm = preg_replace("/^([^*]+[^\n]+)?\n/m", "", $mm);
          echo '<div class="rbr MainMenu">' .  "\n" .
               ewiki_format($mm) . "\n</div>\n";
     }
   ?>

   <div class="rbr">
   <b>examples</b><br>
   » <A HREF="./">this version</A><BR>
   » <A HREF="./example-2.php">plain white</A><BR>
   » <A HREF="./example-3.php">dark sample</A><BR>
   » <A HREF="./example-4.php">nuke like</A><BR>
   » <A HREF="./example-5.php">wiki homepage</A><BR>
   </div>

   <div class="rbr">
   <b>database <A HREF="./tools/index.html">tools</A></b><br>
   » <A HREF="./tools/t_flags.php">set page flags</A><BR>
   » <A HREF="./tools/t_backup.php">backup util</A><BR>
   » <A HREF="./tools/t_restore.php">restore util</A><BR>
   » <A HREF="./tools/t_remove.php">page deletion</A><BR>
   » <A HREF="./tools/t_holes.php">make holes</A><BR>
   » <A HREF="./tools/t_convertdb.php">convert db</A><BR>
   » <A HREF="./tools/t_checklinks.php">check links</A><BR>
   </div>


   <div class="rbr">
   <b>internal pages</b><br>
   <?php
     foreach ($ewiki_plugins["page"] as $id=>$pf) {
        echo '» <A HREF="?id=' . $id . '">' . $id . '</A><BR>' . "\n";
     }
   ?>
   </div>

   <?php
     if (function_exists("calendar_exists") && calendar_exists()) {
        echo '<div class="rbr">';
        echo  calendar();
        echo '</div>';
     }
   ?>

<?php
    if (file_exists($sf = "sftail.php")) {
     $sfsummary = "full";
     include($sf);
  }
?>

</td></tr></table>

<br><br>

</BODY>
</HTML>