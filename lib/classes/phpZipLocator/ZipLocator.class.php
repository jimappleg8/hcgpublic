<?php

// +----------------------------------------------------------------------+
// | Filename: phpZipLocator.php                                           |
// +----------------------------------------------------------------------+
// | Copyright (c) http://www.sanisoft.com                                |
// +----------------------------------------------------------------------+
// | License (c) This software is licensed under LGPL                     |
// +----------------------------------------------------------------------+
// | Description: A simple class for finding distances between two zip    |
// | codes, The distance calculation is based on Zipdy package found      |
// | at http://www.cryptnet.net/fsp/zipdy/ written by V. Alex Brennen     |
// | <vab@cryptnet.net>                                                   |
// | You can also do radius calculations to find all the zipcodes within  |
// | the radius of x miles                                                |
// +----------------------------------------------------------------------+
// | Authors: Dr Tarique Sani <tarique@sanisoft.com>                      |
// |          Girish Nair <girish@sanisoft.com>                           |
// +----------------------------------------------------------------------+
// | Adapted for use with the hcgPublic Framework by Jim Applegate        |
// +----------------------------------------------------------------------+
//
// $Id$

class zipLocator
{

    var $_DB;
    
    function zipLocator($db)
    {
       $this->_DB = $db;
       $this->_DB->SetFetchMode(ADODB_FETCH_ASSOC);
    }
    
    
    /**
     * Short description.
     * This method returns the distance in Miles between two zip codes, a zip code and another location (latitude and longitude), or two locations depending on how many parameters are passed.
     * Detail description
     * This method returns the distance in Miles between two zip codes, if either of the zip codes is not found and error is retruned
     * @param      zipOne - The first zip code
     * @param      zipTwo - The second zip code
     * @global     none
     * @since      1.0
     * @access     public
     * @return     string
     * @update
    */
    function distance()
    {
       $num_params = func_num_args();
       if ($num_params == 2 || $num_params == 3) {
          $zipOne = func_get_arg(0);
          $query = "SELECT * FROM zipcodes_us WHERE zipcode = $zipOne";
          $zipOneRec = $this->_DB->GetRow($query);
          if (count($zipOneRec) < 1) {
              return "First Zip Code not found";
          } else {
              $lat1 = $zipOneRec["latitude"];
              $lon1 = $zipOneRec["longitude"];
          }
       }
       if ($num_params == 2) {
          $zipTwo = func_get_arg(1);
          $query = "SELECT * FROM zipcodes_us WHERE zipcode = $zipTwo";
          $zipTwoRec = $this->_DB->GetRow($query);
          if (count($zipTwoRec) < 1) {
              return "Second Zip Code not found";
          } else {
              $lat2 = $zipTwoRec["latitude"];
              $lon2 = $zipTwoRec["longitude"];
          }
       }
       if ($num_params == 3) {
          $lat2 = func_get_arg(1);
          $lon2 = func_get_arg(2);
       }
       if ($num_params == 4) {
          $lat1 = func_get_arg(0);
          $lon1 = func_get_arg(1);
          $lat2 = func_get_arg(2);
          $lon2 = func_get_arg(3);       
       }

       /* Convert all the degrees to radians */
       $lat1 = $this->deg_to_rad($lat1);
       $lon1 = $this->deg_to_rad($lon1);
       $lat2 = $this->deg_to_rad($lat2);
       $lon2 = $this->deg_to_rad($lon2);

       /* Find the deltas */
       $delta_lat = $lat2 - $lat1;
       $delta_lon = $lon2 - $lon1;

       /* Find the Great Circle distance */
       $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);

       $EARTH_RADIUS = 3956;
       $distance = $EARTH_RADIUS * 2 * atan2(sqrt($temp),sqrt(1-$temp));

       return $distance;

    } // end func

    /**
     * Short description.
     * Converts degrees to radians
     * @param      deg - degrees
     * @global     none
     * @since      1.0
     * @access     private
     * @return     void
     * @update
    */
    function deg_to_rad($deg)
    {
        $radians = 0.0;
        $radians = $deg * M_PI/180.0;
        return($radians);
    }


    /**
     * Short description.
     * This method returns an array of zipcodes found with the radius supplied
     * Detail description
     * This method returns an array of zipcodes found with the radius supplied in miles, if the zip code is invalid an error string is returned
     * @param      zip - The zip code
     * @param      radius - The radius in miles
     * @global     none
     * @since      1.0
     * @access     public
     * @return     array/string
     * @update     date time
    */
    function inradius($zip, $radius)
    {
        $query="SELECT * FROM zipcodes_us WHERE zipcode='$zip'";
        $zipRec = $this->_DB->GetRow($query);
        if (count($zipRec) < 1) {
           return "Zip Code not found";
        } else {
           $lat = $zipRec["latitude"];
           $lon = $zipRec["longitude"];

           $query="SELECT zipcode FROM zipcodes_us WHERE ".
                  "(POW((69.1*(longitude-\"$lon\")*".
                  "cos($lat/57.3)),\"2\")+".
                  "POW((69.1*(latitude-\"$lat\")),\"2\"))".
                  "<($radius*$radius)";

           $allZips = $this->_DB->GetAll($query);
           if (count($allZips) > 0) {
              $i = 0;
              foreach($allZips as $thisZip) {
                 $zipArray[$i] = $thisZip["zipcode"];
                 $i++;
              }
           }
        }
     return $zipArray;
    } // end func

} // end class
?>