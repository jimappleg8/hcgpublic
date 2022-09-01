<?php

// =========================================================================
// gallery.fns.php
// written by Jim Applegate
//
// =========================================================================

require_once("template.class.php");
require_once("gallery.inc.php");


// ------------------------------------------------------------------------
// TAG: dir_gallery
//
// ------------------------------------------------------------------------

function dir_gallery($pwd="default", $site_id="default") 
{   
   global $_HCG_GLOBAL;
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }

   // set the working directory
   if ($pwd == "default") {
      $doc_pwd = dirname($_SERVER['PHP_SELF']);
      $pwd = $_HCG_GLOBAL['application_dir']."/".$site_id . "docs" . $doc_pwd;
   }
   
   // Get the array of image filenames.
   $imgs = getImageFilenames($pwd);
   
   foreach ($imgs as $img_part) {
      list($base, $ext) = explode('.', $img_part);
      $gallery[$base]['file'] = $img_part;
      $gallery[$base]['base'] = $base;
      $gallery[$base]['ext'] = $ext;
      $gallery[$base]['pwd'] = $pwd . '/';
      $gallery[$base]['doc_pwd'] = $doc_pwd . '/';
   }
   
   reset($gallery);
   while (list($imagekey,$imageset) = each($gallery)) {
   
      $or_file = $pwd.'/'.$gallery[$imagekey]['file'];
      $gallery[$imagekey]['size'] = number_format(filesize($or_file)/1024)." KB";

      $tn_file = 'TN_'.$gallery[$imagekey]['base'].'.jpg';
      if (file_exists($pwd.'/'.$tn_file)) {
         $gallery[$imagekey]['thumbnail'] = $tn_file;
      } else {
         $tn_settings['pwd']       = $pwd;
         $tn_settings['src_file']  = $gallery[$imagekey]['file'];
         $tn_settings['qual']      = 90;
         $tn_settings['prefix']    = "TN_";
         $tn_settings['tn_width']  = 80;
         $tn_settings['tn_height'] = 80;
         createThumbnail($tn_settings);
         $gallery[$imagekey]['thumbnail'] = $tn_file;
      }

      $pv_file = 'PV_'.$gallery[$imagekey]['base'].'.jpg';
      if (file_exists($pwd.'/'.$pv_file)) {
         $gallery[$imagekey]['preview'] = $pv_file;
      } else {
         $pv_settings['pwd']       = $pwd;
         $pv_settings['src_file']  = $gallery[$imagekey]['file'];
         $pv_settings['qual']      = 90;
         $pv_settings['prefix']    = "PV_";
         $pv_settings['pv_width']  = 450;
         $pv_settings['pv_height'] = 450;
         createPreview($pv_settings);
         $gallery[$imagekey]['preview'] = $pv_file;
      }

      $de_file = $pwd.'/DE_'.$gallery[$imagekey]['base'].'.txt';
      if (file_exists($de_file)) {
         $fh = fopen($de_file,'r') or die(php_errormsg);
         if ($heading = fgets($fh, 1024)) {
            $heading = rtrim($heading);
            $gallery[$imagekey]['heading'] = $heading;
         } else {
            $gallery[$imagekey]['heading'] = $gallery[$imagekey]['base'];
         }
         if ($description = fgets($fh, 1024)) {
            $description = rtrim($description);
            $gallery[$imagekey]['description'] = $description;
         } else {
            $gallery[$imagekey]['description'] = "No description available.";
         }
         fclose($fh) or die(php_errormsg);;
      } else {
         $gallery[$imagekey]['heading'] = $gallery[$imagekey]['base'];
         $gallery[$imagekey]['description'] = "No description available.";
      }
   }
   
   $t = new HCG_Smarty;

   $t->assign("gallery", $gallery);

   $t->setTplPath("dir_gallery.tpl");
   echo $t->fetch("dir_gallery.tpl");

}


