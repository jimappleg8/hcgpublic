<?php

// version 1.1 - changed to use new products database structure 8/04

// ------------------------------------------------------------------------
// getProductData
//
// ------------------------------------------------------------------------

function getProductData($prod_id)
{
   global $_HCG_GLOBAL;
      
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
      
   $query1 = "SELECT * FROM pr_product ".
             "WHERE ProductID = ".$prod_id;
   $product = $db->GetRow($query1);
   
   $product['error'] = 0;
   $product['error_msg'] = "";
   
   if ($product['SiteID'] != $_HCG_GLOBAL['site_id'])
   {
      $product['error'] = 1;
      $product['error_msg'] .= "The requested product is not part of this brand: " . $product['SiteID'] ." != " . $_HCG_GLOBAL['site_id'];
   }
   
   if ($product['Status'] == "discontinued")
   {
      $product['error'] = 1;
      $product['error_msg'] .= "This product has been discontinued. ";
   }   
   
   if (!empty($product['KosherSymbol']))
   {
      $kosher_sym = getSymbolData($prod_id, "KosherSymbol");
      $product['KosherFile'] = $kosher_sym['SymbolFile'];
      $product['KosherWidth'] = $kosher_sym['SymbolWidth'];
      $product['KosherHeight'] = $kosher_sym['SymbolHeight'];
      $product['KosherAlt'] = $kosher_sym['SymbolAlt'];
   }

   if (!empty($product['OrganicSymbol']))
   {
      $organic_sym = getSymbolData($prod_id, "OrganicSymbol");
      $product['OrganicFile'] = $organic_sym['SymbolFile'];
      $product['OrganicWidth'] = $organic_sym['SymbolWidth'];
      $product['OrganicHeight'] = $organic_sym['SymbolHeight'];
      $product['OrganicAlt'] = $organic_sym['SymbolAlt'];
   }
   
   $product['UPC12'] = getFullUPC($product['UPC']);

   return $product;
}


// ------------------------------------------------------------------------
// getCategoryData()
//   returns the category data given a category number.
//   (used to create back link in HV)
//
// ------------------------------------------------------------------------

function getCategoryData($cat_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT * FROM pr_category " .
      "WHERE CategoryID = ".$cat_id;
   $category = $db->GetRow($query1);
      
   return $category;
}


// ------------------------------------------------------------------------
// getFirstCategory()
//   returns the first category found for a particular ProductID.
//
// ------------------------------------------------------------------------

function getFirstCategory($prod_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT CategoryID FROM pr_product_category " .
      "WHERE ProductID = ".$prod_id;
   $cat_rec = $db->GetRow($query1);
      
   $query2 = "SELECT * FROM pr_category " .
      "WHERE CategoryID = ".$cat_rec['CategoryID'];
   $category = $db->GetRow($query2);
   
   return $category;
}


// ------------------------------------------------------------------------
// getFirstCategoryID()
//   returns the first category found for a particular ProductID.
//
// ------------------------------------------------------------------------

function getFirstCategoryID($prod_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT CategoryID FROM pr_product_category " .
      "WHERE ProductID = ".$prod_id;
   $cat_rec = $db->GetRow($query1);
      
   return $cat_rec['CategoryID'];
}


// ------------------------------------------------------------------------
// getAllCategories()
//   returns the category data for all categories assigned to ProductID.
//
// ------------------------------------------------------------------------

function getAllCategories($prod_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT CategoryID FROM pr_product_category " .
      "WHERE ProductID = ".$prod_id;
   $cats = $db->GetAll($query1);
   
   for ($i=0; $i<count($cats); $i++) {
      $query2 = "SELECT * FROM pr_category " .
         "WHERE CategoryID = ".$cats[$i]['CategoryID'];
      $categories[$i] = $db->GetRow($query2);
   }
   
   return $categories;
}


// ------------------------------------------------------------------------
// getCategoryIDFromCode()
//   returns the CategoryID for a given code. This is to allow for
//   backward compatibility with sites that use the code.
//   May want to make a tag (used to create back link in HV)
//
// ------------------------------------------------------------------------

