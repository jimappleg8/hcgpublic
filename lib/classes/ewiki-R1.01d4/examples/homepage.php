<?php

   #-- This is an example standalone lite-CMS Homepage based on ewiki.php

   # - it requires PHP4.1+
   # - you should install it as index.php into your dedicated webspace
   # - copy the ewiki.php there, too
   # - DON'T upload the tools/ directory, as this requires a lot more
   #   setup to be used securely
   # - HTML Editors usually allow you to tweak the layout without
   #   garbaging the PHP code inside
   # - authentication is done using JavaScript+Cookies
   # - requires a MySQL database, else you must enable db_flat_files
   #   by creating a subdirectory "./pages" and allow write access to
   #   it with the command "chown 777 ./pages" (both from within your
   #   ftp program)!
   # - there will be no pages initially, you must first create some
   # - most config options are in the upper part of this file:

   $HOMEPAGE_TITLE	= "MyHomepage";
   $LOGIN_PASSWORD	= "password";
   $AUTHOR_NAME		= "your_nickname_here";
   $MYSQL_HOST		= "";	# "localhost"
   $MYSQL_USER		= "";
   $MYSQL_PASSWORD	= "";
   $MYSQL_DATABASE	= "";
   $FILES_DIRECTORY	= "";	# "/tmp" or "./pages" or "files"

   #-- open database
   if (file_exists("../ewiki.php")) { chdir(".."); }
   if ($FILES_DIRECTORY) {
     define("EWIKI_DBFILES_DIRECTORY", $FILES_DIRECTORY);
     define("EWIKI_DB_FAST_FILES", 1);
     include("plugins/db/flat_files.php");
   }
   elseif ($MYSQL_HOST && function_exists("mysql_ping")) {
     mysql_connect($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD);
     mysql_query("use $MYSQL_DATABASE");
   }
   else {
      die("example-5: Edit me, stupid! There must be either a MySQL server or a writable directory!");
   }

   #-- no errors shown from here
   error_reporting(0);

   #-- auth
   define("EWIKI_PROTECTED_MODE", 2);  # 2 == classic protected mode
   $ewiki_ring = 3;	// means browsing only

   #-- check for correct password
   if (($LOGIN_PASSWORD != "password") && (@$_COOKIE["password"])) {
      if ($LOGIN_PASSWORD == $_COOKIE["password"]) {
         $ewiki_author = $AUTHOR_NAME;
         $ewiki_ring = 2;    // this gives permission to edit pages
      }
      else {
         $page_content == "<h3>password wrong</h3>";
      }
   }


   #-- config
   define("EWIKI_SCRIPT", substr(__FILE__, strrpos(__FILE__, "/") + 1) . "?page=");
   define("EWIKI_SCRIPT_BINARY", substr(__FILE__, strrpos(__FILE__, "/") + 1) . "?binary=");
   define("EWIKI_PAGE_INDEX", $HOMEPAGE_TITLE);
   define("EWIKI_CONTROL_LINE", 0);

   #-- load plugins
   include("plugins/email_protect.php");

   #-- load ewiki lib
   include("ewiki.php");


   #-- get current page
   if (empty($page_content)) {
      $page_content = ewiki_page();
   }


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 <title><?php echo($ewiki_title); ?></title>
 <meta name="GENERATOR" content="ewiki">
 <meta name="ROBOTS" content="INDEX,FOLLOW">

 <style type="text/css">
 <!--
   body {
     background-color:#6666ee;
     color:#000011;
   }
   .menu {
     background-color:#111166;
     color:#ffffff;
     border: 2px solid #000055;
     padding: 8px;
     text-align:center;
     width:120px;
   }
   a,a:link { color: #ffff33; text-decoration: none; }
   a:active { color: #FF6666; }
   a:visited { color: #660000; }
   a:hover { font-weight:900; background-color:#ffff00; color:#000000; }
   .menu a { color:#ffffff; }
   .menu a:hover { color:#000000; }
 //-->
 </style>

 <script language="JavaScript" type="text/javascript">
 <!--

   function login()
   {
      var password = window.prompt("Please enter the administrator password:");
      window.document.cookie = "password=" + password;
      window.document.location.reload();
   }

   function logout()
   {
      window.document.cookie = "password=";
      window.document.location.reload();
   }

 //-->
 </script>

</HEAD>

<BODY>

<CENTER>
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="10" WIDTH="90%">
<TR>

<TD WIDTH="120" VALIGN="TOP">

<DIV CLASS="menu">

<h3>Welcome to my Homepage!</h3>


 <A HREF=".">Startpage</A> <BR>

 <A HREF="?page=EMailMe">EMailMe</A> <BR>

 <A HREF="?page=MyLinks">MyLinks</A> <BR>

 <BR>


<?php

 echo "<A HREF=\"?page=links/$ewiki_id\">Links to here</A><BR><BR>";

 if ($ewiki_author) {

    echo "<A HREF=\"javascript:logout()\">Logout</A><BR>";
    echo "<A HREF=\"?page=edit/$ewiki_id\">EditThisPage</A><BR>";
    echo "<A HREF=\"?page=info/$ewiki_id\">PageInfo</A><BR>";

 }
 else {

    echo "<SMALL><A HREF=\"javascript:login()\">EditorLogin</A><BR></SMALL>";
 }


?>

</DIV>
</TD>

<TD VALIGN="TOP" WIDTH="90%">
<DIV CLASS="content">
<?php


 echo($page_content);


?>
</DIV>
</TD>
</TR>
</TABLE>
</CENTER>

</BODY>
</HTML>