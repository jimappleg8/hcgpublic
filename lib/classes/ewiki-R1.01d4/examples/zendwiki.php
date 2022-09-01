<?php
#
# this example layout partially mimics php.net
#

#-- conf, lib
define("EWIKI_PAGE_INDEX", "WikiNews");
include("../config.php");
if (empty($ewiki_plugins["page"]["WikiNews"])) {
  @include("plugins/contrib/page_wikinews.php");
}
$CONTENT = ewiki_page();


?>
<html>
<head>
  <title><?php echo $ewiki_title; ?></title>
<style type="text/css"><!--

html {
}
body,html { 
  padding:0px;
  margin:0px;
}

body,td,ul {
  font-size:85%;
}
small {
  font-size:75%;
}

.quicksearch {
  border: solid #333366;
  border-width:1px 0px;
  color:#ffffff;
}

form {
  display:inline;
}

td.main, td.left, td.right {
  padding:4px;
}
td.main {
  border-left:1px dashed #999999;
  border-right:1px dashed #999999;
  padding:10px;
  min-height:600px;
}

.wiki.view .action-links {
  border:1px dashed #999999;
  padding:10px;
  background-color:#f1f1f1;
}

.action-links hr {
  display:none;
}

.rarr {
  border:0px;
  background-color:#ffffff;
  color:#666699;
  font-weight:900;
}

.no-underline a {
  text-decoration:none;
}

h2 a {
  text-decoration:none;
  border-bottom:2px solid #eeeeff;
}

/* grabbed from php.net */
body, ul, td, th, p, h1, h2, h3, h4, small, input, textarea, select {
  font-family: "Verdana", "Arial", "Helvetica", sans-serif;
}
code, pre, tt {
  font-family: "Courier", "Courier New", monospace;
}

h2, h2 a, h3, h4 {
  font-weight: bold;
  color: #000066;
}
h2 { font-size: 125%; }
h3 { font-size: 110%; }
h4 { font-size: 100%; }

hr {
  height:1px; border:0px;
  color:#ffffff; background-color:#ffffff;
}

//--></style>
</head>
<body topmargin="0" leftmargin="0" marginright="0" rightmargin="0" bgcolor="#ffffff" text="#000000"
 link="#000099" alink="ffcc22" vlink="#000055">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr bgcolor="#9999cc">
  <td width="130" align="center" height="67"><a href="http://php.net/"><img src="?=<?php echo php_logo_guid(); ?>" alt="PHP" width="120" height="67" hspace="5" border="0"></a></td>
  <td valign="bottom" align="right" style="padding:2px;"><small class="no-underline">
    <a href="/downloads/">downloads</a> |
    <a href="zendwiki.php?id=README">documentation</a> |
    <a href="zendwiki.php?id=OccasionallyAskedQuestions">faq</a> |
    <a href="zendwiki.php?id=DevelopersPage">getting help</a> |
    <a href="zendwiki.php?id=MailingList">mailing list</a> |
    <a href="zendwiki.php?id=BugReports">reporting bugs</a> |
    <a href="zendwiki.php?id=Links">links</a>
    &nbsp;
  </small></td>
</tr>
<tr bgcolor="#666699">
 <td colspan="2" align="right" valign="middle" class="quicksearch"><small>
   <form action="zendwiki.php" method="GET">
    <input type="hidden" name="id" value="PowerSearch">
    <u>s</u>earch for <input type="text" name="q" size="30" accesskey="s">
    in the <select name="where">
    <option value="text" selected>whole site</option>
    <option value="titles">page names</option>
    <option value="undef">online documentation [en]</option>
    </select>
    <input type="submit" value="&rArr;" class="rarr">
    &nbsp; </form>
 </small></td>
</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<colgroups width="100%"><col width="200"><col width="*"><col width="230"></colgroups>
<tr>
<td bgcolor="#f1f1f1" valign="top" class="left" width="150">

  <?php
  echo ewiki_format(<<<___

!! What is a Wiki?

A WikiWikiWeb is a minimalistic [CMS|ContentManagementSystem], that allows people to
contribute without depending on passwords or user accounts (per default). 
Users can easily edit pages and comment on each others thoughts and opinions
without needing to know HTML. Links will appear automatically for phrases
and terms written as [WikiWord]s, so a Wiki is a very fast growing hypertext
system.

Ever wondered how popular the Wiki idea is? see the [Google:Wiki "Google results"].

ErfurtWiki is a maintained by losely knit of [DevelopersPage "developers"].

!! Thanks To
* [http://sourceforge.net/ "SourceForge.net"]
* [http://www.php.net/ "The PHP Group"]
* [http://www.burgiss.com/ "Burgiss.com"]
* [http://www.freshmeat.net/ "FreshMeat.net"]
* [http://nanoweb.si.kz/ "Nanoweb Software Foundation"]
* [http://www.google.com/ "Google"]

!! Related sites
* [http://www.php.net/ "The PHP Home"]
* [WardsWiki:WelcomeVisitors]
* [WikiPedia:WikiWikiWeb]
* [PhpWiki: "PhpWiki: the evil concurrent"]

!! Community
* [http://www.osdn.org/ "OSDN"]

!! Contact
Please submit website bugs in the BugReports

!! Contribute!
Please file any wishes on UserSuggestions or the WishList.
___
  );
  ?>

</td>
<td valign="top" class="main" width="*">
<?php

  echo $CONTENT;

?>
</td>
<td bgcolor="#f1f1f1" valign="top" class="right" width="150">

  <center>
  <h3>This mirror sponsored by:</h3>
  <a href="http://sourceforge.net/"><img src="http://sourceforge.net/sflogo.php?group_id=75510&amp;type=5" width="210" height="62" border="0" alt="sourceforge.net"></a><br>
  <!--a href="http://www.va-software.com/">VA Software</a-->
  </center>

  <br>
  <hr noshade>

  <?php
    if ($data = ewiki_database("GET", array("id"=>"ZendWikiSideBar"))) {
       echo ewiki_format($data["content"]);
       echo '<br>';
    }
    echo '<br><small><a href="'.ewiki_script("edit", "ZendWikiSideBar").'">Add some text here...</a></small>';
  ?>

</td></tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td colspan="2" align="right" bgcolor="#9999cc" class="quicksearch" style="color:#000000; padding:2px;">
<small class="no-underline">
    <a href="zendwiki.php?id=PublicDomain">license</a> |
    <a href="../CREDITS">credits</a> |
    <a href="zendwiki.php?id=PageIndex">sitemap</a> |
    <a href="../example-1.php">default theme</a>
    &nbsp;
</small></td></tr>
<tr bgcolor="#cccccc"><td width="50%" style="padding:3px;"><small>
 <a href="http://sco.com/">Copyright &copy; 1973 The SCO Group - give us all your money!!</a>
 <br> No warranty.
</small></td>
<td width="50%" align="right" style="padding:3px;"><small>
This site is hosted on <a href="http://sourceforge.net/">http://sourceforge.net/</a>,<br>
home of thousands of open source projects.
</small></td></tr></table></body></html>