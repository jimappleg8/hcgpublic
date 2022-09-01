<?php

// ========================================================================
// pr_populate.inc.php
// 
// part of the Products Module.
//
// ========================================================================


// ------------------------------------------------------------------------
// adm_populate_pr_product()
//  build pr_product table using old tables
//
// ------------------------------------------------------------------------

function adm_populate_pr_products(&$old_db, &$new_db)
{
   global $_HCG_GLOBAL;
   
   include($_HCG_GLOBAL['forms_dir']."/pr_product.php");

   $new_db->query("DROP TABLE pr_product");
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }
   // to come: generate product build query
   $new_db->query($TABLE_CREATE_STATEMENT);
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }
   
   $products = $old_db->query("SELECT * FROM products", "GetAll");

   if ($old_db->isError()) {
      echo "products: ".$old_db->getError()."<br><br>";
      $old_db->resetError();
   } else {
   
      foreach ($products as $product) {
   
         if (empty($product['FilterID'])) {
            $product['FilterID'] = 0; 
         }
         if (($product['Status'] == "") || ($product['Status'] == "In Production")) {
            $product['Status'] = "active";
         }
         $product['Status'] = strtolower($product['Status']);
         
         $product['SiteID'] = $product['SiteId'];
   
         //print_r($product);
         if (!empty($product['ThumbnailImage'])) {
            $thumb_image = adm_getImageData($old_db, $product['ThumbnailImage']);
            $product['ThumbFile'] = $thumb_image['ImageFile'];
            $product['ThumbWidth'] = $thumb_image['ImageWidth'];
            $product['ThumbHeight'] = $thumb_image['ImageHeight'];
            $product['ThumbAlt'] = $thumb_image['ImageAlt'];
         } else {
            $product['ThumbWidth'] = 0;
            $product['ThumbHeight'] = 0;
         }

         if (!empty($product['SmallImage'])) {
            $small_image = adm_getImageData($old_db, $product['SmallImage']);
            $product['SmallFile'] = $small_image['ImageFile'];
            $product['SmallWidth'] = $small_image['ImageWidth'];
            $product['SmallHeight'] = $small_image['ImageHeight'];
            $product['SmallAlt'] = $small_image['ImageAlt'];
         } else {
            $product['SmallWidth'] = $small_image['ImageWidth'];
            $product['SmallHeight'] = $small_image['ImageHeight'];
         }

         if (!empty($product['LargeImage'])) {
            $large_image = adm_getImageData($old_db, $product['LargeImage']);
            $product['LargeFile'] = $large_image['ImageFile'];
            $product['LargeWidth'] = $large_image['ImageWidth'];
            $product['LargeHeight'] = $large_image['ImageHeight'];
            $product['LargeAlt'] = $large_image['ImageAlt'];
         } else {
            $product['LargeWidth'] = 0;
            $product['LargeHeight'] = 0;
         }

         if (!empty($product['CaffeineImage'])) {
            $caffeine_image = adm_getImageData($old_db, $product['CaffeineImage']);
            $product['CaffeineFile'] = $caffeine_image['ImageFile'];
            $product['CaffeineWidth'] = $caffeine_image['ImageWidth'];
            $product['CaffeineHeight'] = $caffeine_image['ImageHeight'];
            $product['CaffeineAlt'] = $caffeine_image['ImageAlt'];
         } else {
            $product['CaffeineWidth'] = 0;
            $product['CaffeineHeight'] = 0;
         }

         $query2 = adm_buildInsert($product, $FORM_SAVE_ARRAY, "pr_product");
         $new_db->query($query2);	      
         if ($new_db->isError()) {
            echo "products2: ".$new_db->getError()."<br><br>";
            $new_db->resetError();
            echo $query2."<br><br>";
         }
      }
   }
   
   echo "Step 1 Complete<br><br>";
}


// ------------------------------------------------------------------------
// adm_populate_pr_site()
//  transfer sites table to pr_site
//
// ------------------------------------------------------------------------

function adm_populate_pr_site(&$old_db, &$new_db)
{
   global $_HCG_GLOBAL;

   include($_HCG_GLOBAL['forms_dir']."/site.php");

   $new_db->query("DROP TABLE site");
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }
   $new_db->query($TABLE_CREATE_STATEMENT);
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }
   
   $sites = $old_db->query("SELECT * FROM sites", "GetAll");

   if ($old_db->isError()) {
      echo "sites: ".$old_db->getError()."<br><br>";
      $old_db->resetError();
   } else {
   
      foreach ($sites as $site) {
         $query3 = adm_buildInsert($site, $FORM_SAVE_ARRAY, "site");
         $new_db->query($query3);	      
      }
   }

   echo "Step 2 Complete<br><br>";
}


// ------------------------------------------------------------------------
// adm_populate_pr_symbol()
//  transfer symbols table to pr_symbol
//
// ------------------------------------------------------------------------

function adm_populate_pr_symbol(&$old_db, &$new_db)
{
   global $_HCG_GLOBAL;

   include($_HCG_GLOBAL['forms_dir']."/pr_symbol.php");

   $new_db->query("DROP TABLE pr_symbol");
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }
   $new_db->query($TABLE_CREATE_STATEMENT);
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }

   $symbols = $old_db->query("SELECT * FROM symbols", "GetAll");
      
   if ($old_db->isError()) {
      echo "symbols: ".$old_db->getError()."<br><br>";
      $old_db->resetError();
   } else {

      foreach ($symbols as $symbol) {
         $query4 = adm_buildInsert($symbol, $FORM_SAVE_ARRAY, "pr_symbol");
         $new_db->query($query4);	      
      }
   }
   
   echo "Step 3 Complete<br><br>";
}


