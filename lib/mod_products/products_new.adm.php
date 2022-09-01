<?php

require_once 'mod_products/products.inc.php';
require_once 'dbi_adodb.inc.php';
require_once 'template.class.php';


// ------------------------------------------------------------------------
// TAG: adm_manage_products
//   This is the controller for all administrative functions. It calls
//   other functions based on the $action supplied. The default action
//   is to display the list of products with links to modify them.
//
// ------------------------------------------------------------------------

function adm_manage_products($site_id, $action = "display", $prod_num = "", $lastaction="", $sort="") 
{
   global $_HCG_GLOBAL;
   
   $display_list = true;
   
   if ($action == "display") {
      $result = 1;
   } elseif ($action == "cats") {
      $result = adm_assign_categories($site_id, $prod_num);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   //} elseif ($action == "toggle") {
   //   $result = adm_change_job_status($job_num, $lastaction);
   //} elseif ($action == "delete") {
   //   $result = adm_trash_job($job_num);
   //} elseif ($action == "create") {
   //   $result = adm_create_job($site_id);
   //   if ($result == "in_progress") {
   //      $display_list = false;
   //      $result = 1;
   //   }
   } elseif ($action == "edit") {
      $result = adm_edit_product($site_id, $prod_num);
      if ($result == "in_progress") {
         $display_list = false;
         $result = 1;
      }
   }
   
   if ($result != 1) {
      $products['error_msg'] = $result;
   }
   
   if ($display_list == true) {
   
      $products['siteid'] = $site_id;

      $query = "SELECT * FROM pr_product " .
               "WHERE SiteID LIKE \"".$site_id."\" ";
      if ($sort == "") {
         $query .= "ORDER BY Status DESC, UPC ASC";
      } else {
         $query .= "ORDER BY ".$sort;
      }

      $db = HCGNewConnection('hcg_public');
      $db->SetFetchMode(ADODB_FETCH_ASSOC);
      $product_list = $db->GetAll($query);
      
      $num_prods = count($product_list);
      if ($num_prods == 0) {
         $products['product_exists'] = false;
      } else {
         $products['product_exists'] = true;
      }
      
      $t = new HCG_Smarty;

      $t->assign("products", $products);
      $t->assign("product_list", $product_list);
      $t->assign("lastaction", $_SESSION['user_last_action'] + 1);
      $t->assign("sort", $sort);
	
      $t->setTplPath("products_adm_manageproducts.tpl");
      echo $t->fetch("products_adm_manageproducts.tpl");
   }
}





// ------------------------------------------------------------------------
// adm_edit_product()
//   This is to allow for edits to the main products table.
//
// ------------------------------------------------------------------------

