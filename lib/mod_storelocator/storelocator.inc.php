<?php

// ---------------------------------------------------------------------------
// storelocator.inc.php
//   written by Jim Applegate
//
// ---------------------------------------------------------------------------

global $_HCG_GLOBAL;

require_once($_HCG_GLOBAL['ziplocator_dir']."/ZipLocator.class.php");
require_once($_HCG_GLOBAL['lib_dir']."/mod_xml/xml.inc.php");


// ------------------------------------------------------------------------
// get_iri_locator_results
//   This is designed specifically to connect to the IRI Product Locator
//   and return the results it produces.
//
// ------------------------------------------------------------------------

function get_iri_locator_results($site_id = "default")
{
   global $_HCG_GLOBAL;
   
   // using PEAR HTTP_Request
   require 'HTTP/Request.php';
   
   $r =& new HTTP_Request('http://productlocator.infores.com/productlocator/servlet/ProductLocatorEngine');
   
   if ($_HCG_GLOBAL['proxy'] != "") {
      $r->setProxy($_HCG_GLOBAL['proxy'], $_HCG_GLOBAL['proxy_port']);
   }

   $r->setMethod(HTTP_REQUEST_METHOD_POST);

   $r->addPostData('productid', $_HCG_GLOBAL['passed_vars']['productid']);
   $r->addPostData('zip', $_HCG_GLOBAL['passed_vars']['zip']);
   $r->addPostData('searchradius', $_HCG_GLOBAL['passed_vars']['searchradius']);
   $r->addPostData('productfamilyid', $_HCG_GLOBAL['passed_vars']['productfamilyid']);
   $r->addPostData('clientid', $_HCG_GLOBAL['passed_vars']['clientid']);
   $r->addPostData('template', $_HCG_GLOBAL['passed_vars']['template']);
   $r->addPostData('stores', $_HCG_GLOBAL['passed_vars']['stores']);
   $r->addPostData('storespagenum', $_HCG_GLOBAL['passed_vars']['storespagenum']);
   $r->addPostData('storesperpage', $_HCG_GLOBAL['passed_vars']['storesperpage']);
   $r->addPostData('etailers', $_HCG_GLOBAL['passed_vars']['etailers']);
   $r->addPostData('etailerspagenum', $_HCG_GLOBAL['passed_vars']['etailerspagenum']);
   $r->addPostData('etailersperpage', $_HCG_GLOBAL['passed_vars']['etailersperpage']);
   $r->addPostData('producttype', $_HCG_GLOBAL['passed_vars']['producttype']);

   $response = $r->sendRequest();

   if (!PEAR::isError($response)) {
      $xml = $r->getResponseBody();
      $result = makeXMLTree($xml);
      // check XML for success variable.
      $result_code = $result['RESULTS']['SUCCESS_CODE'];
      if ($result_code == 0) {
         $result['error'] = "ok";
      } else {
         $result['error'] = $result['RESULTS']['ERROR'];
      }
      return $result;
   } else {
      return $result['error'] = "The request to IRI did not go through. Please try again later.<br>Error Message: ".$response->getMessage();
   }
   
}


// ------------------------------------------------------------------------
// get_local_locator_results
//
// ------------------------------------------------------------------------

function get_local_locator_results($site_id = "default")
{
   global $_HCG_GLOBAL;
   
   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   $zip = $_HCG_GLOBAL['passed_vars']['zip'];
   $radius = $_HCG_GLOBAL['passed_vars']['searchradius'];

   $db = HCGNewConnection('hcg_public');
   
   $zipLoc = new zipLocator($db);
   if ($zip != "") {
      $zipArray = $zipLoc->inradius($zip, $radius);
   }

   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query = "select * from stores";
   for ($i=0; $i<count($zipArray); $i++) {
      if ($i == 0) {
         $query .= " WHERE (";
      } else {
         $query .= " OR ";
      }
      $query .= "Zip = ".$zipArray[$i];
   }
   $query .= ") AND Status != 'inactive' AND Status != 'pending'";
   
   $results = $db->GetAll($query);
   
   // calculate each store's distance from the main zip's location
   $j = 0;
   for ($i=0; $i<count($results); $i++)
   {
//      if ($results[$i]['longitude'] < 0)
//      {
//         $results[$i]['longitude'] = $results[$i]['longitude'] * -1;
//      }
      if ($results[$i]['latitude'] == 0 || $results[$i]['longitude'] == 0)
      {
         $distance = "unknown";
      }
      else
      {
         $distance = $zipLoc->distance($zip, $results[$i]['latitude'], $results[$i]['longitude']);
      }
//      if ($distance <= $radius || $distance == "unknown") {
         $new_results[$j] = $results[$i];
         if ($distance != "unknown") {
            $new_results[$j]['distance'] = number_format($distance, 2);
         } else {
            $new_results[$j]['distance'] = $distance;
         }
         $j++;
//      }
   }
   $new_sorted_results = mu_sort($new_results, "distance");
   return $new_sorted_results;
}

