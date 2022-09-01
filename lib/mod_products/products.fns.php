<?php

// version 1.1 - changed to use new products database structure 8/04

require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';
require_once 'mod_products/products.inc.php';


// ------------------------------------------------------------------------
// TAG: product_detail
//
//   NOTE: this function does not check to see if the requested product
//   is discontinued. It returns the "status" field, however, and the
//   product_detail template can check the status and respond accordingly.
// ------------------------------------------------------------------------
function product_detail($prod_id, $cat_id = "", $site_id = "default", $template = "product_detail.tpl")
{
   global $_HCG_GLOBAL;
   
   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   $product = getProductData($prod_id);
   if (preg_match("/\d+/", $cat_id)) {
      settype($cat_id, "integer");
      $category = getCategoryData($cat_id);
   } else {
      // this is to make the function backwards compatible
      $product['cat_id'] = $cat_id;
      $product['cat_id_title'] = category_id_title($cat_id);
   }
   $nutfacts = nutrition_facts($prod_id);
   
   $t = new HCG_Smarty;

   $t->assign("product", $product);
   $t->assign("nutfacts", $nutfacts);
   if (!empty($category)) {
      $t->assign("category", $category);
   }
	
   $t->setTplPath($template);
   echo $t->fetch($template);
}


// ------------------------------------------------------------------------
// TAG: product_meta
//
// ------------------------------------------------------------------------
function product_meta($prod_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT ProductName, MetaTitle, MetaDescription, MetaMisc, MetaKeywords ".
      "FROM pr_product " .
      "WHERE ProductID = ".$prod_id;
   $results = $db->GetRow($query1);
   
   return $results;
}


// ------------------------------------------------------------------------
// TAG: random_product
//
// ------------------------------------------------------------------------
function random_product($bool_field = "none", $cat_id = 0, $site_id = "default")
{
   global $_HCG_GLOBAL;
   
   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   if ($cat_id == 0) {
      if ($bool_field == "none") {
         $product_list = getProdIDsInSite($site_id);
      } else {
         $product_list = getProdIDsPerField($bool_field, $site_id);
      }
   } else {
      $product_list = getProdIDsInCategory($cat_id);
   }
   
   // random code taken from The PHP Cookbook, p.469
   $m = 1000000;
   $prod_id = $product_list[((mt_rand(1,$m * count($product_list))-1)/$m)];
   
   $product = getProductData($prod_id);
   $category = getFirstCategory($prod_id);
   
//   echo "<pre>"; print_r($product); echo "</pre>";
   
   $t = new HCG_Smarty;

   $t->assign("product", $product);
   $t->assign("category", $category);
	
   $t->setTplPath("random_product.tpl");
   echo $t->fetch("random_product.tpl");
}


// ------------------------------------------------------------------------
// TAG: category_data
//
// ------------------------------------------------------------------------
function category_data($cat_id, $site_id="default", $tpl="category_data.tpl")
{
   global $_HCG_GLOBAL;
   
   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   $category = getCategoryData($cat_id);

   $t = new HCG_Smarty;

   $t->assign("category", $category);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
} 


// ------------------------------------------------------------------------
// TAG: category_meta
//
// ------------------------------------------------------------------------
function category_meta($cat_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT CategoryName, MetaTitle, MetaDescription, MetaMisc, MetaKeywords ".
      "FROM pr_category " .
      "WHERE CategoryID = ".$cat_id;
   $results = $db->GetRow($query1);
   
   return $results;
}


