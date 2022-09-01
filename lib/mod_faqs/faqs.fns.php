<?php

// =========================================================================
// faqs.fns.php
// written by Jim Applegate
//
// =========================================================================


require_once("template.class.php");
require_once("dbi_adodb.inc.php");
require_once("mod_core/core.inc.php");


// ------------------------------------------------------------------------
// TAG: faq_list
//   Supplies information to the template that allows the template designer
//   to create either links to individual FAQ pages or a complete list
//   of FAQs with anchor links for the same page.
//
// ------------------------------------------------------------------------

function faq_list($faqlist, $tpl = "default", $lang = "") 
{
   global $_HCG_GLOBAL;
   
   if ($tpl == "default") {
      $tpl = "faqs_list_".$faqlist.".tpl";
   }

   // get faq data

   $query = "SELECT * FROM faqs " . 
            "WHERE faqlist LIKE \"".$faqlist."\" ";
   if ($lang != "")
   {
      $query .= "AND Language = \"".$lang."\" ";
   }
   $query .= "AND status = 1 " .
             "ORDER BY position";

   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $faqs = $db->GetAll($query);

   $num_faqs = count($faqs);

   for ($i=0; $i<count($faqs); $i++) {

      $faqs[$i]['title'] = stripslashes($faqs[$i]['title']);
      $faqs[$i]['shortquestion'] = stripslashes($faqs[$i]['shortquestion']);
      $faqs[$i]['question'] = stripslashes($faqs[$i]['question']);
      $faqs[$i]['answer'] = stripslashes($faqs[$i]['answer']);
      $faqs[$i]['status'] = stripslashes($faqs[$i]['status']);
      $faqs[$i]['datecreated'] = stripslashes($faqs[$i]['datecreated']);
      $faqs[$i]['lastmodified'] = stripslashes($faqs[$i]['lastmodified']);

      // some faqs may not have htmlentities coverted in database
      // so decode them all in case they are
      $faqs[$i]['title'] = html_entity_decode($faqs[$i]['title'], ENT_QUOTES);
      $faqs[$i]['shortquestion'] = html_entity_decode($faqs[$i]['shortquestion'], ENT_QUOTES);
      $faqs[$i]['question'] = html_entity_decode($faqs[$i]['question'], ENT_QUOTES);
      $faqs[$i]['answer'] = html_entity_decode($faqs[$i]['answer'], ENT_QUOTES);
      // then re-encode them
      $faqs[$i]['title'] = htmlentities($faqs[$i]['title'], ENT_QUOTES);
      $faqs[$i]['shortquestion'] = htmlentities($faqs[$i]['shortquestion'], ENT_QUOTES);
      $faqs[$i]['question'] = htmlentities($faqs[$i]['question'], ENT_QUOTES);
      //if autoformat is off, don't re-encode the answer.
      if ($faqs[$i]['autoformat'] == 1) {
         $faqs[$i]['answer'] = htmlentities($faqs[$i]['answer'], ENT_QUOTES);
      }

      $allowed_html = array("ALL");
      $faqs[$i]['question'] = text_to_html($faqs[$i]['question'], $allowed_html);

      if ($faqs[$i]['autoformat'] == 1) {
         $faqs[$i]['answer'] = text_to_html($faqs[$i]['answer'], $allowed_html);
      }
   }

   $t = new HCG_Smarty;

   $t->assign("faqs", $faqs);
   $t->assign("num_faqs", $num_faqs);

   // templates need to include faqlist name
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


// ------------------------------------------------------------------------
// TAG: faq_detail
//   template needs to handle case where status is 0 or 2. In those cases
//   the faq is no longer available.
//
// ------------------------------------------------------------------------

function faq_detail($faq_num, $tpl = "default") 
{
   $query = "SELECT * FROM faqs " . 
            "WHERE faqid = ".$faq_num;

   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $faqs = $db->GetRow($query);
   
   $faqlist = $faqs['faqlist'];
   $faqs['title'] = stripslashes($faqs['title']);
   $faqs['shortquestion'] = stripslashes($faqs['shortquestion']);
   $faqs['question'] = stripslashes($faqs['question']);
   $faqs['answer'] = stripslashes($faqs['answer']);
   $faqs['status'] = stripslashes($faqs['status']);
   $faqs['datecreated'] = stripslashes($faqs['datecreated']);
   $faqs['lastmodified'] = stripslashes($faqs['lastmodified']);
   
   // some faqs may not have htmlentities coverted in database
   // so decode them all in case they are
   $faqs['title'] = html_entity_decode($faqs['title'], ENT_QUOTES);
   $faqs['shortquestion'] = html_entity_decode($faqs['shortquestion'], ENT_QUOTES);
   $faqs['question'] = html_entity_decode($faqs['question'], ENT_QUOTES);
   $faqs['answer'] = html_entity_decode($faqs['answer'], ENT_QUOTES);
   // then re-encode them
   $faqs['title'] = htmlentities($faqs['title'], ENT_QUOTES);
   $faqs['shortquestion'] = htmlentities($faqs['shortquestion'], ENT_QUOTES);
   $faqs['question'] = htmlentities($faqs['question'], ENT_QUOTES);
   //if autoformat is off, don't re-encode the answer.
   if ($faqs['autoformat'] == 1) {
      $faqs['answer'] = htmlentities($faqs['answer'], ENT_QUOTES);
   }

   $allowed_html = array("ALL");
   $faqs['question'] = text_to_html($faqs['question'], $allowed_html);

   if ($faqs['autoformat'] == 1) {
      $faqs['answer'] = text_to_html($faqs['answer'], $allowed_html);
   }
   
   if ($tpl == "default") {
      $tpl = "faqs_detail_".$faqlist.".tpl";
   }

   $t = new HCG_Smarty;

   $t->assign("faqs", $faqs);

   // templates need to include faqlist name
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);   

}


?>