function getCategoryIDFromCode($cat_code)
{
   global $_HCG_GLOBAL;
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT CategoryID FROM pr_category " .
      "WHERE CategoryCode LIKE \"".$cat_code."\" " .
      "AND SiteID LIKE \"".$_HCG_GLOBAL['site_id']."\"";
   $cat_rec = $db->GetRow($query1);
      
   return $cat_rec['CategoryID'];
}


// ------------------------------------------------------------------------
// getProdIDsInCategory()
//   returns a list of ProductIDs for all products in a category, 
//   excluding products with a status of "discontinued" or "pending".
//
//   It also sorts by ProductName.
//
// ------------------------------------------------------------------------

function getProdIDsInCategory($cat_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = 'SELECT pr_product_category.ProductID '.
      'FROM pr_product_category, pr_product '.
      'WHERE pr_product_category.CategoryID LIKE "'.$cat_id.'" '.
      'AND pr_product_category.ProductID = pr_product.ProductID '.
      'AND pr_product.Status NOT LIKE "discontinued" '.
      'AND pr_product.Status NOT LIKE "pending" '.
      'AND pr_product.Status NOT LIKE "inactive" '.
      'AND (pr_product.ProductGroup LIKE "none" '.
      'OR pr_product.ProductGroup LIKE "master") '.
      'ORDER BY pr_product.ProductName ASC';   
   $raw_list = $db->GetAll($query1);
   
   for ($i=0; $i<count($raw_list); $i++) {
      $id_list[$i] = $raw_list[$i]['ProductID'];
   }
   
//   print_r($id_list);
   
   return $id_list;
}


// ------------------------------------------------------------------------
// getProdIDsInSite()
//   returns a list of ProductIDs for all products in $site_id.
//
// ------------------------------------------------------------------------

function getProdIDsInSite($site_id = "default")
{
   global $_HCG_GLOBAL;

   if ($site_id = "default") {
      $site_id = $_HCG_GLOBAL['site_id'];
   }

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT ProductID FROM pr_product " .
      "WHERE SiteID LIKE \"".$site_id."\"";
   $raw_list = $db->GetAll($query1);
   
   for ($i=0; $i<count($raw_list); $i++) {
      $id_list[$i] = $raw_list[$i]['ProductID'];
   }
   
   //print_r($id_list);
   
   return $id_list;
}


// ------------------------------------------------------------------------
// getProdIDsPerField()
//   returns a list of ProductIDs for all products assigned to $site_id 
//   that have $bool_field set to 1. Since the field is specified as a 
//   variable, it makes this useful for all boolean fields.
//
// ------------------------------------------------------------------------
function getProdIDsPerField($bool_field, $site_id)
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query1 = "SELECT ProductID FROM pr_product " .
      "WHERE ".$bool_field." = 1 " .
      "AND SiteID LIKE \"".$site_id."\"";
   $raw_list = $db->GetAll($query1);
   
   for ($i=0; $i<count($raw_list); $i++) {
      $id_list[$i] = $raw_list[$i]['ProductID'];
   }
   
//   print_r($id_list);
   
   return $id_list;
}


// ------------------------------------------------------------------------
// getCategoryChildren()
//   returns a list of the category IDs of the children of $cat_id, 
//   including $cat_id itself.
//
// ------------------------------------------------------------------------
function getCategoryChildren($cat_id, $level)
{
   global $children;
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   if ($level == 0) {
      $query = 'SELECT * FROM pr_category '.
               'WHERE CategoryID = '.$cat_id;
      $result = $db->GetAll($query);
      $catRec = array_merge($result[0], array('Level' => $level));
      $children[] = $catRec;
      if ($catRec['Status'] != 1) {
         return false;
      }
   }
   
   // retrieve all chilren of the parent
   $query = 'SELECT * FROM pr_category '.
            'WHERE CategoryParentID = '.$cat_id.' '.
            'AND Status = 1 '.
            'ORDER BY CategoryOrder';
   $result = $db->GetAll($query);
   
   for ($i=0; $i<count($result); $i++) {
      $catRec = array_merge($result[$i], array('Level' => $level+1));
      $children[] = $catRec;
      getCategoryChildren($catRec['CategoryID'], $level+1);
   }
   return $children;
}