// ------------------------------------------------------------------------
// TAG: category_list
//
// ------------------------------------------------------------------------
function category_list($cat_id, $site_id="default", $tpl="category_list.tpl")
{
   global $_HCG_GLOBAL;
   
   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   if (!is_numeric($cat_id)) {
      // this is to make the function backwards compatible
      $category['id'] = $cat_id;
      $category['title'] = category_id_title($cat_id);
   } else {
      $category = getCategoryData($cat_id);
   }
   
   if (!is_numeric($cat_id)) {
      $cat_id = getCategoryIDFromCode($cat_id);
   }
   $prod_list = getProdIDsInCategory($cat_id);
   
   // go through the list and build data structure
   $count = 0;
   foreach ($prod_list as $prod_id) {
      $items[$count] = getProductData($prod_id);
      $count++;
   }
   
   foreach($items as $item) {
      $sortAux[] = $item['SortOrder'];
   }
   array_multisort($sortAux, SORT_ASC, $items);
   
   $t = new HCG_Smarty;

   $t->assign("category", $category);
   $t->assign("items", $items);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: sub_category_list
//   Displays a category page divided by the category's sub-categories.
//
//   NOTE: This function doesn not need to be backward compatible because 
//   it is newer than the change to using category id numbers.
//
// ------------------------------------------------------------------------
function sub_category_list($cat_id, $site_id="default", $tpl="sub_category_list.tpl")
{
   global $_HCG_GLOBAL;

   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }

   // get a list of categories ($cat_id and all it's children)
   // excluding any inactive categories.
   $cat_list = getCategoryChildren($cat_id, 0);
   if ($cat_list == false) {
      echo "This is an inactive category.";
   }
//   echo "<pre>"; print_r($cat_list); echo "</pre>";

   // get all data for all products assigned to each category
   $count = 0;
   $max_level = 0;
   for ($i=1; $i<count($cat_list); $i++) {

      $prod_list = getProdIDsInCategory($cat_list[$i]['CategoryID']);

      // go through the list and build data structure
      foreach ($prod_list as $prod_id) {
         $items[$count] = getProductData($prod_id);
         $items[$count]['CatID'] = $cat_list[$i]['CategoryID'];
         if ($cat_list[$i]['Level'] > $max_level) {
            $max_level = $cat_list[$i]['Level'];
         }
         $count++;
      }
   }

//   echo "<pre>"; print_r($items); echo "</pre>";

   $t = new HCG_Smarty;

   $t->assign("max_level", $max_level);
   $t->assign("cat_list", $cat_list);
   $t->assign("items", $items);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);

}


// ------------------------------------------------------------------------
// TAG: multi_category_list
//   Shows all products in a secondary category in a list that divides 
//   according to primary categories. e.g. all kosher products divided by
//   product type category.
//
//   Parameters:
//      $cat_id    the secondary category id. A $cat_id of 0 indicates that 
//                   you want a list of all products for that brand.
//      $cat_list  list of categories not to include in primary list
//      $site_id   if you want to list products from a different site
//      $tpl       template name to override default
//
// ------------------------------------------------------------------------
function multi_category_list($cat_id, $cat_list = array(), $site_id = "default", $tpl="multi_category_list.tpl")
{
   global $_HCG_GLOBAL;

   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }

   if (!is_numeric($cat_id)) {
      // this is to make the function backwards compatible
      if ($cat_id == "all") {
         $cat_id = 0;
      } else {
         $cat_id = getCategoryIDFromCode($cat_id);
      }
   }
   if ($cat_id != 0) {
      $category = getCategoryData($cat_id);
      $category['title'] = $category['CategoryName'];
      $category['id'] = $category['CategoryCode'];
   }

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   // get Product IDs for all items that fit the main category.
   // Tables are joined to check whether the product is discontinued
   // and to limit the search according to site ID.

   if ($cat_id == 0) {  
      $query1 = "SELECT pr_product_category.ProductID " .
         "FROM pr_product_category, pr_product " .
         "WHERE pr_product_category.ProductID = pr_product.ProductID " .
         "AND pr_product.SiteId LIKE \"$site_id\" " .
         "AND pr_product.Status NOT LIKE \"discontinued\" ".
         "AND pr_product.Status NOT LIKE \"pending\" " .
         "ORDER BY pr_product_category.ProductID DESC";
   } else {
      $query1 = "SELECT pr_product_category.ProductID " .
         "FROM pr_product_category, pr_product " .
         "WHERE pr_product_category.CategoryID LIKE \"$cat_id\" " .
         "AND pr_product_category.ProductID = pr_product.ProductID " .
         "AND pr_product.SiteId LIKE \"$site_id\" " .
         "AND pr_product.Status NOT LIKE \"discontinued\" ".
         "AND pr_product.Status NOT LIKE \"pending\" " .
         "ORDER BY pr_product_category.ProductID DESC";   
   }
   $row1 = $db->GetAll($query1);

   // get all category listings and product information for each 
   // Product ID found above. Sort products by name.

   $query2 = "SELECT pr_product_category.ProductID, pr_product_category.CategoryID, pr_product.* " .
      "FROM pr_product_category, pr_product " .
      "WHERE pr_product_category.CategoryID NOT LIKE \"$cat_id\" ";

   // exclude any categories that are in the exclude list
   if (!empty($cat_list)) {
      foreach ($cat_list as $omit_item) {
         if (!is_numeric($omit_item)) {
            // this is to make the function backwards compatible
            $omit_item = getCategoryIDFromCode($omit_item);
         }
         $query2 .= "AND pr_product_category.CategoryID NOT LIKE \"$omit_item\" ";
      }
   }
   $first = 1;
   for($i=0; $i<count($row1); $i++) {
      if ($first == 1) {
         $query2 .= "AND (";
         $first = 0;
      } else {
         $query2 .= "OR ";
      }
      $query2 .= "(pr_product_category.ProductID = ".$row1[$i]['ProductID']." ".
         "AND pr_product.ProductID = ".$row1[$i]['ProductID'].") ";
   }
   $query2 .= ") order by pr_product.ProductName";

   $row2 = $db->GetAll($query2);

   // build data structure
   
   // 1. create category list from full product list, removing redundancy

   $category_list = array();
   for($i=0; $i<count($row2); $i++) {
      if (!in_array($row2[$i]['CategoryID'], $category_list)) {
         $category_list[] = $row2[$i]['CategoryID'];
      }
   }
   
   // 2. get category info based on list, sorting according to 
   //    CategoryOrder and eliminating any inactive categories

   $query3 = "SELECT * FROM pr_category ";
   $first = 1;
   for($i=0; $i<count($category_list); $i++) {
      if ($first == 1) {
         $query3 .= "WHERE ";
         $first = 0;
      } else {
         $query3 .= "OR ";
      }
      $query3 .= "CategoryID = ".$category_list[$i]." ";
   }
   $query3 .= "AND Status = 1 ";
   $query3 .= "ORDER BY CategoryOrder";
   $category['list'] = $db->GetAll($query3);

