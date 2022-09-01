<?php

// =========================================================================
// postcards.fns.php
// written by Jim Applegate
//
// =========================================================================


require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';


// ------------------------------------------------------------------------
// classicPostcardForm
//
//  This is designed to emulate the existing postcards on the CS site.
//
// ------------------------------------------------------------------------

function classicPostcardForm($art_id, $tpl="postcard_form.tpl")
{
   global $_HCG_GLOBAL;
   
   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';
   
   $form_html = "";
   $postcard['display_response'] = false;
   
   $db = HCGNewConnection('hcg_public');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
      
   $query1 = "SELECT QuoteID FROM pc_artwork_quote ".
             "WHERE ArtworkID = ".$art_id;

   $quote_list = $db->GetAll($query1);
   
   $query2 = "SELECT * FROM quote ";
   for ($i=0; $i<count($quote_list); $i++) {
      if ($i == 0) {
         $query2 .= "WHERE QuoteID = ".$quote_list[$i]['QuoteID'];
      } else {
         $query2 .= " OR QuoteID = ".$quote_list[$i]['QuoteID'];
      }
   }
         
   $quotes = $db->GetAll($query2);
   $letters = array("A","B","C","D","E","F","G","H","I");
   
   $form = new HTML_QuickForm('postcard', null, null, null, null, true);
   
   for ($i=0; $i<count($quotes); $i++) {
      $quotes[$i]['letter'] = $letters[$i];
      $form->addElement('radio', 'QuoteID', 'Choose a Quote:', '</td><td>'.$quotes[$i]['letter'] . '. "'.$quotes[$i]['Quotation'] . '"<div align="right"><b><i>&mdash;' . $quotes[$i]['Author'].'</i></b></div>', $quotes[$i]['QuoteID'], array());
   }
   $form->addElement('text', 'ToName', 'Recipient\'s Name:', array('size' => 25, 'maxlength' => 127, 'style' =>'width: 225px'));
   $form->addElement('text', 'ToEmail', 'Recipient\'s Email:', array('size' => 25, 'maxlength' => 127, 'style' =>'width: 225px'));
   $form->addElement('text', 'FromName', 'Your Name:', array('size' => 25, 'maxlength' => 127, 'style' =>'width: 225px'));
   $form->addElement('text', 'FromEmail', 'Your Email:', array('size' => 25, 'maxlength' => 127, 'style' =>'width: 225px'));
   $form->addElement('textarea', 'Message', 'Message:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual", 'style' =>'width: 325px'));
   $form->addElement('hidden', 'ArtworkID', $art_id);
   $form->addElement('submit', 'Submit', 'Send Postcard', 'class="buttonsubmitlarge"');

   $form->applyFilter('ToName', 'trim');
   $form->applyFilter('ToEmail', 'trim');
   $form->applyFilter('FromName', 'trim');
   $form->applyFilter('FromEmail', 'trim');
   $form->applyFilter('Message', 'trim');

   $form->addRule('QuoteID', 'You must choose a quote.', 'required', null, 'client');
   $form->addRule('ToName', 'You must enter the recipient\'s name.', 'required', null, 'client');
   $form->addRule('ToEmail', 'You must enter the recipient\'s email address.', 'required', null, 'client');
   $form->addRule('FromName', 'You must enter the your name.', 'required', null, 'client');
   $form->addRule('FromEmail', 'You must enter the your email address.', 'required', null, 'client');
   $form->addRule('Message', 'You must enter a message.', 'required', null, 'client');

   $t = new HCG_Smarty;

   if ($form->validate())
   {
      $form->freeze();
      processPostcardForm($form);
      $postcard['display_response'] = true;
   }
   else
   {
      // prepare the renderer for Smarty
      $renderer = &new HTML_QuickForm_Renderer_ArraySmarty($t);

      $required_template =
         '{if $error}<span class="red">
             {$label}</span>
          {else}
             {if $required}
                <span class="red">*</span>
             {/if}
             {$label}
          {/if}';
      $error_template = 
         '{$html}
          {if $error}
             <span class="red">{$error}</span>
          {/if}';
      $renderer->setRequiredTemplate($required_template);
      $renderer->setErrorTemplate($error_template);

      // assign all variables to the template
      $form->accept($renderer);
      $form_data = $renderer->toArray();
//      echo "<pre>"; print_r($form_data); echo "</pre>"; 

      $query3 = "SELECT * FROM artwork WHERE ArtworkID = ".$art_id;
      $artwork = $db->GetRow($query3);
   }

   $t->assign("postcard", $postcard);
   $t->assign("artwork", $artwork);
   $t->assign("quotes", $quotes);
   $t->assign("form_data", $form_data);
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}

//-------------------------------------------------------------------------
// TAG: processPostcardForm
//   used to process the form data from 'contact' Quickform; 
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processPostcardForm($form)

{
   global $_HCG_GLOBAL;
  
// For some reason, this doesn't include the QuoteID value, so
// I've changed it to the line below.
//   $values = $form->exportValues();
   $values = $_HCG_GLOBAL['passed_vars'];
   
   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }
   
   // calculate PostcardKey
   $PostcardKey = time();

   $values['PostcardKey'] = $PostcardKey;

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO pc_postcard ".
            "(PostcardKey, Message, ToName, ToEmail, FromName, FromEmail, QuoteID, ArtworkID, SiteID, DateSent) ".
            "VALUES ".
            "(\"".$PostcardKey."\", ".
            "\"".$slash_values['Message']."\", ".
            "\"".$slash_values['ToName']."\", ".
            "\"".$slash_values['ToEmail']."\", ".
            "\"".$slash_values['FromName']."\", ".
            "\"".$slash_values['FromEmail']."\", ".
            "\"".$slash_values['QuoteID']."\", ".
            "\"".$slash_values['ArtworkID']."\", ".
            "\"".$_HCG_GLOBAL['site_id']."\", ".
            "\"".date("Y-m-d")."\")";
   $db->Execute($query);
   
   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   $m = new HCG_Smarty;
   $m->assign("mail", $values);
   $m->setTplPath("postcard_mail.tpl");
   $mail_content = $m->fetch("postcard_mail.tpl");
   
   $fd = popen($sendmail,"w");
   fputs($fd, stripslashes($mail_content)."\n");
   pclose($fd);
}


// ------------------------------------------------------------------------
// displayClassicPostcard
//
//  This is designed to emulate the existing postcards on the CS site.
//
// ------------------------------------------------------------------------

function displayClassicPostcard($key, $tpl="postcard_display.tpl")
{
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query1 = "SELECT * FROM pc_postcard ".
             "WHERE PostcardKey LIKE \"".$key."\"";
   $postcard = $db->GetRow($query1);
   
   if (count($postcard) == 0) {

      $error = "Sorry, this postcard was not found.";

   } else {
   
      $query2 = "SELECT * FROM quote ".
                "WHERE QuoteID = ".$postcard['QuoteID'];
      $quote = $db->GetRow($query2);

      $query3 = "SELECT * FROM artwork ".
                "WHERE ArtworkID = ".$postcard['ArtworkID'];
      $artwork = $db->GetRow($query3);
   }

   $t = new HCG_Smarty;

   $t->assign("artwork", $artwork);
   $t->assign("quote", $quote);
   $t->assign("postcard", $postcard);
   $t->assign("error", $error);
	
   $t->setTplPath($tpl);
   echo $t->fetch($tpl);
}


?>