// ------------------------------------------------------------------------
// getFullUPC()
//   takes an 11-digit UPC and returns the full 12-digit code as a string
//   in the form 0-00000-00000-0
//
// ------------------------------------------------------------------------
function getFullUPC($upc_eleven) 
{
	$upc_eleven_str = (string)$upc_eleven;
	
	$full_upc = substr($upc_eleven_str, 0, 1) . "-" .
				substr($upc_eleven_str, 1, 5) . "-" .
				substr($upc_eleven_str, 6, 5) . "-" .
				calculateCheckDigit($upc_eleven);

	return $full_upc;
}


// ------------------------------------------------------------------------
// calculateCheckDigit()
//   takes an 11-digit UPC and calulates the check digit.
//
// ------------------------------------------------------------------------

function calculateCheckDigit($upc_eleven)
{
    if ($upc_eleven == '')
       return false;
       
	$upc = $upc_eleven;
	
	// 1) add digits 1, 3, 5, 7, 9, 11
	$step1 = $upc[0] + $upc[2] + $upc[4] + $upc[6] + $upc[8] + $upc[10];

	// 2) multiply result by 3
	$step2 = $step1 * 3;

	// 3) add digits 2, 4, 6, 8, 10
	$step3 = $upc[1] + $upc[3] + $upc[5] + $upc[7] + $upc[9];

	// 4) add result to previous result
	$step4 = $step2 + $step3;

	// The Check Digit is the smallest number needed to round the result 
	// of Step 4 up to a multiple of 10.
	
	$check_digit = (10 - ($step4 % 10));
	if ($check_digit == 10) {
	   $check_digit = 0;
	}
	
	return $check_digit;
}


// ------------------------------------------------------------------------
// getImageData()
//
// ------------------------------------------------------------------------

function getImageData($prod_id, $imageSize) 
{
   require_once("mod_db/db.fns.php");
   db_connect("hcg_public");
   db_SetFetchMode(ADODB_FETCH_ASSOC);
   
   $query = "SELECT images.ImageFile, images.ImageWidth, images.ImageHeight, images.ImageAlt " .
      "FROM products, images " .
      "WHERE products.ProductID = $prod_id " . 
      "AND products.$imageSize = images.ImageID";
   $result = db_GetRow($query);

   return $result;
}


// ------------------------------------------------------------------------
// getSymbolData()
//
// ------------------------------------------------------------------------

function getSymbolData($prod_id, $symbol) 
{
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query = "SELECT pr_symbol.SymbolFile, pr_symbol.SymbolWidth, pr_symbol.SymbolHeight, pr_symbol.SymbolAlt " .
      "FROM pr_product, pr_symbol " .
      "WHERE pr_product.ProductID = ".$prod_id." ".
      "AND pr_product.".$symbol." = pr_symbol.SymbolID";
   $result = $db->GetRow($query);

   return $result;
}


// ------------------------------------------------------------------------
//  build_stmt1()
//   used by Nutrition Facts. Turns the coded string of characters into a 
//   sentence. It builds the string in the same order as the characters 
//   are entered; if we need to enforce a particular order, we can add the
//   ability to sort the string elements.
//
// ------------------------------------------------------------------------

function build_stmt1($stmt_one)
{
   $stmt1_data = array(
      'a' => 'saturated fat',
      'b' => 'cholesterol',
      'c' => 'dietary fiber',
      'd' => 'sugars',
      'e' => 'vitamin A',
      'f' => 'vitamin C',
      'g' => 'calcium',
      'h' => 'iron',
      'i' => 'protein',
      'j' => 'calories from fat',
      'k' => 'trans fat',
   );
   
   $statement = "Not a significant source of ";
   
   if (strlen($stmt_one) > 1) {
      for ($i=0, $j=strlen($stmt_one); $i<$j-1; $i++) {
         $statement .= $stmt1_data[$stmt_one[$i]] . ", ";
      }
      $statement .= "or " . $stmt1_data[$stmt_one[$i]] . ".";
   } else {
      $statement .= $stmt1_data[$stmt_one] . ".";
   }

   return $statement;
}


