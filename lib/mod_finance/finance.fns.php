<?php 

// =========================================================================
//  finance.fns.php
//  written by Jim Applegate
//  last modified: 
// =========================================================================

require_once("template.class.php");


// ------------------------------------------------------------------------
// TAG: stock_quote
//   stock_list is an array in the form 
//   array('HAIN' => 'Hain Celestial Group');
//
// ------------------------------------------------------------------------
function stock_quote($stock_list, $tpl="stock_quote.tpl")
{
   global $_HCG_GLOBAL;

   require_once($_HCG_GLOBAL['stocks_dir'] . "/stocks.php");

   $stocks = new stocks();

   $result = $stocks->get_quotes($stock_list);
   
   //print_r($result);

   $t = new HCG_Smarty;

   $t->assign("stock", $result);
	
   $t->setTplPath($tpl);
   return $t->fetch($tpl);

}

// ------------------------------------------------------------------------
// TAG: hcg_stock_quote
//
// ------------------------------------------------------------------------
function hcg_stock_quote($tpl="stock_quote.tpl")
{
   global $_HCG_GLOBAL;

   $got_data = TRUE;
   
   // first see if the scraping program is working
   if (file_exists($_HCG_GLOBAL['lib_dir']."/mod_finance/stock_data.php"))
   {
      include($_HCG_GLOBAL['lib_dir']."/mod_finance/stock_data.php");
   }
   
   // make sure we got the actual data
   if ( ! isset($stock))
   {
      $got_data = FALSE;
      $stock[0]['price_last'] = 'empty';
   }

   if ( ! is_numeric($stock[0]['price_last']) || $got_data == FALSE)
   {
      // We supply blank data to avoid error messages
      $stock[0]["symbol"] = "HAIN"; 
      $stock[0]["name"] = "The Hain Celestial Group, Inc.";
      $stock[0]["date"] = date("M. d, Y");
      $stock[0]["time"] = date("H:i");
      $stock[0]["status"] = "Unavailable";
      $stock[0]["price_last"] = "0";
      $stock[0]["pchange"] = "0";
      $stock[0]["dchangeu"] = "0";
      $stock[0]["direction"] = "unchanged";
      $stock[0]["volume"] = "0";
      $stock[0]["previous"] = "0";
      $stock[0]["price_max"] = "0";
      $stock[0]["price_min"] = "0";
      $stock[0]["best_bid"] = "0";
      $stock[0]["best_ask"] = "0";
   }
      
   $t = new HCG_Smarty;

   $t->assign("stock", $stock);
	
   $t->setTplPath($tpl);
   return $t->fetch($tpl);

}
   


?>
