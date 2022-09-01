<?php

// ---------------------------------------------------------------------------
// ir.inc.php
//   written by Jim Applegate
//
// ---------------------------------------------------------------------------

global $_HCG_GLOBAL;

require_once($_HCG_GLOBAL['lib_dir']."/mod_xml/xml.inc.php");

// ------------------------------------------------------------------------
// getXmlFeed()
//  checks first to see if a requested XML feed is cached. If it is cached
//  and it has not expired, it returns the cached version, otherwise it
//  requests the feed from the server and caches it.
//
// ------------------------------------------------------------------------
function getXmlFeed($url, $lifeTime)
{
   global $_HCG_GLOBAL;
   
   $path = $_HCG_GLOBAL['lib_dir']."/mod_ir/cache/";
   
   require_once 'Cache/Lite.php';
   $options = array(
       'cacheDir' => $path,
       'lifeTime' => $lifeTime
   );
   // Create a Cache_Lite object
   $Cache_Lite = new Cache_Lite($options);

   if ($data = $Cache_Lite->get($url)) {
       return $data;
   } else {
      // using PEAR HTTP_Request
      require_once 'HTTP/Request.php';
   
      $r =& new HTTP_Request($url);
      if ($_HCG_GLOBAL['proxy'] != "") {
         $r->setProxy($_HCG_GLOBAL['proxy'], $_HCG_GLOBAL['proxy_port']);
      }
      $response = $r->sendRequest();

      if (!PEAR::isError($response)) {
         $data = $r->getResponseBody();
         // check for "The system cannot locate the object specified."
         if (strpos($data, "<IRXML")) {
            $Cache_Lite->save($data);
            return $data;
         } else {
            return "<br>Error Message: No XML file was returned.";
         }
      } else {
         return "<br>Error Message: ".$response->getMessage();
      }
   }
}


// ------------------------------------------------------------------------
// downloadXmlFeeds()
//  Downloads all the XML feeds that it can in an attempt to make sure that
//  the files needed by GetXmlFeed() are rarely expired.
//  For each download, it checks to see if the cached file has expired,
//  if it has, it requests the feed from the server and caches it.
//
// ------------------------------------------------------------------------
function downloadXmlFeeds()
{
   global $_HCG_GLOBAL;
   
   require_once 'HTTP/Request.php';
   require_once 'Cache/Lite.php';
   
   $url_base = "http://xml.corporate-ir.net/irxmlclient.asp?compid=87078";
   
   // lifetimes are set 5:20 shorter than in the main function
   // to make sure they are always up-to-date with a 5 minute cron job.
   $urls = array(
      $url_base."&reqtype=alerts" => 3280,
      $url_base."&reqtype=annualbalancesheet" => 3280,
      $url_base."&reqtype=company" => 3280,
      $url_base."&reqtype=events2" => 3280,
      $url_base."&reqtype=fundamentals" => 3280,
      $url_base."&reqtype=informationrequest" => 3280,
      $url_base."&reqtype=informationrequestconfig" => 3280,
      $url_base."&reqtype=items" => 3280,
      $url_base."&reqtype=newsreleases" => 3280,
      $url_base."&reqtype=people2" => 3280,
      $url_base."&reqtype=quotes" => 300,
      $url_base."&reqtype=secfilings" => 3280,
   );
   
   $path = $_HCG_GLOBAL['lib_dir']."/mod_ir/cache/";
//   $path = "/var/opt/httpd/lib/mod_ir/cache/";
  
   foreach ($urls as $url => $lifeTime) {
      $options = array(
          'cacheDir' => $path,
          'lifeTime' => $lifeTime
      );
      // Create a Cache_Lite object
      $Cache_Lite = new Cache_Lite($options);

      if ($data = $Cache_Lite->get($url)) {
//         echo "OK: ".$url."\n";
      } else {   
         $r =& new HTTP_Request($url);
         if ($_HCG_GLOBAL['proxy'] != "") {
            $r->setProxy($_HCG_GLOBAL['proxy'], $_HCG_GLOBAL['proxy_port']);
         }
         $response = $r->sendRequest();

         if (!PEAR::isError($response)) {
            $data = $r->getResponseBody();
            // check for "The system cannot locate the object specified."
            if (strpos($data, "<IRXML")) {
               $Cache_Lite->save($data);
//               echo "Cached: ".$url."\n";
            } else {
//               echo "Error: ".$url."\n";
//               echo "(No XML file was returned)\n";
            }
         } else {
//            echo "Error: ".$url."\n";
//            echo "(".$response->getMessage().")\n";
         }
      }
   }
}

// ------------------------------------------------------------------------
// irAdjustTime()
//  This takes a timestamp and a timezone and adjusts it to local time,
//  in this case, Mountain Standard Time (MST). This is not a complete
//  list of time zones, but only includes those used by CCBN in the XML 
//  feeds.
//
// ------------------------------------------------------------------------
function irAdjustTime($time, $zone)
{
   $localzone = +7;  // MST is -7 hours from GMT, so we have to add 7
   
   $timezones = array(
      'GMT'  => (0 + $localzone)*3600,   // Greenwich Mean
      'ET'   => (-5 + $localzone)*3600,  // Eastern Time
      'CT'   => (-6 + $localzone)*3600,  // Central Time
      'MT'   => (-7 + $localzone)*3600,  // Mountain Time
      'PT'   => (-8 + $localzone)*3600,  // Pacific Time
   );
   
   return $time + $timezones[$zone];
}

?>