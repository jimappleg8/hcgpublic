<?php

// =========================================================================
// gallery.inc.php
//   Some of these functions were taken from the Qdig script, 
//   but they were modified heavily.
//
//   See Qdig Functions Summary at 
//    http://home.cis-dept.com/support/scripts/qdig/functions_list.txt
//
// =========================================================================


// ------------------------------------------------------------------------
// getImageFilenames()
//   Get the names of image files in a directory.
//   From Qdig, modified by Jim Applegate
//
// ------------------------------------------------------------------------

function getImageFilenames($path)
{
   if ((is_dir($path)) && (is_readable($path))) {
      $pwd_handle = opendir($path) or die($php_errormsg);
      while (false !== ($file = readdir($pwd_handle))) {
         if (is_file($path . '/' . $file) 
            && (is_readable($path.'/'.$file))
            && (eregi('\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$|\.eps$|\.tif$|\.tiff$', $file))
            && (!eregi('^TN_|^PV_|^DE_', $file))) {
				$imgs[]=$file;
         }
      }
      closedir($pwd_handle);
      if (isset($imgs)) {
         natcasesort($imgs);
         foreach($imgs as $x) {
            $sorted_files[]=$x;
         }
         return $sorted_files;
      } else {
         echo "ERROR: \$imgs is not defined.";
      }
   } else {
      echo "ERROR: path (".$path.")is either not a directory or it is not readable.";
   }
} // end function getImageFilenames()


// ------------------------------------------------------------------------
// createThumbnail()
//   Generate a thumbnail image for an image.
//   Takes an array of settings: pwd, qual, prefix, src_file, 
//                               tn_width, tn_height
//   Based on function from Qdig
//
// ------------------------------------------------------------------------

function createThumbnail($cnvrt_thmb)
{
   global $_HCG_GLOBAL;
   
   if (! isset($cnvrt_thmb['tn_width'])) {
      $cnvrt_thmb['tn_width'] = 80;
   }
   if (! isset($cnvrt_thmb['tn_height'])) {
      $cnvrt_thmb['tn_height'] = 80;
   }
   if (! isset($cnvrt_thmb['qual'])) {
      $cnvrt_thmb['qual'] = 90;
   }
   if (! isset($cnvrt_thmb['prefix'])) {
      $cnvrt_thmb['prefix'] = "TN_";
   }
   if (is_writable($cnvrt_thmb['pwd'])) {

      $tn_width  = $cnvrt_thmb['tn_width'];
      $tn_height = $cnvrt_thmb['tn_height'];
      $pwd       = $cnvrt_thmb['pwd'];
      $prefix    = $cnvrt_thmb['prefix'];
      $qual      = $cnvrt_thmb['qual'];
      $src_file  = $cnvrt_thmb['src_file'];
			
      list($img_base,$extension) = explode(".",$src_file);
			
      $tn_file = $prefix . $img_base . ".jpg";
      
      $command = $_HCG_GLOBAL['convert_cmd'] .
          ' -geometry ' . $tn_width . 'x' . $tn_height .
          ' -quality ' . $qual .
          ' "' . $pwd . '/' . $src_file . '"' .
          ' "' . $pwd . '/' . $tn_file . '"';
      
      exec($command);

   } else {
      echo "ERROR: Thumbnail directory is not writable.";
   }

} // end function createThumbnail()


// ------------------------------------------------------------------------
// createPreview()
//   Generate a preview image for an image.
//   Takes an array of settings: pwd, qual, prefix, src_file, 
//                               pv_width, pv_height
//   Based on function from Qdig
//
// ------------------------------------------------------------------------

function createPreview($cnvrt_prev)
{
   global $_HCG_GLOBAL;
   
   if (! isset($cnvrt_prev['pv_width'])) {
      $cnvrt_prev['pv_width'] = 450;
   }
   if (! isset($cnvrt_prev['pv_height'])) {
      $cnvrt_prev['pv_height'] = 450;
   }
   if (! isset($cnvrt_prev['qual'])) {
      $cnvrt_prev['qual'] = 90;
   }
   if (! isset($cnvrt_prev['prefix'])) {
      $cnvrt_prev['prefix'] = "PV_";
   }
   if (is_writable($cnvrt_prev['pwd'])) {

      $pv_width  = $cnvrt_prev['pv_width'];
      $pv_height = $cnvrt_prev['pv_height'];
      $pwd       = $cnvrt_prev['pwd'];
      $prefix    = $cnvrt_prev['prefix'];
      $qual      = $cnvrt_prev['qual'];
      $src_file  = $cnvrt_prev['src_file'];
			
      list($img_base,$extension) = explode(".",$src_file);
			
      $pv_file = $prefix . $img_base . ".jpg";
      
      $command = $_HCG_GLOBAL['convert_cmd'] .
          ' -geometry ' . $pv_width . 'x' . $pv_height .
          ' -quality ' . $qual .
          ' "' . $pwd . '/' . $src_file . '"' .
          ' "' . $pwd . '/' . $pv_file . '"';
      
      exec($command);

   } else {
      echo "ERROR: Preview directory is not writable.";
   }

} // end function createThumbnail()