// ------------------------------------------------------------------------
//  draw_line()
//   used by Nutrition Facts. This is a function used in the default 
//   templates that draws a line of a specific width, indented or not. 
//   It's made available to the template using the "register_function" 
//   method in Smarty.
//
//   Parameters:
//      width="number"      the thickness of the line
//      indented="yes|no"   whether the line is indented
//      class="classname"   assigns class="classname" to the td tag
//      xhtml="yes|no"      adds a closing slash to the image tag
//
// ------------------------------------------------------------------------

function draw_line($params)
{
   extract($params);
   if (strtoupper($indent) == "YES") {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td class="'.$class.'">';
      } else {
         $html_data .= '<td>';
      }
      $html_data .= '<img src="/images/dot_clear.gif" width="11" height="1" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      if ($class != "") {
         $html_data .= '<td colspan="7" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="7">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="219" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   } else {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td colspan="8" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="8">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="232" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   echo $html_data;
}


// ------------------------------------------------------------------------
//  draw_line_wide()
//   used by Nutrition Facts. This is a function used in the default 
//   templates that draws a line of a specific width, indented or not. It's 
//   made available to the template using the "register_function" method in 
//   Smarty. The wide in the name indicates that it spans 9 columns rather 
//   than the 8 the other does.
//
//   Parameters:
//      width="number"      the thickness of the line
//      indented="yes|no"   whether the line is indented
//      class="classname"   assigns class="classname" to the td tag
//      xhtml="yes|no"      adds a closing slash to the image tag
//
// ------------------------------------------------------------------------

function draw_line_wide($params)
{
   extract($params);
   if (strtoupper($indent) == "YES") {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td class="'.$class.'">';
      } else {
         $html_data .= '<td>';
      }
      $html_data .= '<img src="/images/dot_clear.gif" width="11" height="1" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      if ($class != "") {
         $html_data .= '<td colspan="8" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="8">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="219" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   } else {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td colspan="9" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="9">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="232" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   echo $html_data;
}

// ------------------------------------------------------------------------
//  draw_baby_line()
//   used by Nutrition Facts. This is a function used in the default 
//   templates that draws a line of a specific width, indented or not. 
//   It's made available to the template using the "register_function" 
//   method in Smarty. The baby in the name indicates that it spans 3 
//   columns rather than the 8 the other does.
//
//   Parameters:
//      width="number"      the thickness of the line
//      indented="yes|no"   whether the line is indented
//      class="classname"   assigns class="classname" to the td tag
//      xhtml="yes|no"      adds a closing slash to the image tag
//
// ------------------------------------------------------------------------

function draw_baby_line($params)
{
   extract($params);
   if (strtoupper($indent) == "YES") {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td class="'.$class.'">';
      } else {
         $html_data .= '<td>';
      }
      $html_data .= '<img src="/images/dot_clear.gif" width="11" height="1" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      if ($class != "") {
         $html_data .= '<td colspan="2" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="2">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="157" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   } else {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td colspan="3" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="3">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="170" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   echo $html_data;
}

// ------------------------------------------------------------------------
//  draw_baby_line2()
//   used by Nutrition Facts. This is a function used in the default 
//   templates that draws a line of a specific width, indented or not. 
//   It's made available to the template using the "register_function" 
//   method in Smarty. The baby in the name indicates that it spans 3 
//   columns rather than the 8 the other does.
//
//   This version is for the slightly newer version of the baby food template
//
//   Parameters:
//      width="number"      the thickness of the line
//      indented="yes|no"   whether the line is indented
//      class="classname"   assigns class="classname" to the td tag
//      xhtml="yes|no"      adds a closing slash to the image tag
//
// ------------------------------------------------------------------------

function draw_baby_line2($params)
{
   extract($params);
   if (strtoupper($indent) == "YES") {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td class="'.$class.'">';
      } else {
         $html_data .= '<td>';
      }
      $html_data .= '<img src="/images/dot_clear.gif" width="11" height="1" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      if ($class != "") {
         $html_data .= '<td colspan="3" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="3">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="187" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   } else {
      $html_data = '<tr>' . "\n";
      if ($class != "") {
         $html_data .= '<td colspan="4" class="'.$class.'">';
      } else {
         $html_data .= '<td colspan="4">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="200" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES") {
         $html_data .= ' /></td>' . "\n";
      } else {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   echo $html_data;
}



?>