<?php 

// =========================================================================
//  db.adm.php
//  written by Jim Applegate
//  last modified: 
// =========================================================================

   global $_HCG_GLOBAL; 
   require_once $_HCG_GLOBAL['classes_dir'].'/dbUtils/dbUtils.php';

// ------------------------------------------------------------------------
// dbUpload()
//
// ------------------------------------------------------------------------

function dbUpload($task)
{
   global $_HCG_GLOBAL;
   global $db;
   
   // build database data for dbUtils
   $db["cheetah"]["user"] = "root";
   $db["cheetah"]["pass"] = "kl33nex";
   $db["cheetah"]["host"] = "199.117.189.217";
   $db["cheetah"]["port"] = 3306;
   $db["cheetah"]["time"] = 10;

   $db["webdev1"]["user"] = "root";
   $db["webdev1"]["pass"] = "kl33nex";
   $db["webdev1"]["host"] = "199.117.190.185";
   $db["webdev1"]["port"] = 3306;
   $db["webdev1"]["time"] = 10;

   $db["retriever"]["user"] = "root";
   $db["retriever"]["pass"] = "kl33nex";
   $db["retriever"]["host"] = "208.35.60.218";
   $db["retriever"]["port"] = 3306;
   $db["retriever"]["time"] = 10;

   $db["eagles"]["user"] = "root";
   $db["eagles"]["pass"] = "kl33nex";
   $db["eagles"]["host"] = "mysql-master";
   $db["eagles"]["port"] = 3306;
   $db["eagles"]["time"] = 10;

   $db["dolphins"]["user"] = "root";
   $db["dolphins"]["pass"] = "kl33nex";
   $db["dolphins"]["host"] = "199.117.188.242";
   $db["dolphins"]["port"] = 3306;
   $db["dolphins"]["time"] = 10;

   switch ($task) {
   
   case "allprodsLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/[pr_*][site]/");
      if ($result == 1) {
         $result = "hcg_public products tables copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public products tables to Retriever.";
      }
      break;
      
   case "pr_productLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/pr_product$/");
      if ($result == 1) {
         $result = "hcg_public:pr_product table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:pr_product to Retriever.";
      }
      break;
      
   case "pr_categoryLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/pr_category/");
      if ($result == 1) {
         $result = "hcg_public:pr_category table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:pr_category to Retriever.";
      }
      break;
      
   case "pr_product_categoryLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/pr_product_category/");
      if ($result == 1) {
         $result = "hcg_public:pr_product_category table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:pr_product_category to Retriever.";
      }
      break;
      
   case "pr_nleaLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/pr_nlea/");
      if ($result == 1) {
         $result = "hcg_public:pr_nlea table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:pr_nlea to Retriever.";
      }
      break;
      
   case "pr_symbolLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/pr_symbol/");
      if ($result == 1) {
         $result = "hcg_public:pr_symbol table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:pr_symbol to Retriever.";
      }
      break;
      
   case "siteLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/site/");
      if ($result == 1) {
         $result = "hcg_public:site table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:site to Retriever.";
      }
      break;
         
   case "storesINTRANET":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/stores/");
      if ($result == 1) {
         $result = "hcg_public:stores table copied to Dolphins.";
      } else {
         $result = "Unable to copy hcg_public:stores to Dolphins.";
      }
      break;
      
   case "storesLIVE":
      $result = dbCopy("cheetah","retriever","/hcg_public/","/stores/");
      if ($result == 1) {
         $result = "hcg_public:stores table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:stores to Retriever.";
      }
      break;

   case "storesNEW":
      $result = dbCopy("cheetah","eagles","/hcg_public/","/stores/");
      if ($result == 1) {
         $result = "hcg_public:stores table copied to Eagles.";
      } else {
         $result = "Unable to copy hcg_public:stores to Eagles.";
      }
      break;
      
   case "artworkLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/artwork/");
      if ($result == 1) {
         $result = "hcg_public:artwork table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:artwork to Retriever.";
      }
      break;
      
   case "quoteLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/quote/");
      if ($result == 1) {
         $result = "hcg_public:quote table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:quote to Retriever.";
      }
      break;
      
   case "pc_artwork_quoteLIVE":
      $result = dbCopy("cheetah","dolphins","/hcg_public/","/pc_artwork_quote/");
      if ($result == 1) {
         $result = "hcg_public:pc_artwork_quote table copied to Retriever.";
      } else {
         $result = "Unable to copy hcg_public:pc_artwork_quote to Retriever.";
      }
      break;
      
   case "livedbsNEW":
      $result = dbCopy("retriever","eagles","/.*/","/.*/");
      if ($result == 1) {
         $result = "Retriever databases copied to Eagles.";
      } else {
         $result = "Unable to copy Retriever databases to Eagles.";
      }
      break;
            
   case "":
      $result = "Choose an action below.";

   }
   return $result;
}

?>
