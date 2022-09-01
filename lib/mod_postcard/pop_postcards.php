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

// get a list of message files
$inc = -2;

$d = opendir("/var/opt/httpd/csdocs/justforfun/Message/08") or die($php_errormsg);
	
// loop through the list and test date
	while (false !== ($f = readdir($d))) {	
	    $file = "/var/opt/httpd/csdocs/justforfun/Message/08/$f";
	    if (('.' == $file) || ('..' == $file)) { 
	    continue;
	    }
	    if (date ("Y", filemtime($file))
		== "2005") {
	
		// get timestamp and convert
			$datesent = date ("Y-m-d", filemtime($file));
					
	   	// open the file
	   		$fileopen = fopen($file, "r") or die($php_errormsg);
   			print "file opened \n";
   
   		// read it in
   			$contents = fread($fileopen, filesize($file));
   			//print $contents;
   
   		// explode data and convert as needed
   			$parts1 = explode("\n", $contents);
   			//print "$parts1[0] \n, $parts1[1] \n" ;
   			$parts2 = explode(" ", $parts1[0]);
   			//print "$parts2[0]\n$parts2[1]\n$parts2[2]\n$parts2[3]\n
   			//$parts2[4]\n$parts2[5]\n$parts1[1]\n" ;
   			
   			$ToEmail = urldecode($parts2[3]);
   			$FromEmail = urldecode($parts2[5]);
   			$ToName = urldecode($parts2[2]);
   			$FromName = urldecode($parts2[4]);
   			//print "to: $ToEmail and From: $FromEmail \n";
   			
   			$message = $parts1[1];
  			$art_num = $parts2[0];
  			$quote_letter = $parts2[1];
   
   		// convert the Quote letter to Quote ID
  			$quote_id = $quote[$art_num][$quote_letter];
  			
   
   		// insert into pc_postcard db
   	$db = HCGNewConnection('hcg_public');
  	$db->SetFetchMode(ADODB_FETCH_ASSOC);
 	$query = "INSERT INTO pc_postcard ".
            "(PostcardKey, Message, ToName, ToEmail, FromName, FromEmail, QuoteID, ArtworkID, DateSent, SiteID ) ".
            "VALUES ".
            "(\"".$f."\", ".
            "\"".$parts1[1]."\", ".
            "\"".$ToName."\", ".
            "\"".$ToEmail."\", ".
            "\"".$FromName."\", ".
            "\"".$FromEmail."\", ".
            "\"".$quote_id."\", ".
            "\"".$parts2[0]."\", ".
            "\"".$datesent."\", ".
            "\"cs\")";
   $db->Execute($query);
   echo "$query";
   			
   			
   			//close file
   			fclose($fileopen);
   			print "file closed \n";
   
   			// increment a count
			$inc++;

// end loop
		}
	
	}
closedir($d);


// display the count

if ($inc == -2) {
print "No files processed \n";
}
else {
print "$inc files processed \n";
}
?>