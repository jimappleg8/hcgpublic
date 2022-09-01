#!/usr/bin/php

<?php

$base_dir = str_replace('/lib/mod_finance/get_stock_data.php', '', __FILE__);

ini_set('include_path', $base_dir.'/lib/classes/pear:'. $base_dir.'/lib/classes/stocks:'. ini_get('include_path'));


//require_once 'config.inc.php';

require_once 'HTTP/Request.php';
   
$request = "https://www.nasdaq.com/symbol/hain";


$r =& new HTTP_Request($request);
$response = $r->sendRequest();

if (!PEAR::isError($response))
{
   $page = $r->getResponseBody();
}
else
{
   return "Error Message: ".$response->getMessage();
}

// temporary to force alternate display
//$page = '';

// override local file
//$page = file_get_contents('/Users/japplega/Desktop/websites/lib/mod_finance/nasdaq.html');

if ($page != "")
{   
   //scrape the resulting page stock information
   
   // ------------------------------------------------------
   // price_last
   //-------------------------------------------------------
   
   $pos0 = strpos($page, 'id="qwidget_lastsale"');
   $pos1 = strpos($page, 'dollar">$', $pos0) + 9;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "</div>");
   $stockInfo['price_last'] = substr($page, 0, $pos2);

   // ------------------------------------------------------
   // net change amount / percentage / direction
   //-------------------------------------------------------
   
   $pos0 = strpos($page, 'id="qwidget_netchange"');
   $pos1 = strpos($page, '">', $pos0) + 2;
   $page = trim(substr($page, $pos1));
   $pos2 = strpos($page, "</div>");
   $stockInfo['dchangeu'] = substr($page, 0, $pos2);
   // attempt to fix issue when price is unchanged
   if (strlen($stockInfo['dchangeu']) == 'unch')
   {
      $stockInfo['dchangeu'] = "0.00";
   }

   $pos3 = strpos($page, 'id="qwidget_percent"');
   $stockInfo['direction'] = substr($page, $pos2, $pos3-$pos2);
   if (preg_match("/arrow-green/", $stockInfo['direction']))
   {
      $stockInfo['direction'] = "up";
   }
   elseif (preg_match("/arrow-red/", $stockInfo['direction']))
   {
      $stockInfo['direction'] = "down";
   }
   else
   {
      $stockInfo['direction'] = "unchanged";
   }

   if ($stockInfo['direction'] != "unchanged")
   {
      $pos3 = strpos($page, '">', $pos3) + 2;
      $page = trim(substr($page, $pos3));
      $pos4 = strpos($page, "</div>");
      $stockInfo['pchange'] = substr($page, 0, $pos4);
      $stockInfo['pchange'] = str_replace("%", "", $stockInfo['pchange']);
   } else {
      $stockInfo['pchange'] = "0";
   }
   
   // ------------------------------------------------------
   // date and time
   //-------------------------------------------------------
   
   $pos1 = strpos($page, 'id="qwidget_markettime">', $pos0) + 24;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "</span>");
   $stockInfo['datetime'] = trim(substr($page, 0, $pos2));

   $datetime = explode(" ", $stockInfo['datetime']);
   $stockInfo['date'] = $datetime[0].' '.$datetime[1].' '.$datetime[2];
   $stockInfo['time'] = ($datetime[3] == '') ? '16:01' : $datetime[3];
   
   list($hours, $minutes) = explode(':', $stockInfo['time']);
   $time = ($hours * 60) + $minutes;
   $stockInfo['status'] = 'Closed';
   // numbers are 9:30 AM and 4:00 PM in minutes from midnight
   if ($time >= 570 && $time <= 960)
   {
      $stockInfo['status'] = 'Open';
   }

   // ------------------------------------------------------
   // best bid and ask
   //-------------------------------------------------------

   $pos0 = strpos($page, "Best Bid / Ask");
   $pos1 = strpos($page, '<div class="table-cell">', $pos0) + 24;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "&nbsp;/");
   $stockInfo['best_bid'] = str_replace("$&nbsp;", '', trim(substr($page, 0, $pos2)));

   $pos1 = $pos2 + 7;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "</div>");
   $stockInfo['best_ask'] = str_replace("$&nbsp;", '', trim(substr($page, 0, $pos2)));
   $stockInfo['best_ask'] = str_replace("&nbsp;", '', $stockInfo['best_ask']);

   // ------------------------------------------------------
   // today's high and low
   //-------------------------------------------------------

   $pos0 = strpos($page, "Today's High / Low");
   $pos1 = strpos($page, "$&nbsp;", $pos0) + 7;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "&nbsp;/");
   $stockInfo['price_max'] = substr($page, 0, $pos2);

   $pos1 = strpos($page, "$&nbsp;", $pos2) + 7;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "</div>");
   $stockInfo['price_min'] = trim(substr($page, 0, $pos2));

   // ------------------------------------------------------
   // volume
   //-------------------------------------------------------

   $pos0 = strpos($page, "Share Volume");
   $pos1 = strpos($page, 'class="table-cell">', $pos0) + 19;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "</div>");
   $stockInfo['volume'] = trim(substr($page, 0, $pos2));

   // ------------------------------------------------------
   // previous close
   //-------------------------------------------------------

   $pos0 = strpos($page, "Previous Close");
   $pos1 = strpos($page, "$&nbsp;", $pos0) + 7;
   $page = substr($page, $pos1);
   $pos2 = strpos($page, "</div>");
   $stockInfo['previous'] = trim(substr($page, 0, $pos2));

}

//print_r($stockInfo); exit;

$fh = fopen($base_dir.'/lib/mod_finance/stock_data.php','w') or die("Can't open stock_data.php: $php_errormsg");

fwrite($fh, "<?php\n");
fwrite($fh, "\$stock = array(0 => array(\n");
fwrite($fh, "\"symbol\" => \"HAIN\", \n");
fwrite($fh, "\"name\" => \"The Hain Celestial Group, Inc.\", \n");
fwrite($fh, "\"date\" => \"".$stockInfo['date']."\", \n");
fwrite($fh, "\"time\" => \"".$stockInfo['time']."\", \n");
fwrite($fh, "\"status\" => \"".$stockInfo['status']."\", \n");
fwrite($fh, "\"price_last\" => \"".$stockInfo['price_last']."\", \n");
fwrite($fh, "\"pchange\" => \"".$stockInfo['pchange']."\", \n");
fwrite($fh, "\"dchangeu\" => \"".$stockInfo['dchangeu']."\", \n");
fwrite($fh, "\"direction\" => \"".$stockInfo['direction']."\", \n");
fwrite($fh, "\"volume\" => \"".$stockInfo['volume']."\", \n");
fwrite($fh, "\"previous\" => \"".$stockInfo['previous']."\", \n");
fwrite($fh, "\"price_max\" => \"".$stockInfo['price_max']."\", \n");
fwrite($fh, "\"price_min\" => \"".$stockInfo['price_min']."\", \n");
fwrite($fh, "\"best_bid\" => \"".$stockInfo['best_bid']."\", \n");
fwrite($fh, "\"best_ask\" => \"".str_replace('"', '\"', $stockInfo['best_ask'])."\", \n");
fwrite($fh, "));\n");
fwrite($fh, "?>");

fclose($fh);
   
?>
