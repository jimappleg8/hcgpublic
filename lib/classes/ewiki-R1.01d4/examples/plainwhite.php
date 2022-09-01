<?php

 define("EWIKI_SCRIPT", "plainwhite.php?page=");
 include("../config.php");

?>
<html>
<head>
 <title>plain white ErfurtWiki</title>
</head>
<body>
<img src="squirrel.jpeg" width="32" height="32" alt="ErfurtWiki" align="left" valign="absmiddle" border="2">
<?php

 echo ewiki_page();

?>
</body>
</html>