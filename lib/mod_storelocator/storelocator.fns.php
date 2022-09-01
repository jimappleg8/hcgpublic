<?php

// ---------------------------------------------------------------------------
// storelocator.fns.php
//   written by Jim Applegate
//
// ---------------------------------------------------------------------------

require_once("template.class.php");
require_once("dbi_adodb.inc.php");
require_once("storelocator.inc.php");


// ------------------------------------------------------------------------
// TAG: canadian_City_results
//   This tags return the Store name given a CITY 
//
// ------------------------------------------------------------------------


function canadian_City_results($province) 
{

   global $_HCG_GLOBAL;
   
  // how many rows to show per page
   $rowsPerPage = 20;

 // by default we show first page
  $pageNum = 1;

  // if $_GET['page'] defined, use it as page number
  if(isset($_HCG_GLOBAL['passed_vars']['page']))
 {
    $pageNum = $_HCG_GLOBAL['passed_vars']['page'];
  }

  // counting the offset
  $offset = ($pageNum - 1) * $rowsPerPage;
  
  // assign template to display the CITY
   if ($tpl == "") {
      $tpl = "canadian_City_results.tpl";
   }

   // Query City data by PROVINCE
    
   $query = "SELECT * from stores ".
	     "WHERE State LIKE \"".$province."\" ".
	     "ORDER BY StoreName ASC, City ASC ".
	     "LIMIT ".$offset.", ".$rowsPerPage;

   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $cities = $db->GetAll($query);


  //*** Total rows we have in database ***
 
   $query1 = "SELECT * from stores ".
	     "WHERE State LIKE \"".$province."\" ".
	     "ORDER BY City ASC";

   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $rows = $db->GetAll($query1);
   $numrows = count($rows);

   // Total page?
	$maxPage = ceil($numrows/$rowsPerPage);

	$self = $_SERVER['PHP_SELF'];
   /*** End of Total Page calculation ***/

//*** Creating Prev, first
if ($pageNum > 1)
{
    $page = $pageNum - 1;
    $prev = " <a href=\"$self?page=$page&province=$province\">[Prev]</a> ";
    
    $first = " <a href=\"$self?page=1&province=$province\">[First Page]</a> ";
} 
else
{
    $prev  = ' [Prev] ';       // we're on page one, don't enable 'previous' link
    $first = ' [First Page] '; // nor 'first page' link
}

// print 'next' link only if we're not
// on the last page
if ($pageNum < $maxPage)
{
    $page = $pageNum + 1;
    $next = " <a href=\"$self?page=$page&province=$province\">[Next]</a> ";  
    $last = " <a href=\"$self?page=$maxPage&province=$province\">[Last Page]</a> ";
} 
else
{
    $next = ' [Next] ';      // we're on the last page, don't enable 'next' link
    $last = ' [Last Page] '; // nor 'last page' link
}

   $t = new HCG_Smarty;

   $t->assign("cities", $cities);
   $t->assign("num_cities", $numrows);
   $t->assign("province", $province);
   $t->assign("first", $first);
   $t->assign("prev", $prev);
   $t->assign("next", $next);
   $t->assign("last", $last);
   $t->assign("pageNum", $pageNum);
   $t->assign("maxPage", $maxPage);


   $t->setTplPath($tpl);
   echo $t->fetch($tpl);

}


// ------------------------------------------------------------------------
// TAG: iri_locator_results
//   This is designed specifically to connect to the IRI Product Locator
//   and return the results it produces.
//
// ------------------------------------------------------------------------

