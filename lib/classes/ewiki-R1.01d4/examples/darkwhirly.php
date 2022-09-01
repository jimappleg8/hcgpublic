<!-- this uses CSS extensively, of little use with w3m or lynx -->
<html>
<head>
 <title>another dark layout sample</title>
 <style type="text/css">a{text-decoration:none;}</style>
</head>
<body bgcolor="#333333" text="#f7f7f7" link="#ff3333">
<?php

 srand(25031682);
 for ($n=1; $n<100; $n++) {
   list($x,$y,$width,$height,$c1,$c2,$br) = array(
      rand(-3,600), rand(-3,1000),
      rand(10,400-$n*3), rand(5,300-$n*2),
      rand(10,55), rand(10,55), rand(0,4),
   );
   echo "<span style=\"position:absolute; " .
        "background-color: rgb($c1,$c1,$c1); " .
#       "border: 3px solid rgb($c2,$c2,$c2); " .
	"color: rgb($c2,$c2,$c2); ".
        "left: {$x}px; top: {$y}px; " .
        "width: {$width}px; height: {$height}px; " .
        "\">ewiki</span>\n";
 }

?>
<div style="position:absolute; left:70px; top:20px; width:500px; z-index:500;">
<?php

 define("EWIKI_SCRIPT", "darkwhirly.php?page=");
 define("EWIKI_SCRIPT_BINARY", "");
 include("../config.php");
 include("plugins/filter/fun_wella.php");

 echo ewiki_page();

?>
</div>
</body>
</html>