<html>
<head>
<TITLE>mapquest example</TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">

a.ex:active, a.ex:link, a.ex:visited  { color: #ffffff; text-decoration: underline }
a.ex:hover { color: #ff00ff; text-decoration: none }
.text { font-family: Verdana,Geneva,Helvetica; font-size: 12px; color: #ffffff; }

</style>
</head>
<body bgcolor="#000000">
<span class=text>
<?

require("class.mapquest.php");

?>
1. this is where I live:<br>
<?

$a = new mapquest;
$a->a_css="ex";
$a->addQvar("address","2634 W. Rice St.");
$a->printA();

?>
<br><br>
1.b another way to phrase it:<br>
<?

$a = new mapquest;
$a->a_css="ex";
$a->addQvar("address","2634 W. Rice St.");
$a->a_text=$a->mq_qstring['address='];
$a->printA();

?>
<br><br>
1.c this is the aerial view of where I live:<br>
(that's the big Metra train yard to the left)<br>
<?

$a = new mapquest;
$a->a_css="ex";
$a->addQvar("dtype","a"); //a = aerial, s = streetmap
$a->addQvar("address","2634 W. Rice St.");
$a->printA();

?>
<br>
<br>
<br>


2. this is where I used to live:<br>
(sometimes I miss the ocean)<br>
<?

$a = new mapquest;
$a->a_css="ex";
$a->addQvar("address","136 High Dr.");
$a->addQvar("city","Laguna Beach");
$a->addQvar("state","CA");
$a->printA();

?>
<br><br>
2.b this is where I used to live (zoom = 0):<br>
<?

$a = new mapquest;
$a->a_css="ex";
$a->zoom(0);
$a->addQvar("address","136 High Dr.");
$a->addQvar("city","Laguna Beach");
$a->addQvar("state","CA");
$a->printA();

?>
<br><br>
3. this is how you store a value:<br>
<?

$a = new mapquest;
$a->a_css="ex";
$a->addQvar("address","136 High Dr.");
$a->addQvar("city","Laguna Beach");
$a->addQvar("state","CA");
$a->makeA();

$stored = $a->mq;
print $stored;

?>
<br><br>
4. or... if you just want the url (it's long):<br>
<?

$a = new mapquest;
$a->a_css="ex";
$a->addQvar("address","136 High Dr.");
$a->addQvar("city","Laguna Beach");
$a->addQvar("state","CA");
$a->printHREF();


/*

if it's not working you can try getting an error:

if(!$a->printA()) print "error: ".$a->error;

...it's pretty simple though (mapquest will take anything).

*/

?>
</span>
</body>
</html>