// ------------------------------------------------------------------------
// adm_populate_pr_category()
//  build pr_category table from categories table
//
// ------------------------------------------------------------------------

function adm_populate_pr_category(&$old_db, &$new_db)
{
   global $_HCG_GLOBAL;

   include($_HCG_GLOBAL['forms_dir']."/pr_category.php");

   $new_db->query("DROP TABLE pr_category");
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }
   $new_db->query($TABLE_CREATE_STATEMENT);
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }

   $cats = $old_db->query("SELECT * FROM categories", "GetAll");

   if ($old_db->isError()) {
      echo "cats1: ".$old_db->getError()."<br><br>";
      $old_db->resetError();
   } else {
   
      $categories = array();
   
      foreach ($cats as $cat) {
         $query5 = "SELECT SiteID FROM products ".
                   "WHERE products.ProductID = ".$cat['ProductID'];
         $result = $old_db->query($query5, "GetRow");
         if ($old_db->isError()) {
            echo "cats2: ".$old_db->getError()."<br><br>";
            $old_db->resetError();
            echo $query5."<br><br>";
         }
      
         $unique = true;
         foreach ($categories as $category) {
            if ($category['CategoryCode'] == $cat['Category']) {
               if ($category['SiteID'] == $result['SiteID']) {
                  $unique = false;
               }
            }
         }
         if ($unique == true) {
            $next = count($categories);
            $categories[$next]['SiteID'] = $result['SiteID'];
            $categories[$next]['CategoryCode'] = $cat['Category'];
            $categories[$next]['CategoryName'] = ucwords(str_replace("_", " ", $cat['Category']));
            $categories[$next]['CategoryDescription'] = "";
            $categories[$next]['CategoryType'] = "attribute";
            $categories[$next]['Status'] = 1;
         }
      }
   
      foreach ($categories as $category) {
         $query6 = adm_buildInsert($category, $FORM_SAVE_ARRAY, "pr_category");
         $new_db->query($query6);	      
         if ($new_db->isError()) {
            echo "cats3: ".$new_db->getError()."<br><br>";
            $new_db->resetError();
            echo $query6."<br><br>";
         }
      }
   }
   
   echo "Step 4 Complete<br><br>";
}


// ------------------------------------------------------------------------
// adm_populate_pr_product_category()
//  build pr_product_category table
//
// ------------------------------------------------------------------------

function adm_populate_pr_product_category(&$old_db, &$new_db)
{
   global $_HCG_GLOBAL;

   $new_db->query("DROP TABLE pr_product_category");
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }
   
   $TABLE_CREATE_STATEMENT = "create table pr_product_category (".
      "ProductID int(11) unsigned not null, ".
      "CategoryID int(11) not null".
   ")";

   $new_db->query($TABLE_CREATE_STATEMENT);
   if ($new_db->isError()) {
      echo $new_db->getError()."<br><br>";
      $new_db->resetError();
   }

   $cats = $old_db->query("SELECT * FROM categories", "GetAll");

   if ($old_db->isError()) {
      echo "prodcats1: ".$old_db->getError()."<br><br>";
      $old_db->resetError();
   } else {
      
      foreach ($cats as $cat) {

         $query7 = "SELECT SiteID FROM products ".
                   "WHERE products.ProductID = ".$cat['ProductID'];
         $result = $old_db->query($query7, "GetRow");
         if ($old_db->isError()) {
            echo "prodcats2: ".$old_db->getError()."<br><br>";
            $old_db->resetError();
            echo $query7."<br><br>";
         }

         $query8 = "SELECT CategoryID FROM pr_category ".
                   "WHERE CategoryCode LIKE '".$cat['Category']."' ".
                   "AND SiteID LIKE '".$result['SiteID']."'";
         $result2 = $new_db->query($query8, "GetRow");
         if ($new_db->isError()) {
            echo "prodcats3: ".$new_db->getError()."<br><br>";
            $new_db->resetError();
            echo $query8."<br><br>";
         }
         
         $query9 = "INSERT INTO pr_product_category ".
                   "(ProductID,CategoryID) ".
                   "VALUES (".$cat['ProductID'].",".$result2['CategoryID'].")";
         $new_db->query($query9);
         if ($new_db->isError()) {
            echo "prodcats4: ".$new_db->getError()."<br><br>";
            $new_db->resetError();
            echo $query9."<br><br>";
         }
      }
   }

   echo "Step 5 Complete<br><br>";
}


// ------------------------------------------------------------------------
// adm_getImageData()
//
// ------------------------------------------------------------------------

function adm_getImageData(&$dbi, $image_id) 
{
   $dbi->query(ADODB_FETCH_ASSOC, "SetFetchMode");
   
   $query = "SELECT ImageFile, ImageWidth, ImageHeight, ImageAlt " .
      "FROM images " .
      "WHERE ImageID = ".$image_id;
   $result = $dbi->query($query, "GetRow");

   return $result;
}


//--------------------------------------------------------------------
// adm_buildInsert()
//
//--------------------------------------------------------------------

function adm_buildInsert($data, $types, $table)
{
   $fields = array();
   $values = array();
   foreach ($types as $field => $type) {
      array_push($fields, $field);
      // If data type is text then quote(addslashes())
      if (defined($data[$field])) {
         if (!strcmp($type, 'text')) {
            array_push($values, "'".addslashes($data[$field])."'");
         } else {
            array_push($values, $data[$field]);
         }
      }
   }
   // Now build the SQL INSERT statement.
   $fieldList = implode(',', $fields);
   $valueList = implode(',', $values);
   $stmt = sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fieldList, $valueList); 

   return $stmt;
}


?>