function iri_locator_results($site_id = "default", $tpl = "store_locator_results.tpl")
{
   global $_HCG_GLOBAL;
   
   // used by makeXMLTree()
   $_HCG_GLOBAL['XML_LIST_ELEMENTS'] = array("STORE");
   
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   $iri_results = get_iri_locator_results($site_id);
   
   // determine number of pages in results
   $count = $iri_results['RESULTS']['STORES']['COUNT'];
   $perpage = $iri_results['RESULTS']['QUERY']['STORESPERPAGE'];
   $pages = floor($count / $perpage);
   if ($count % $perpage > 0) {
      $pages++;
   }
   
   if ($count == 0) {
      if (!zipcode_exists($iri_results['RESULTS']['QUERY']['ZIP'])) {
         $iri_results['error'] = "The ZIP code <b>".$iri_results['RESULTS']['QUERY']['ZIP']."</b> does not currently appear to be assigned by the US Postal Service to any city. Only about 43,000 of the 100,000 possible 5-digit ZIP codes are currently in use. Please check to make sure you entered the correct ZIP code.";
      }
   }
   
   $t = new HCG_Smarty;
   
   $t->assign("php_self", $_SERVER['PHP_SELF']);   
   $t->assign("brand", $site_id);

   $t->assign("iri_error", $iri_results['error']);
   $t->assign("iri_query", $iri_results['RESULTS']['QUERY']);
   $t->assign("iri_store", $iri_results['RESULTS']['STORES']['STORE']);
   $t->assign("iri_count", $count);
   $t->assign("iri_pages", $pages);
   
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: all_locator_results
//   connects to both the IRI Product Locator and the local database
//   and returns the results it produces.
//
// ------------------------------------------------------------------------

function all_locator_results($site_id = "default", $tpl="store_locator_results_all.tpl")
{
   require_once("mod_products/products.fns.php");
   
   global $_HCG_GLOBAL;
   
   // used by makeXMLTree()
   $_HCG_GLOBAL['XML_LIST_ELEMENTS'] = array("STORE");

   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   $site_id_lc = strtolower($site_id);
   
   $t = new HCG_Smarty;
   
   $t->assign("php_self", $_SERVER['PHP_SELF']);   
   $t->assign("brand", $site_id);
   $t->assign("brand_name", get_brand_name($site_id_lc));

   // ---- IRI Locator Results ----
   
   $iri_results = get_iri_locator_results($site_id);

//   echo "<pre>";
//   print_r($iri_results);
//   echo "</pre>";

   // determine number of pages in results
   $count = $iri_results['RESULTS']['STORES']['COUNT'];
   $perpage = $iri_results['RESULTS']['QUERY']['STORESPERPAGE'];
   if ($perpage != 0) {
      $pages = floor($count / $perpage);
      if ($count % $perpage > 0) {
         $pages++;
      }
   } else {
      $pages = 0;
   }
   
   if ($count == 0) {
      if (!zipcode_exists($iri_results['RESULTS']['QUERY']['ZIP'])) {
         $iri_results['error'] = "The ZIP code <b>".$iri_results['RESULTS']['QUERY']['ZIP']."</b> does not currently appear to be assigned by the US Postal Service to any city. Only about 43,000 of the 100,000 possible 5-digit ZIP codes are currently in use. Please check to make sure you entered the correct ZIP code.";
      }
   }

   $t->assign("iri_error", $iri_results['error']);
   $t->assign("iri_query", $iri_results['RESULTS']['QUERY']);
   $t->assign("iri_store", $iri_results['RESULTS']['STORES']['STORE']);
   $t->assign("iri_count", $count);
   $t->assign("iri_pages", $pages);

   // ---- LOCAL Results ----
   
   $local_results = get_local_locator_results($site_id);
   
   $brand = array();
   
   for ($i=0; $i<count($local_results); $i++)
   {
      $assigned = false;
      if (strtolower($local_results[$i]['status']) != "inactive" &&
          strtolower($local_results[$i]['status']) != "pending")
      {
         $brand_list_lc = trim(strtolower($local_results[$i]['Brands']));
         $notbrand_list_lc = trim(strtolower($local_results[$i]['NotBrands']));
         if ($brand_list_lc != "")
         {
            if (strpos($brand_list_lc, $site_id_lc))
            {
               $brand[] = $local_results[$i];
               $assigned = true;
            } 
         }
         if (!empty($notbrand_list_lc) && $assigned == false)
         {
            if (strpos($notbrand_list_lc, $site_id_lc) == 0)
            {
               $company[] = $local_results[$i];
            }
         }
         elseif ($assigned == false)
         {
            $company[] = $local_results[$i];
         }
      }
   }

   $t->assign("brand_store", $brand);
   $t->assign("brand_count", count($brand));
   $t->assign("company_store", $company);
   $t->assign("company_count", count($company));
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: all_locator_results_mixed
//   connects to both the IRI Product Locator and the local database
//   and returns the results it produces.
//
// ------------------------------------------------------------------------

function all_locator_results_mixed($site_id = "default", $tpl="store_locator_results_mixed.tpl", $sort = "NAME")
{
   require_once("mod_products/products.fns.php");
   
   global $_HCG_GLOBAL;
   
   // used by makeXMLTree()
   $_HCG_GLOBAL['XML_LIST_ELEMENTS'] = array("STORE");

   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   $site_id_lc = strtolower($site_id);
   
   $t = new HCG_Smarty;
   
   $t->assign("php_self", $_SERVER['PHP_SELF']);   
   $t->assign("brand", $site_id);
   $t->assign("brand_name", get_brand_name($site_id_lc));

   // ---- IRI Locator Results ----
   
   $iri_results = get_iri_locator_results($site_id);

//   echo "<pre>";
//   print_r($iri_results);
//   echo "</pre>";

   // determine number of pages in results
   $count = $iri_results['RESULTS']['STORES']['COUNT'];
   $perpage = $iri_results['RESULTS']['QUERY']['STORESPERPAGE'];
   if ($perpage != 0) {
      $pages = floor($count / $perpage);
      if ($count % $perpage > 0) {
         $pages++;
      }
   } else {
      $pages = 0;
   }
   
   if ($count == 0) {
      if (!zipcode_exists($iri_results['RESULTS']['QUERY']['ZIP'])) {
         $iri_results['error'] = "The ZIP code <b>".$iri_results['RESULTS']['QUERY']['ZIP']."</b> does not currently appear to be assigned by the US Postal Service to any city. Only about 43,000 of the 100,000 possible 5-digit ZIP codes are currently in use. Please check to make sure you entered the correct ZIP code.";
      }
   }
   
   $cnt = 0;
   
   $iri_stores = $iri_results['RESULTS']['STORES']['STORE'];
   for ($i=0; $i<count($iri_stores); $i++) {
      $stores[$cnt]['NAME'] = $iri_stores[$i]['NAME'];
      $stores[$cnt]['ADDRESS'] = $iri_stores[$i]['ADDRESS'];
      $stores[$cnt]['CITY'] = $iri_stores[$i]['CITY'];
      $stores[$cnt]['STATE'] = $iri_stores[$i]['STATE'];
      $stores[$cnt]['ZIP'] = $iri_stores[$i]['ZIP'];
      $stores[$cnt]['PHONE'] = $iri_stores[$i]['PHONE'];
      $stores[$cnt]['WEBSITE'] = '';
      $stores[$cnt]['DISTANCE'] = $iri_stores[$i]['DISTANCE'];
      $stores[$cnt]['SRC'] = "iri";
      $cnt++;
   }

//   $t->assign("iri_error", $iri_results['error']);
   $t->assign("iri_query", $iri_results['RESULTS']['QUERY']);
//   $t->assign("iri_store", $iri_results['RESULTS']['STORES']['STORE']);
//   $t->assign("iri_count", $count);
//   $t->assign("iri_pages", $pages);

//   echo "<pre>"; print_r($iri_results['RESULTS']['QUERY']); echo "<pre>";

   // ---- LOCAL Results ----
   
   $local_results = get_local_locator_results($site_id);
   
   for ($i=0; $i<count($local_results); $i++) {
      $stores[$cnt]['NAME'] = htmlentities($local_results[$i]['StoreName']);
      $stores[$cnt]['ADDRESS'] = htmlentities($local_results[$i]['Address1']);
      $stores[$cnt]['CITY'] = htmlentities($local_results[$i]['City']);
      $stores[$cnt]['STATE'] = $local_results[$i]['State'];
      $stores[$cnt]['ZIP'] = $local_results[$i]['Zip'];
      $stores[$cnt]['PHONE'] = $local_results[$i]['Phone'];
      $stores[$cnt]['WEBSITE'] = $local_results[$i]['Website'];
      $stores[$cnt]['DISTANCE'] = $local_results[$i]['distance'];
      $stores[$cnt]['SRC'] = "local";
      $cnt++;
   }
   
   // sort according to desired field
   $sorted_stores = mu_sort($stores, $sort);

//   echo "<pre>"; print_r($stores); echo "</pre>";

   $t->assign("stores", $sorted_stores);
   $t->assign("count", count($stores));
   $t->assign("sort", $sort);
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: local_locator_results
//   connects to the local database and returns the results it produces.
//
// ------------------------------------------------------------------------

function local_locator_results($site_id = "default", $tpl="store_locator_results_local.tpl")
{
   require_once("mod_products/products.fns.php");
   
   global $_HCG_GLOBAL;
   
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   $site_id_lc = strtolower($site_id);
   
   $local_results = get_local_locator_results($site_id);
   
   for ($i=0; $i<count($local_results); $i++) {
      if (strtolower($local_results[$i]['Status']) != "inactive" &&
          strtolower($local_results[$i]['Status']) != "pending") {
         $brand_list_lc = strtolower($local_results[$i]['Brands']);
         $notbrand_list_lc = strtolower($local_results[$i]['NotBrands']);
         if (strpos($brand_list_lc, $site_id_lc)) {
            $brand[] = $local_results[$i];
         } elseif (strpos($notbrand_list_lc, $site_id_lc) == 0) {
            $company[] = $local_results[$i];
         }
      }
   }

   $t = new HCG_Smarty;
   
   $t->assign("php_self", $_SERVER['PHP_SELF']);   
   $t->assign("brand", $site_id);
   $t->assign("brand_name", get_brand_name($site_id_lc));
   $t->assign("zip", $_HCG_GLOBAL['passed_vars']['zip']);
   $t->assign("radius", $_HCG_GLOBAL['passed_vars']['searchradius']);


   $t->assign("brand_store", $brand);
   $t->assign("brand_count", count($brand));
   $t->assign("company_store", $company);
   $t->assign("company_count", count($company));
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: store_locator_form
//
// ------------------------------------------------------------------------

function store_locator_form($loc_code = "", $site_id = "default", $template = "store_locator.tpl")
{
   global $_HCG_GLOBAL;
   
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query = "SELECT ProductName, LocatorCode from pr_product ".
            "WHERE SiteID LIKE '".$site_id."' ".
            "AND Status NOT LIKE 'discontinued' ".
            "AND Status NOT LIKE 'inactive' ".
            "AND LocatorCode NOT LIKE 'none' ".
            "AND (ProductGroup LIKE 'master' ".
            "OR ProductGroup LIKE 'none') ".
            "ORDER BY ProductName";
   $products = $db->GetAll($query);
   
   if (!$products) {
      echo "Error: ".$db->ErrorMsg()."<br><br>";
   }
   
   $t = new HCG_Smarty;

   $t->assign("loc_code", $loc_code);
   $t->assign("products", $products);
   $t->assign("brand", $site_id);
	
   $t->setTplPath($template);
   echo $t->fetch($template);

}

// ------------------------------------------------------------------------
// TAG: store_locator_form_by_category
//
// ------------------------------------------------------------------------

function store_locator_form_by_category($loc_code = "", $site_id = "default", $tpl = "store_locator_by_category.tpl")
{
   global $_HCG_GLOBAL;
   global $cat_list;
   
   if ($site_id == "default")
      $site_id = $_HCG_GLOBAL['site_id'];
   
   $cat_root = get_category_root($site_id);
   
   get_categories($cat_root, 1);
   
   // flatten the categories
   $cat_hds = array();
   for($i=0; $i<count($cat_list); $i++)
   {
      $name = $cat_list[$i]['CategoryName'];
      if ($cat_list[$i]['level'] == 1)
      {
         $cat_hds = array();
         $cat_hds[0] = $cat_list[$i]['CategoryName'];
         $flat_cats[$name]['CategoryName'] = $cat_hds[0];
      }
      elseif ($cat_list[$i]['level'] == 2)
      {
         $cat_hds[1] = $cat_list[$i]['CategoryName'];
         $flat_cats[$name]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[1];
      }
      elseif ($cat_list[$i]['level'] == 3)
      {
         $cat_hds[2] = $cat_list[$i]['CategoryName'];
         $flat_cats[$name]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[2];
      }
      $flat_cats[$name]['CategoryID'] = $cat_list[$i]['CategoryID'];
      $flat_cats[$name]['Products'] = array();
   }
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $sql = 'SELECT p.ProductName, p.LocatorCode, c.CategoryName '.
          'FROM pr_product AS p, '.
               'pr_category AS c, '.
               'pr_product_category AS pc '.
          'WHERE c.SiteID = "'.$site_id.'" '.
          'AND pc.CategoryID = c.CategoryID '.
          'AND pc.ProductID = p.ProductID '.
          'AND c.Status = 1 '.
          'AND p.Status != "discontinued" '.
          'AND p.Status != "inactive" '.
          'AND p.LocatorCode != "none" '.
          'AND (p.ProductGroup = "master" '.
          'OR p.ProductGroup = "none") '.
          "ORDER BY p.ProductName";
   $products = $db->GetAll($sql);
   
   foreach($products AS $product)
   {
      $flat_cats[$product['CategoryName']]['Products'][] = $product;
   }
   
   // convert to a numeric index
   foreach($flat_cats AS $cat)
   {
      $cats[] = $cat;
   }
   
//   echo "<pre>"; print_r($flat_cats); echo "</pre>";
//   exit;

   if ( ! $products)
   {
      echo "Error: ".$db->ErrorMsg()."<br><br>";
   }
   
   $t = new HCG_Smarty;

   $t->assign("loc_code", $loc_code);
   $t->assign("cats", $cats);
   $t->assign("brand", $site_id);
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);

}


?>