function adm_edit_product($site_id, $prod_id, $sort)
{
   require_once 'HTML/QuickForm.php';
   
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query1 = "SELECT * from pr_product ".
            "WHERE ProductID = ".$prod_id;
   $prod_data = $db->GetAll($query1);


   $form = new HTML_QuickForm('products', null, null, null, null, true);

   $form->addElement('submit', 'Submit', 'Done');
   $form->addElement('text', 'ProductID', 'ProductID:', array('size' => 25, 'maxlength' => 25));
   $form->addElement('text', 'UPC', 'UPC:', array('size' => 15, 'maxlength' => 11));
   
   $query2 = "SELECT * from sites";
   $sites_list = $db->GetAll($query2);
   
   $form->addElement('text', 'SiteID', 'SiteID:', array('size' => 15, 'maxlength' => 2));
   $form->addElement('text', 'FilterID', 'FilterID:', array('size' => 15, 'maxlength' => 11));
   
   $status_list = array('discontinued', 'active', 'partial', 'inactive');
          
   $form->addElement('select', 'Status', 'Status:', $status_list);
   $form->addElement('text', 'Verified', 'Verified:', array('size' => 25, 'maxlength' => 128));
   $form->addElement('text', 'ProductName', 'ProductName:', array('size' => 44, 'maxlength' => 255));
   $form->addElement('textarea', 'LongDescription', 'LongDescription:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('text', 'Teaser', 'Teaser:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('textarea', 'Benefits', 'Benefits:', array('cols' => 44, 'rows' => 3, 'wrap' => "virtual"));
   $form->addElement('text', 'AvailableIn', 'AvailableIn:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('textarea', 'Footnotes', 'Footnotes:', array('cols' => 44, 'rows' => 2, 'wrap' => "virtual"));
   $form->addElement('textarea', 'Ingredients', 'Ingredients:', array('cols' => 44, 'rows' => 6, 'wrap' => "virtual"));
   $form->addElement('textarea', 'NutritionBlend', 'NutritionBlend:', array('cols' => 44, 'rows' => 6, 'wrap' => "virtual"));
   $form->addElement('text', 'Standardization', 'Standardization:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('textarea', 'Directions', 'Directions:', array('cols' => 44, 'rows' => 4, 'wrap' => "virtual"));
   $form->addElement('textarea', 'Warning', 'Warning:', array('cols' => 44, 'rows' => 4, 'wrap' => "virtual"));
   $form->addElement('textarea', 'AllNatural', 'AllNatural:', array('cols' => 44, 'rows' => 2, 'wrap' => "virtual"));
   $form->addElement('text', 'Gluten', 'Gluten:', array('size' => 25, 'maxlength' => 128));
   $form->addElement('textarea', 'OrganicStatement', 'OrganicStatement:', array('cols' => 44, 'rows' => 2, 'wrap' => "virtual"));
   $form->addElement('text', 'ThumbFile', 'ThumbFile:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'ThumbWidth', 'ThumbWidth:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'ThumbHeight', 'ThumbHeight:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'ThumbAlt', 'ThumbAlt:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'SmallFile', 'SmallFile:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'SmallWidth', 'SmallWidth:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'SmallHeight', 'SmallHeight:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'SmallAlt', 'SmallAlt:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'LargeFile', 'LargeFile:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'LargeWidth', 'LargeWidth:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'LargeHeight', 'LargeHeight:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'LargeAlt', 'LargeAlt:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'NutritionFacts', 'NutritionFacts:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'KosherSymbol', 'KosherSymbol:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'OrganicSymbol', 'OrganicSymbol:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'CaffeineFile', 'CaffeineFile:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'CaffeineWidth', 'CaffeineWidth:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'CaffeineHeight', 'CaffeineHeight:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'CaffeineAlt', 'CaffeineAlt:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'StoreSection', 'StoreSection:', array('size' => 25, 'maxlength' => 11));
   $form->addElement('text', 'LocatorCode', 'LocatorCode:', array('size' => 25, 'maxlength' => 10));
   $form->addElement('text', 'MenuSubsection', 'MenuSubsection:', array('size' => 25, 'maxlength' => 60));
   $form->addElement('text', 'DiscontinueDate', 'DiscontinueDate:', array('size' => 10, 'maxlength' => 11));
   $form->addElement('textarea', 'Replacements', 'Replacements:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('textarea', 'Explanation', 'Explanation:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('text', 'LastModifiedDate', 'LastModifiedDate:', array('size' => 10, 'maxlength' => 11));
   $form->addElement('text', 'LastModifiedBy', 'LastModifiedBy:', array('size' => 25, 'maxlength' => 60));
   $form->addElement('textarea', 'MetaMisc', 'MetaMisc:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('textarea', 'MetaDescription', 'MetaDescription:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('textarea', 'MetaKeywords', 'MetaKeywords:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('text', 'Components', 'Components:', array('size' => 25, 'maxlength' => 11));
   $form->addElement('text', 'ProductType', 'ProductType:', array('size' => 25, 'maxlength' => 20));
   $form->addElement('textarea', 'FlavorDescriptor', 'FlavorDescriptor:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('text', 'SortOrder', 'SortOrder:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'FlagAsNew', 'FlagAsNew:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'Featured', 'Featured:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'SpiceLevel', 'SpiceLevel:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('textarea', 'Alergens', 'Alergens:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
   $form->addElement('text', 'FeatureFile', 'FeatureFile:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'FeatureWidth', 'FeatureWidth:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'FeatureHeight', 'FeatureHeight:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'FeatureAlt', 'FeatureAlt:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'BeautyFile', 'BeautyFile:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'BeautyWidth', 'BeautyWidth:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'BeautyHeight', 'BeautyHeight:', array('size' => 6, 'maxlength' => 11));
   $form->addElement('text', 'BeautyAlt', 'BeautyAlt:', array('size' => 25, 'maxlength' => 255));
   $form->addElement('text', 'PackageSize', 'PackageSize:', array('size' => 25, 'maxlength' => 127));
   $form->addElement('text', 'ProductGroup', 'ProductGroup:', array('size' => 25, 'maxlength' => 127));
   $form->addElement('text', 'Language', 'Language:', array('size' => 25, 'maxlength' => 15));

   $form->addElement('hidden', 'site', $site_id);
   $form->addElement('hidden', 'prod_id', $prod_id);
   $form->addElement('hidden', 'action', 'edit');

   $form->addElement('submit', 'Submit', 'Done');


   if ($form->validate()) {
      return 1;
//      $form->process('process_data', false);
   } else {
      $form->setDefaults($prod_data[0]);
//      $form->freeze();
      $renderer =& $form->defaultRenderer();
      $form->accept($renderer);
      $form_html = $renderer->toHtml();
      echo $form_html;
      return "in_progress";
   }
}


// ------------------------------------------------------------------------
// adm_assign_categories()
//   This is to make it easier to assign catagories to products. It is one
//   part of what I hope will be a complete product CMS.
//
// ------------------------------------------------------------------------

function adm_assign_categories($site_id, $prod_id)
{
   global $_HCG_GLOBAL;
   
   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

   // get product data for $prod_id
   $prod_data = getProductData($prod_id);

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
      
   // get list of catagories for this site
   $query1 = "SELECT CategoryID, CategoryName, CategoryParentID ".
             "FROM pr_category ".
             "WHERE SiteID LIKE \"".$prod_data['SiteID']."\" ".
             "ORDER BY CategoryOrder";
   $cat_list = $db->GetAll($query1);
   
   // add a "level" field to the records. 
   // This assumes that there are no more than 3 levels
   for ($i=0; $i<count($cat_list); $i++) {
      $tmp_cat_list[$cat_list[$i]['CategoryID']] = $cat_list[$i];
      $tmp_cat_list[$cat_list[$i]['CategoryID']]['key'] = $i;
   }
   foreach ($tmp_cat_list as $key => $data) {
      if ($tmp_cat_list[$key]['CategoryParentID'] == "0") {
         $cat_list[$tmp_cat_list[$key]['key']]['level'] = 1;
      } else {
         $parent = $tmp_cat_list[$key]['CategoryParentID'];
         if ($tmp_cat_list[$parent]['CategoryParentID'] == "0") {
            $cat_list[$tmp_cat_list[$key]['key']]['level'] = 2;
         } else {
            $parent = $tmp_cat_list[$parent]['CategoryParentID'];
            if ($tmp_cat_list[$parent]['CategoryParentID'] == "0") {
               $cat_list[$tmp_cat_list[$key]['key']]['level'] = 3;
            }
         }
      }
   }
   
//   echo "<pre>"; print_r($cat_list); echo "</pre>";
   
   // get list categories currently assigned to this product
   $query2 = "SELECT CategoryID FROM pr_product_category ".
             "WHERE ProductID = ".$prod_data['ProductID'];
   $cat_data = $db->GetAll($query2);
   
   // form settings
   $form_html = "";
   $display_response = false;

   $form = new HTML_QuickForm('categories', null, null, null, null, true);
   
   for ($i=0; $i<count($cat_list); $i++) {
      
      // determine if item should be checked
      $check = "";
      for($j=0; $j<count($cat_data); $j++) {
         if ($cat_data[$j]['CategoryID'] == $cat_list[$i]['CategoryID']) {
            $check = array('checked' => 'yes');
            $cat_list[$i]['checked'] = "YES";
         }
      }
      if ($cat_list[$i]['checked'] != "YES") {
         $cat_list[$i]['checked'] = "NO";
      }
      
      $form->addElement('checkbox', $cat_list[$i]['CategoryID'], $spacer, ' '.$cat_list[$i]['CategoryName']." (".$cat_list[$i]['CategoryID'].")", $check);

   }

   $form->addElement('hidden', 'source', base64_encode(serialize($cat_list)));
   $form->addElement('hidden', 'site', $site_id);
   $form->addElement('hidden', 'prod_id', $prod_id);
   $form->addElement('hidden', 'action', 'cats');

   $buttons[] = &HTML_QuickForm::createElement('submit', 'btnCancel', 'Cancel');
   $buttons[] = &HTML_QuickForm::createElement('submit', 'btnSubmit', 'Submit');
   $form->addGroup($buttons, null, null, '&nbsp;');

   if ($form->validate()) {

      $form->process('process_categories', false);
      return 1;

   } else {

      $tpl = new HCG_Smarty;
      // prepare the renderer for Smarty
      $renderer = &new HTML_QuickForm_Renderer_ArraySmarty($tpl);

      $required_template =
         '{if $error}<span class="requiredErrLabel">
             {$label}</span>
          {else}
             {if $required}
                <span class="required">*</span>&nbsp;
             {/if}
             {$label}
          {/if}';
      $error_template = 
         '{$html}
          {if $error}
             <span class="errorMsg">{$error}</span>
          {/if}';
      $renderer->setRequiredTemplate($required_template);
      $renderer->setErrorTemplate($error_template);

      $form->accept($renderer);
      $form_data = $renderer->toArray();
//      echo "<pre>"; print_r($form_data); echo "</pre>";
      $tpl->assign('form_data', $form_data);
      $tpl->assign('prod_data', $prod_data);
      $tpl->assign('cat_list', $cat_list);

      // process the template for display
      $tpl->setTplPath("pr_adm_assign_cats.tpl");   
      $form_html = $tpl->fetch("pr_adm_assign_cats.tpl");
      echo $form_html;
      return "in_progress";
   }
}

function process_categories($values)
{
   echo "<pre>"; print_r($values); echo "</pre>";
   if ($values['btnSubmit'] == "Submit") {

      $db = HCGNewConnection('hcg_public');
      $db->SetFetchMode(ADODB_FETCH_ASSOC);

      $source = unserialize(base64_decode($values['source']));
      
      for($i=0; $i<count($source); $i++) {
         if ($source[$i]['checked'] == "YES") {
            if (empty($values['cat'.$source[$i]['CategoryID']])) {
               $query = "DELETE FROM pr_product_category ".
                        "WHERE CategoryID = ".$source[$i]['CategoryID']." ".
                        "AND ProductID = ".$values['prod_id'];
               $db->Execute($query);
               echo $source[$i]['CategoryName']." has been unchecked.<br>";
           }
         } else { // it equals "NO"
            if (!empty($values['cat'.$source[$i]['CategoryID']])) {
               $query = "INSERT INTO pr_product_category ".
                        "VALUES (".$values['prod_id'].",".$source[$i]['CategoryID'].")";
               $db->Execute($query);
               echo $source[$i]['CategoryName']." has been checked.<br>";
            }
         }
      }
   }
}


// ------------------------------------------------------------------------
// adm_export_prod()
//   This exports the brands product data to a CVS file that will work
//   with the IRI Store Locator system. This function generates the data
//   for the prod.cvs file.
//
//   This file has two parts, a UPC section and a Custom section:
//
//   The UPC section has five columns
//      upc (10 digits)
//      $site_id." ".ProductName
//      UPC (literal)
//      HNCL (literal)
//      69 (literal)
//
//   The Custom section also has five columns
//      LocatorCode
//      $site_id." ".ProductName
//      Custom (literal)
//      HNCL (literal)
//      69 (literal)
//
// ------------------------------------------------------------------------

function adm_export_prod($site_id)
{
   if ($site_id == "all") {
      $sites = array("am","cs","eb","ge","hf","hs","hv","if","tc","td","wb");
      $filename = "prod.csv";
   } else {
      $sites = array($site_id);
      $filename = $site_id."_prod.csv";
   }
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   header("Content-Type: text/plain");
   header("Content-Disposition: attachment; filename=".$filename);

   $eol = "\n";
   
   foreach ($sites as $site) {

      $query1 = "SELECT UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND LocatorCode NOT LIKE 'none'";
      $prod_data = $db->GetAll($query1);
   
      // generate UPC records
      for($i=0; $i<count($prod_data); $i++) {
         if ($prod_data[$i]['ProductGroup'] != "master") {
            $line = "";
            $line .= substr($prod_data[$i]['UPC'], 1).",";
            $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
            $line .= "UPC,HNCL,69".$eol;
            echo $line;
         }
      }

      // generate Custom "master" records
      for($i=0; $i<count($prod_data); $i++) {
         if ($prod_data[$i]['ProductGroup'] == "master") {
            $line = "";
            $line .= $prod_data[$i]['LocatorCode'].",";
            $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
            $line .= "Custom,HNCL,69".$eol;
            echo $line;
         }
      }

      // generate Custom "none" records
      for($i=0; $i<count($prod_data); $i++) {
         if ($prod_data[$i]['ProductGroup'] == "none") {
            $line = "";
            $line .= $prod_data[$i]['LocatorCode'].",";
            $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
            $line .= "Custom,HNCL,69".$eol;
            echo $line;
         }
      }
   }
}


// ------------------------------------------------------------------------
// adm_export_prod_rel()
//   This exports the brands product data to a CVS file that will work
//   with the IRI Store Locator system. This function generates the data
//   for the prod_rel.cvs file.
//
//   This file is pretty simple, just four columns:
//     upc (10 digits)
//     HNCL (literal)
//     69 (literal)
//     LocatorCode
//
// ------------------------------------------------------------------------

function adm_export_prod_rel($site_id)
{
   if ($site_id == "all") {
      $sites = array("am","cs","eb","ge","hf","hs","hv","if","tc","td","wb");
      $filename = "prod_rel.csv";
   } else {
      $sites = array($site_id);
      $filename = $site_id."_prod_rel.csv";
   }

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   header("Content-Type: text/plain");
   header("Content-Disposition: attachment; filename=".$filename);

   $eol = "\n";
   
   foreach ($sites as $site) {

      $query1 = "SELECT UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND ProductGroup NOT LIKE 'master' ".
                "AND LocatorCode NOT LIKE 'none'";
      $prod_data = $db->GetAll($query1);
   
      // generate records
      for($i=0; $i<count($prod_data); $i++) {
         $line = "";
         $line .= substr($prod_data[$i]['UPC'], 1).",";
         $line .= "HNCL,69,";
         $line .= $prod_data[$i]['LocatorCode'].$eol;
         echo $line;
      }
   }
}


// ------------------------------------------------------------------------
// adm_export_upc_69()
//   This exports the brands product data to a CVS file that will work
//   with the IRI Store Locator system. This function generates the data
//   for the upc_69.cvs file.
//
//   This file is very simple, just one column:
//     upc (10 digits)
//
// ------------------------------------------------------------------------

function adm_export_upc_69($site_id)
{
   if ($site_id == "all") {
      $sites = array("am","cs","eb","ge","hf","hs","hv","if","tc","td","wb");
      $filename = "upc_69.csv";
   } else {
      $sites = array($site_id);
      $filename = $site_id."_upc_69.csv";
   }

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   header("Content-Type: text/plain");
   header("Content-Disposition: attachment; filename=".$filename);

   $eol = "\n";
   
   foreach ($sites as $site) {

      $query1 = "SELECT UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND ProductGroup NOT LIKE 'master' ".
                "AND LocatorCode NOT LIKE 'none'";
      $prod_data = $db->GetAll($query1);
   
      // generate records
      for($i=0; $i<count($prod_data); $i++) {
         $line = "";
         $line .= substr($prod_data[$i]['UPC'], 1).$eol;
         echo $line;
      }
   }
}


// ------------------------------------------------------------------------
// adm_export_iri_report()
//   This generates a report of all the products being supported by IRI
//   for the store locator.
//
// ------------------------------------------------------------------------

function adm_export_iri_report($site_id)
{
   if ($site_id == "all") {
      $sites = array("am","cs","eb","ge","hf","hs","hv","if","tc","td","wb");
      $filename = "iri_report.csv";
   } else {
      $sites = array($site_id);
      $filename = $site_id."_iri_report.csv";
   }
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   header("Content-Type: text/plain");
   header("Content-Disposition: attachment; filename=".$filename);

   $eol = "\n";

   $count1['total'] = 0;
   $count2['total'] = 0;
   
   foreach ($sites as $site) {
   
      $query1 = "SELECT BrandName from site ".
                "WHERE SiteID LIKE '".$site."'";
      $brand = $db->GetRow($query1);
   
      $brandname[$site] = $brand['BrandName'];

      $query2 = "SELECT ProductID, UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND LocatorCode NOT LIKE 'none'";
      $prod_data = $db->GetAll($query2);
   
      // generate UPC records
      $count1[$site] = 0;
      $line1[$site] = "";
      for($i=0; $i<count($prod_data); $i++) {
         if ($prod_data[$i]['ProductGroup'] != "master") {
            $line1[$site] .= substr($prod_data[$i]['UPC'], 1).",";
            if ($prod_data[$i]['ProductGroup'] != "none") {
               $line1[$site] .= "\"* ";
            } else {
               $line1[$site] .= "\"";
            }
            $line1[$site] .= str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
            $line1[$site] .= $eol;
            $count1[$site]++;
         }
      }

      // generate Custom "master" records
      $count2[$site] = 0;
      $line2[$site] = "";
      for($i=0; $i<count($prod_data); $i++) {
         if ($prod_data[$i]['ProductGroup'] == "master") {
            $line2[$site] .= $prod_data[$i]['LocatorCode'].",";
            $line2[$site] .= "\"".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
            $line2[$site] .= $eol;
            $line2[$site] .= "\"(Group containing these products: ";
            for($j=0; $j<count($prod_data); $j++) {
               if ($prod_data[$j]['ProductGroup'] == $prod_data[$i]['ProductID']) {
                  $line2[$site] .= substr($prod_data[$j]['UPC'], 1)." ";
               }
            }
            $line2[$site] .= ")\"".$eol;
            $count2[$site]++;
         }
      }

      // generate Custom "none" records
      $line3[$site] = "";
      for($i=0; $i<count($prod_data); $i++) {
         if ($prod_data[$i]['ProductGroup'] == "none") {
            $line3[$site] .= $prod_data[$i]['LocatorCode'].",";
            $line3[$site] .= "\"".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
            $line3[$site] .= $eol;
            $count2[$site]++;
         }
      }
      
      $count1['total'] = $count1['total'] + $count1[$site];
      $count2['total'] = $count2['total'] + $count2[$site];
   }
   
   echo "IRI Store Locator Product Report".$eol;
   echo "Generated ".date('Y-m-d').$eol;
   echo $eol;
   echo "Summary,Individual UPCs,Product Groups".$eol;
   foreach ($sites as $site) {
      echo $brandname[$site].":,".$count1[$site].",".$count2[$site].$eol;   
   }
   echo "TOTAL:,".$count1['total'].",".$count2['total'].$eol;
   echo $eol;
   echo $eol;   
   
   foreach ($sites as $site) {

      echo $brandname[$site].$eol;
      echo $eol;
      echo "Individual UPCs: ".$count1[$site].$eol;
      echo "Product Groups: ".$count2[$site].$eol;
      echo $eol;
      echo $brandname[$site]." Individual UPCs:".$eol;
      echo "*part of a multi-sku product group".$eol;
      echo $eol;
      echo $line1[$site];
      echo $eol;
      echo $brandname[$site]." Product Groups:".$eol;
      echo $eol;
      echo $line2[$site];
      echo $line3[$site];
      echo $eol;
      echo $eol;
   }
}


?>