// ------------------------------------------------------------------------
// TAG: slideshow
//
//  This is based on the script PHPSlideShow v0.9 written by Greg Lawler
//  from http://www.zinkwazi.com/scripts
//  PHPSlideshow is relesed under the GPL
//
// ------------------------------------------------------------------------
function slideshow($tpl = "slideshow.tpl")
{

   // number of images to display as thumbnails if a thumbnail directory exists
   // (note that this will be rounded down to an odd number for symmetry.)
   $thumb_row = 5;

   // name of file containing optional page headings
   $heading_info_file = "heading.txt";

   // file containing optional image descriptions
   $pic_info_file="pics.txt";

   // thumbnail directory name (no slashes needed)
   $thumbnail_dir = "thumbnails";

   // language text for various areas...
   $lang_back = "back";
   $lang_next = "next";
   $lang_of = "of";
   $lang_stop_slideshow = "stop slideshow";
   $lang_start_slideshow = "start slideshow";
   $lang_img_hover = "click for next image...";
   $lang_img_alt = "slideshow image";

   // automated slideshow options
   // remember that you need <META_REFRESH> in the <head> section of your html
   // AND the <AUTO_SLIDESHOW_LINK> tag in your page.
   // $delay is the number of seconds to pause between slides...
   $delay = 2;

   // sort images with newest or oldest on top. (this has no effect when 
   // pics.txt is used)
   // $sort_images = "oldest"; 
   $sort_images = "newest"; 

   // set to true to display navigation icons instead of text...
   $show_navigation_buttons = "false";
   $back_button = "/i/lround.gif"; 
   $next_button = "/i/rround.gif";

   ######################################################################
   // grab the variables we want set for newer php version compatability
   $phpslideshow = (isset($_GET['phpslideshow'])) ? $_GET['phpslideshow'] : '';
   $directory = (isset($_GET['directory'])) ? $_GET['directory'] : '';
   $currentPic = (isset($_GET['currentPic'])) ? $_GET['currentPic'] : '';
   $browse = (isset($_GET['browse'])) ? $_GET['browse'] : '';
   $auto = (isset($_GET['auto'])) ? $_GET['auto'] : '';

   // check for platform dependent path info... (for windows and mac OSX)
   $path = empty($_SERVER['PATH_INFO'])?
$_SERVER['PHP_SELF']:$_SERVER['PATH_INFO'];

//   if (file_exists("template.html")) {
//      $template = file_get_contents("template.html");
//   } else {
//      echo "<b>ERROR:</b> Can't find the template.html file";
//      exit;
//   }

   // check that the user did not change the path...
   if (preg_match(':(\.\.|^/|\:):', $directory)) {
      echo "<b>ERROR:</b> Your request contains an invalid path.<br>
      Your directory may not contain .. or : or start with a /<br>";
      exit;
   }

   if (empty($directory)) $directory = ".";
   // if there is no $heading_info_file (see format above) set page heading here
   if ( !file_exists("$directory/$heading_info_file")) {
      $header = "Slideshow";
      $title = "$header";
   } else {
      $heading_info = file("$directory/$heading_info_file");
      $header = "$heading_info[0]";
      $title = $header;
   }
//   $template = str_replace("<SHOW_TITLE>",$title,$template);

   // image / text buttons
   if ($show_navigation_buttons == "true") {
      $back_src = "<img src='$back_button' alt='back' class='nav_buttons' class='nav_buttons'>";
      $next_src = "<img src='$next_button' alt='next' class='nav_buttons' class='nav_buttons'>";
   } else {
      $back_src = "$lang_back";
      $next_src = "$lang_next";
   }	

   if (!file_exists("$directory/$pic_info_file"))
   {
      $dh = opendir("$directory");
      $pic_info = array();
      $time_info = array();
      while ($file = readdir($dh))  // look for these file types....
      {  
         if (preg_match('/(jpg|jpeg|gif|png)$/i',$file))
         {
            $time_info[] = filemtime("$directory/$file");
            $pic_info[] = $file;
         }
      }
      if ($sort_images == "oldest")
      {
         $sortorder = SORT_ASC;
      }
      elseif ($sort_images == "newest")
      {
         $sortorder = SORT_DESC;
      }
      array_multisort($time_info, $sortorder, $pic_info, SORT_ASC, $time_info);
   }
   else
   {
      $pic_info=file("$directory/$pic_info_file");
   }

   // begin messing with the array
   $number_pics = count ($pic_info);
   if (($currentPic > $number_pics)||($currentPic == $number_pics)||!$currentPic)
      $currentPic = '0';
   $item = explode (";", rtrim($pic_info[$currentPic]));
   $last = $number_pics - 1;
   $next = $currentPic + 1;
   if ($currentPic > 0 ) $back = $currentPic - 1;
   else $currentPic = "0";

   $blank = empty($item[1])?'&nbsp;':$item[1];

   if ($currentPic > 0 ) 
   {
      $nav = $back;
   }
   else
   {
      $nav = $last;
   }
   $nav = "<a href='$path?directory=$directory&currentPic=$nav'>$back_src</a>";
   $current_show = "$path?directory=$directory";
   $next_link = "<a href='$path?directory=$directory&currentPic=$next'>$next_src</a>";

   //get comments from the EXIF data if available...
   if (extension_loaded('exif'))
   {
      $curr_image = "$directory/$item[0]";
      $all_exif = @exif_read_data($curr_image,0,true);
      $exifhtml = $all_exif['COMPUTED'];
      $comment = (isset($all_exif['COMMENT'][0])) ? $all_exif['COMMENT'][0] : '';
   }

   $image_title = (isset($item[1])) ? "$item[1]" : '';
   $auto_url = '';

   // meta refresh stuff for auto slideshow...
   if ($auto == "1")
   {
      $auto_url = "&auto=1";
      $meta_refresh = "<meta http-equiv='refresh' content='".$delay;
      $meta_refresh .= ";url=".$path."?directory=".$directory.$auto_url."&currentPic=".$next."'>";
      $auto_slideshow = "<a href='$path?directory=$directory&currentPic=$currentPic'>$lang_stop_slideshow</a>\n";
   }
   else
   {
      $meta_refresh = "";
      $auto_slideshow = "<a href='$path?directory=$directory&auto=1&currentPic=$next'>$lang_start_slideshow</a>\n";
   }

   $images = "<a href='$path?directory=$directory$auto_url&currentPic=$next'>";
   $images .= "<img src='$directory/$item[0]' class='image' alt='$lang_img_alt' title='$lang_img_hover'></a>";

   if (file_exists("$directory/$thumbnail_dir")) { 
      $thumb_row = my_circular($thumbnail_dir, $pic_info, $currentPic, $thumb_row, $directory); 
   }

   $image_filename = "$item[0]";
//   $template = str_replace("<IMAGE_FILENAME>",$image_filename,$template);

//   echo $template;

   $t = new HCG_Smarty;

   $t->assign("SHOW_TITLE", $title);
   $t->assign("CURRENT_SHOW", $current_show);
   $t->assign("BACK", $nav);
   $t->assign("NEXT", $next_link);
   $t->assign("POSITION", "$next $lang_of $number_pics");
   $t->assign("EXIF_COMMENT", $comment);
   $t->assign("IMAGE_TITLE", $image_title);
   $t->assign("META_REFRESH", $meta_refresh);
   $t->assign("AUTO_SLIDESHOW_LINK", $auto_slideshow);
   $t->assign("IMAGE", $images);
   $t->assign("THUMBNAIL_ROW", $thumb_row);
   $t->assign("IMAGE_FILENAME", $image_filename);

   $t->setTplPath($tpl);
   $results[0] = $t->fetch($tpl);
   $results[1] = $title;
   $results[2] = $meta_refresh;
   
   return $results;
}


?>