// ------------------------------------------------------------------------
// zipcode_exists()
//
// ------------------------------------------------------------------------
function zipcode_exists($zip)
{
   $db = HCGNewConnection('hcg_public');
   $query = "SELECT zipcode FROM zipcodes_us ".
            "WHERE zipcode LIKE \"".$zip."\"";
   $results = $db->GetAll($query);
   return count($results);
}

// ------------------------------------------------------------------------
// scrape_lat_long()
//
// ------------------------------------------------------------------------
function scrape_lat_long($address)
{
   global $_HCG_GLOBAL;
   
  // using PEAR HTTP_Request
   require 'HTTP/Request.php';
   
   $request = "http://www.maporama.com/share/map.asp".
              "?COUNTRYCODE=".$address['Country'].
              "&_XgoGCAddress=".urlencode($address['Address1']).
              "&Zip=".$address['Zip'].
              "&State=".$address['State'].
              "&_XgoGCTownName=".urlencode($address['City']);
   echo $request;
   
   $r =& new HTTP_Request($request);
   if ($_HCG_GLOBAL['proxy'] != "") {
      $r->setProxy($_HCG_GLOBAL['proxy'], $_HCG_GLOBAL['proxy_port']);
   }
   $response = $r->sendRequest();

   if (!PEAR::isError($response)) {
      $page = $r->getResponseBody();
   } else {
      return "<br>Error Message: ".$response->getMessage();
   }
   
   //scrape the resulting page for latitude and longitude information
   
   $pos = strpos($page, "Lat-Long:");
   $pos2 = strpos($page, "SearchMapFontText\">", $pos) + 19;
   $pos3 = strpos($page, "SearchMapFontText\">", $pos2) + 19;
   $pos4 = strpos($page, "</td>", $pos3);
   
   $lat_long = substr($page, $pos3, ($pos4-$pos3));
   
   $results = explode(",", $lat_long);
   
   $location['latitude'] = trim($results[0]);
   $location['longitude'] = trim($results[1]);
   
   return $location;

}

// ------------------------------------------------------------------------
// $parent is the parent of the children we want to see
// $level is increased when we go deeper into the tree,
//        used to display a nice indented tree
// ------------------------------------------------------------------------
function get_categories($parent, $level)
{
   global $cat_list;
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // retrieve all children of $parent
   $sql = 'SELECT * FROM pr_category '.
          'WHERE CategoryParentID = '.$parent.' '.
          'AND Status = 1 '.
          'ORDER BY CategoryOrder';
   $result = $db->GetAll($sql);
   
   // display each child
   foreach ($result AS $row)
   {
       // add this category to the list
       $row['level'] = $level;
       $cat_list[] = $row;

       // call this function again to display this
       // child's children
       get_categories($row['CategoryID'], $level+1);
   }
} 

// ------------------------------------------------------------------------
// $parent is the parent of the children we want to see
// $level is increased when we go deeper into the tree,
//        used to display a nice indented tree
// ------------------------------------------------------------------------
function get_category_root($site_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $sql = 'SELECT CategoryID FROM pr_category '.
          'WHERE SiteID = "'.$site_id.'" '.
          'AND CategoryCode = "root"'; 
   $result = $db->GetRow($sql);
   
   return $result['CategoryID'];
}


?>