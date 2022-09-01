#!/usr/local/bin/php

<?php

require_once("config.inc.php");
require_once("dbi_adodb.inc.php");

// establish letter to QuoteID conversion table
$quote[1]['A'] = 1;
$quote[1]['B'] = 2;
$quote[1]['C'] = 3;
$quote[2]['A'] = 4;
$quote[2]['B'] = 5;
$quote[2]['C'] = 6;
$quote[3]['A'] = 7;
$quote[3]['B'] = 8;
$quote[3]['C'] = 9;
$quote[4]['A'] = 10;
$quote[4]['B'] = 11;
$quote[4]['C'] = 12;
$quote[5]['A'] = 13;
$quote[5]['B'] = 14;
$quote[5]['C'] = 15;
$quote[6]['A'] = 4;
$quote[6]['B'] = 5;
$quote[6]['C'] = 6;
$quote[7]['A'] = 16;
$quote[7]['B'] = 17;
$quote[7]['C'] = 13;
$quote[8]['A'] = 18;
$quote[8]['B'] = 19;
$quote[8]['C'] = 20;
$quote[9]['A'] = 21;
$quote[9]['B'] = 22;
$quote[9]['C'] = 23;
$quote[10]['A'] = 24;
$quote[10]['B'] = 25;
$quote[10]['C'] = 26;
$quote[11]['A'] = 27;
$quote[11]['B'] = 28;
$quote[11]['C'] = 29;
$quote[12]['A'] = 30;
$quote[12]['B'] = 31;
$quote[12]['C'] = 32;
$quote[13]['A'] = 28;
$quote[13]['B'] = 33;
$quote[13]['C'] = 34;
$quote[14]['A'] = 35;
$quote[14]['B'] = 36;
$quote[14]['C'] = 37;

$inc = 0;

pc_process_dir2 ("/var/opt/httpd/csdocs/justforfun/Message/", 'process_postcard', 3,0);

print "\n\n$inc total files processed.\n";



function pc_process_dir2($dir_name,$func_name,$max_depth,$depth = 0) 
{
   global $f;
   
   if ($depth >= $max_depth) {
      error_log("You reached the maximum depth of $max_depth in $dir_name.");
      return false;
   }
   $subdirectories = array();
   $files = array();
   $dirinc = 0;
   if (is_dir($dir_name) && is_readable($dir_name)) {
      $d = dir($dir_name);
      while (false !== ($f = $d->read())) {
         if (('.' == $f) || ('..' == $f)) {
            continue;
         }
         if (is_dir("$dir_name/$f")) {
            array_push($subdirectories, $dir_name.$f);
         } else {
            $func_name("$dir_name/$f");
            $dirinc++;
         }
      }
      // display the count of files processed by directory
      print "$dirinc files processed in $dir_name\n";
      $d->close();
      foreach ($subdirectories as $subdirectory) {
         pc_process_dir2($subdirectory,$func_name,$max_depth,$depth+1);
      }				
   }
}
			
function process_postcard($file) 
{
   global $inc;
   global $quote;
   global $f;
   
   if (date ("Y", filemtime($file)) == "2005") {

      // get timestamp and convert
      $datesent = date ("Y-m-d", filemtime($file));
					
      // open the file
      $fileopen = fopen($file, "r") or die($php_errormsg);
   
      // read it in
      $contents = fread($fileopen, filesize($file));
   
      // explode data and convert as needed
      $parts1 = explode("\n", $contents);
      $parts2 = explode(" ", $parts1[0]);
   			
      $ToEmail = urldecode($parts2[3]);
      $FromEmail = urldecode($parts2[5]);
      $ToName = urldecode($parts2[2]);
      $FromName = urldecode($parts2[4]);
   			
      $message = $parts1[1];
      $art_num = $parts2[0];
      $quote_letter = $parts2[1];
   
      // convert the Quote letter to Quote ID
      $quote_id = $quote[$art_num][$quote_letter];
  			
   
      // insert into pc_postcard db
      $db = HCGNewConnection('hcg_public_master');
      $db->SetFetchMode(ADODB_FETCH_ASSOC);
      $query = "INSERT INTO pc_postcard ".
               "(PostcardKey, Message, ToName, ToEmail, FromName, FromEmail, QuoteID, ArtworkID, DateSent, SiteID ) ".
               "VALUES ".
               "(\"".$f."\", ".
               "\"".addslashes($parts1[1])."\", ".
               "\"".addslashes($ToName)."\", ".
               "\"".addslashes($ToEmail)."\", ".
               "\"".addslashes($FromName)."\", ".
               "\"".addslashes($FromEmail)."\", ".
               "\"".$quote_id."\", ".
               "\"".$parts2[0]."\", ".
               "\"".$datesent."\", ".
               "\"cs\")";
               
      if ($db->Execute($query) === false) {
         echo 'error inserting: '.$db->ErrorMsg()."\n";
      } else {
         // increment a count
         $inc++;      
      }
   			  			
      //close file
      fclose($fileopen);
   }
   return;
}

?>