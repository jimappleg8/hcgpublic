<?php

// =========================================================================
// recipes.fns.php
// written by Jim Applegate
//
// =========================================================================

require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';


function recipe_detail($recipe_id)
{
   $query = "SELECT * FROM recipes " . 
            "WHERE RecipeID = \"".$recipe_id."\" ".
            "AND Active = 1";

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $recipe = $db->GetRow($query);
   
//   echo "<pre>"; print_r($recipe); echo "</pre>";
   
   str_replace("\r", " ", $recipe['Ingredients']);
   str_replace("", " ", $recipe['Ingredients']);

   $t = new HCG_Smarty;
   
   $t->assign("recipe", $recipe);
	
   $t->setTplPath("recipe_detail.tpl");
   echo $t->fetch("recipe_detail.tpl");   

}

function recipe_list($cat_name, $tpl = "recipe_list.tpl")
{
   global $_HCG_GLOBAL;
   
   $query = 'SELECT * FROM recipes ' . 
            'WHERE Category LIKE "'.$cat_name.'" '.
            'AND SiteID = "'.$_HCG_GLOBAL['site_id'].'" '.
            'AND Active = 1';

   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $recipe = $db->GetAll($query);
   
   $num_recipes = count($recipe);

   foreach ($recipe as $data) {
      str_replace("\r", " ", $data['Ingredients']);
      str_replace("", " ", $data['Ingredients']);
   }
//   echo "<pre>"; print_r($recipe); echo "</pre>";
   
   $t = new HCG_Smarty;
   
   $t->assign("recipe", $recipe);
   $t->assign("num_recipes", $num_recipes);
   $t->assign("category", $cat_name);
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);   

}



?>
