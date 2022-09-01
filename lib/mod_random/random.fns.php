<?php

// =========================================================================
// menu.fns.php
// written by Jim Applegate
//
// =========================================================================


//-------------------------------------------------------------------------
// tag: random_quote
//   taken from The PHP Cookbook, p.469
//
//-------------------------------------------------------------------------

function random_quote($site_id = "default")
{
   global $_HCG_GLOBAL;
   
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
  
   $quote_file = "/var/opt/httpd/" . $site_id . "docs/inc/quotes.txt";
   $fh = fopen ($quote_file, "r") or die ($php_errormsg);

   $line_number = 0;
   while (!feof($fh)) {
      if ($s = fgets($fh,1048576)) {
         $line_number++;
         if (pc_randomint($line_number) < 1) {
            $line = $s;
         }
      }   
   }
   fclose($fh) or die($php_errormsg);
   return $line;
   
}


//-------------------------------------------------------------------------
// random_baby
//   taken from The PHP Cookbook, p.469
//
//-------------------------------------------------------------------------

function random_baby($site_id = "default")
{
   global $_HCG_GLOBAL;
   
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
  
   $line_number = 0;
   
   $baby_file = "/var/opt/httpd/".$site_id."docs/inc/".$site_id."_babies.txt";
   $fh = fopen ($baby_file, "r") or die ($php_errormsg);
   while (!feof($fh)) {
      if ($s = fgets($fh,1048576)) {
         $line_number++;
         if (pc_randomint($line_number) < 1) {
            $line = $s;
         }
      }   
   }
   fclose($fh) or die($php_errormsg);
   $line_array = explode("|", $line);
   return $line_array;
   
}


//-------------------------------------------------------------------------
// random_image
//   taken from The PHP Cookbook, p.469
//
//-------------------------------------------------------------------------

function random_image($site_id = "default")
{
   global $_HCG_GLOBAL;
   
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
  
   $line_number = 0;
   
   $image_file = "/var/opt/httpd/".$site_id."docs/inc/".$site_id."_images.txt";
   $fh = fopen ($image_file, "r") or die ($php_errormsg);
   while (!feof($fh)) {
      if ($s = fgets($fh,1048576)) {
         $line_number++;
         if (pc_randomint($line_number) < 1) {
            $line = $s;
         }
      }   
   }
   fclose($fh) or die($php_errormsg);
   $line_array = explode("|", $line);
   return $line_array;
   
}



//-------------------------------------------------------------------------
// random_pic_array
//   selects a random file from a supplied array of files.
//
//-------------------------------------------------------------------------

function random_pic_array($file_array)
{     
   $max = count($file_array) - 1;
   
   echo $file_array[mt_rand(0, $max)];
   
}



//-------------------------------------------------------------------------
// pc_randomint
//   taken from The PHP Cookbook, p.469
//
//-------------------------------------------------------------------------

function pc_randomint($max = 1)
{
   $m = 1000000;
   return ((mt_rand(1,$m * $max)-1)/$m);

}

?>