//   echo $query3;
//   echo "<pre>"; print_r($row2); echo "</pre>";
   
   // 3. assign products to array with index corresponding to category list
   
   // create reverse lookup array for categories
   $cat_key = array();
   for ($j=0; $j<count($category['list']); $j++) {
      $cat_key[$category['list'][$j]['CategoryID']] = $j;
   }
   // go through each product and assign it to the correct item id
   for ($i=0; $i<count($row2); $i++) {
      if (($row2[$i]['Status'] != "discontinued" &&
           $row2[$i]['Status'] != "pending") &&
          ($row2[$i]['ProductGroup'] == "none" ||
           $row2[$i]['ProductGroup'] == "master")) {
         $items[$cat_key[$row2[$i]['CategoryID']]][] = $row2[$i];
      }
   }

   
//   echo "<pre>"; print_r($category); echo "</pre>";
//   echo "<pre>"; print_r($items); echo "</pre>";

   $t = new HCG_Smarty;

   $t->assign("category", $category);
   $t->assign("items", $items);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: multi_category_list_by_field
//   Shows all products as selected by $select in a list that divides 
//   according to primary categories. e.g. all new products divided by
//   category. The select statement must result in a list of ProductIDs:
//
//      $select = "SELECT ProductID FROM pr_product " .
//         "WHERE FlagAsNew > 0 " .
//         "AND SiteId LIKE \"cs\" " .
//         "AND Status NOT LIKE \"discontinued\" ".
//         "AND Status NOT LIKE \"pending\"";;
//
//   The $category parameter is an array that mimics the pr_category 
//   table to allow for the creation of a pseudo-category with the same 
//   properties as a real one. You can include any fields you'd like, 
//   including ones that are not part of pr_category, but the practical 
//   ones are as follows:
//      $category['CategoryCode']
//      $category['CategoryName']
//      $category['CategoryDescription']
//      $category['CategoryText']
//      $category['CategoryType']
//
// ------------------------------------------------------------------------
function multi_category_list_by_field($select, $category, $cat_list = array(), $site_id = "default", $tpl="multi_category_list_by_field.tpl")
{
   global $_HCG_GLOBAL;

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   } elseif ($site_id != $_HCG_GLOBAL['site_id']) {
      $site = $db->GetRow("SELECT * FROM site WHERE SiteID LIKE \"$site_id\"");
   }

   // get Product IDs for all items that fit the main category.
   // Tables are joined to check whether the product is discontinued
   // and to limit the search according to site ID.

   $row1 = $db->GetAll($select);

   // get all category listings and product information for each 
   // Product ID found above. Sort products by name.

   $query2 = "SELECT pr_product_category.ProductID, pr_product_category.CategoryID, pr_product.* " .
      "FROM pr_product_category, pr_product " .
      "WHERE pr_product_category.CategoryID NOT LIKE \"$cat_id\" ";

   // exclude any categories that are in the exclude list
   if (!empty($cat_list)) {
      foreach ($cat_list as $omit_item) {
         if (!is_numeric($omit_item)) {
            // this is to make the function backwards compatible
            $omit_item = getCategoryIDFromCode($omit_item);
         }
         $query2 .= "AND pr_product_category.CategoryID NOT LIKE \"$omit_item\" ";
      }
   }
   $first = 1;
   for($i=0; $i<count($row1); $i++) {
      if ($first == 1) {
         $query2 .= "AND (";
         $first = 0;
      } else {
         $query2 .= "OR ";
      }
      $query2 .= "(pr_product_category.ProductID = ".$row1[$i]['ProductID']." ".
         "AND pr_product.ProductID = ".$row1[$i]['ProductID'].") ";
   }
   $query2 .= ") order by pr_product.ProductName";

   $row2 = $db->GetAll($query2);

   // build data structure
   
   // 1. create category list from full product list, removing redundancy

   $category_list = array();
   for($i=0; $i<count($row2); $i++) {
      if (!in_array($row2[$i]['CategoryID'], $category_list)) {
         $category_list[] = $row2[$i]['CategoryID'];
      }
   }
   
   // 2. get category info based on list, sorting according to 
   //    CategoryOrder and eliminating any inactive categories

   $query3 = "SELECT * FROM pr_category ";
   $first = 1;
   for($i=0; $i<count($category_list); $i++) {
      if ($first == 1) {
         $query3 .= "WHERE ";
         $first = 0;
      } else {
         $query3 .= "OR ";
      }
      $query3 .= "CategoryID = ".$category_list[$i]." ";
   }
   $query3 .= "AND Status = 1 ";
   $query3 .= "ORDER BY CategoryOrder";
   $category['list'] = $db->GetAll($query3);