// ------------------------------------------------------------------------
// my_circular()
//
//   Based on function from PHP Slideshow
//
// ------------------------------------------------------------------------
function my_circular($thumbnail_dir, $a_images, $currentPic, $thumb_row, $directory) 
{
   global $path;
   global $auto_url;

   // get size of $a_images array...
   $number_pics = count($a_images);
   // do a little error checking...
   if ($currentPic > $number_pics) $currentPic = 0;
   if ($currentPic < 0) $currentPic = 0;
   if ($thumb_row < 0) $thumb_row = 1;

   // check if thumbnail row is greater than number of images...
   if ($thumb_row > $number_pics) $thumb_row = $number_pics;

   // split the thumbnail number and make it symmetrical...
   $half = floor($thumb_row/2);

   // show thumbnails
   // left hand thumbs
   if (($currentPic - $half) < 0) { // near the start...
      $underage = ($currentPic-1) - $half; 
      for ( $x=($number_pics-abs($underage+1)); $x<$number_pics; $x++) {
         $next=$x;
         $item = explode (";", rtrim($a_images[$x]));
         $out .= "\n<a href='$path?directory=$directory$auto_url&currentPic=$next' class='thumbnail'><img src='$directory/$thumbnail_dir/".$item[0]."' class='thumbnail'></a>";
      }
      for ( $x=0; $x<$currentPic  ; $x++ ) {
         $next=$x;
         $item = explode (";", rtrim($a_images[$x]));
         $out .= "\n<a href='$path?directory=$directory$auto_url&currentPic=$next' class='thumbnail'><img src='$directory/$thumbnail_dir/".$item[0]."' class='thumbnail'></a>";
      }
   } else {
      for ( $x=$currentPic-$half; $x < $currentPic; $x++ ) {
         $next=$x;
         $item = explode (";", rtrim($a_images[$x]));
         $out .= "\n<a href='$path?directory=$directory$auto_url&currentPic=$next' class='thumbnail'><img src='$directory/$thumbnail_dir/".$item[0]."' class='thumbnail'></a>";
      }
   }

   // show current (center) image thumbnail...
   $item = explode (";", rtrim($a_images[$currentPic]));
   $out .= "\n<img src='$directory/$thumbnail_dir/".rtrim($item[0])."' class='thumbnail_center'>";

   // array for right side...
   if (($currentPic + $half) >= $number_pics) { // near the end
      $overage = (($currentPic + $half) - $number_pics);
      for ( $x=$currentPic+1; $x < $number_pics; $x++) {
         $next=$x;
         $item = explode (";", rtrim($a_images[$x]));
         $out .= "\n<a href='$path?directory=$directory$auto_url&currentPic=$next' class='thumbnail'><img src='$directory/$thumbnail_dir/".$item[0]."' class='thumbnail'></a>";
      }
      for ( $x=0; $x<=abs($overage); $x++) {
         $next=$x;
         $item = explode (";", rtrim($a_images[$x]));
         $out .= "\n<a href='$path?directory=$directory$auto_url&currentPic=$next' class='thumbnail'><img src='$directory/$thumbnail_dir/".$item[0]."' class='thumbnail'></a>";
      }
   } else {
      for ( $x=$currentPic+1; $x<=$currentPic+$half; $x++ ) {  // right hand thumbs
         $next=$x;
         $item = explode (";", rtrim($a_images[$x]));
         $out .= "\n<a href='$path?directory=$directory$auto_url&currentPic=$next' class='thumbnail'><img src='$directory/$thumbnail_dir/".$item[0]."' class='thumbnail'></a>";
      }
   }
   return $out;
}



?>