//   echo $query3;
//   echo "<pre>";
//   print_r($category);
//   echo "</pre>";
   
   // 3. assign products to array with index corresponding to category list
   
   for($i=0; $i<count($row2); $i++) {
      for($j=0; $j<count($category['list']); $j++) {
         if ($category['list'][$j]['CategoryID'] == $row2[$i]['CategoryID']) {
            break;
         }
      }
      if (($row2[$i]['Status'] != "discontinued" &&
           $row2[$i]['Status'] != "pending") &&
          ($row2[$i]['ProductGroup'] == "none" ||
           $row2[$i]['ProductGroup'] == "master")) {
         $items[$j][] = $row2[$i];
      }
   }
   
   $db->Close();

   $t = new HCG_Smarty;

   $t->assign("site", $site);
   $t->assign("category", $category);
   $t->assign("items", $items);

   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: category_id_title
//
// ------------------------------------------------------------------------
function category_id_title($cat_id)
{
   return ucwords(str_replace("_", " ", $cat_id));
}


// ------------------------------------------------------------------------
// TAG: get_brand_name()
//   Returns the brand name given a site ID. If no siteID is given, it
//   uses the current site.
//
// ------------------------------------------------------------------------
function get_brand_name($site_id = "default")
{
   global $_HCG_GLOBAL;
   
   if ($site_id == "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query = "SELECT BrandName from site ".
            "WHERE SiteID LIKE '".$site_id."'";
   $brand = $db->GetRow($query);
   
   $db->Close();
   
   return $brand['BrandName'];
}


// ------------------------------------------------------------------------
//  TAG: nutrition_facts
//
// ------------------------------------------------------------------------
function nutrition_facts($prod_id, $display_hd = false)
{
   global $_HCG_GLOBAL;

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query = "SELECT * FROM pr_nlea WHERE ProductID = $prod_id";
   $nutfacts = $db->GetRow($query);
   
   $db->Close();
   
   if (count($nutfacts) == 0)
      return "";

   // calculate the total number of table rows
   $nutfacts['total_rows'] = 100;
   
   $nutfacts['STMT1'] = isset($nutfacts['STMT1']) ? $nutfacts['STMT1'] : 'NO';
   
   // build copy for STMT1 if applicable
   if (strtoupper($nutfacts['STMT1']) == "YES") {
      $nutfacts['STMT1Q'] = build_stmt1($nutfacts['STMT1Q']);
   }
   
   $nutfacts['display_hd'] = $display_hd;

   $tpl = "nutrition_facts_" . $nutfacts['TYPE'] . ".tpl";

   $t = new HCG_Smarty;

   $t->assign("nutfacts", $nutfacts);
   $t->register_function("draw_line", "draw_line");
   $t->register_function("draw_line_wide", "draw_line_wide");
   $t->register_function("draw_baby_line", "draw_baby_line");
   $t->register_function("draw_baby_line2", "draw_baby_line2");
	
   $t->setTplPath($tpl);
   return $t->fetch($tpl);
}



?>