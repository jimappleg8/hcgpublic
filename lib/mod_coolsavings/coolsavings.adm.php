<?php

// =========================================================================
// mailform.fns.php
// written by Jim Applegate
//
// =========================================================================

define("DEBUG", 0);

require_once 'dbi_adodb.inc.php';
require_once 'template.class.php';
require_once 'HTML/QuickForm.php';


//-------------------------------------------------------------------------
// TAG: coolsavings_nomail
//   This is specifically to create a way for users to sign up to receive
//   online coupons from Cool Savings. The idea is to require them to give 
//   us a valid email so we can limit the number of people getting the 
//   coupons. Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function coolsavings_nomail($tpl = "adm/coolsavings_nomail.tpl")
{
   global $_HCG_GLOBAL;

   require_once 'HTML/QuickForm.php';

   $siteid = $_HCG_GLOBAL['site_id'];

   $form_html = "";
   $display_response = "form";

   $form = new HTML_QuickForm('coupon', null, null, null, null, true);

   $form->addElement('text', 'FName', 'First Name:', array('size' => 25, 'maxlength' => 25));
   $form->addElement('text', 'LName', 'Last Name:', array('size' => 25, 'maxlength' => 25));
   $form->addElement('text', 'Email', 'Email:', array('size' => 30, 'maxlength' => 255));
   $form->addElement('hidden', 'Source', $_HCG_GLOBAL['passed_vars']['source']);
   $form->addElement('submit', 'Submit', 'Send Me the Link');
   $form->addElement('html', '<tr><td width="120"><img src="/images/dot_clear.gif" width="120" height="1" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td></tr>');

   $form->applyFilter('FName', 'trim');
   $form->applyFilter('LName', 'trim');
   $form->applyFilter('Email', 'trim');

   $form->addRule('FName', 'You must enter your last name.', 'required', null, 'client');
   $form->addRule('LName', 'You must enter your last name.', 'required', null, 'client');
   $form->addRule('Email', 'You must enter your email address.', 'required', null, 'client');
   $form->addRule('Email', 'This does not appear to be a valid email address.', 'email', null, 'client');
   
   if ($form->validate()) {
      $result = processcoolsavings_nomail($form, $tpl);
   } else {
      $renderer =& $form->defaultRenderer();
      $form->accept($renderer);
      $results = $renderer->toHtml();
      echo $results;
   }
}



//-------------------------------------------------------------------------
// processcoolsavings_nomail
//   used to process the form data from 'coupon' Quickform; 
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processcoolsavings_nomail($form, $tpl)

{
   global $_HCG_GLOBAL;
  
   $values = $form->exportValues();
  
   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }
   
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   
   // check to see if email is already in database
   $query = "SELECT * FROM coolsavings ".
            "WHERE Email LIKE \"".md5(strtolower($values['Email']))."\"";
   $check = $db->GetAll($query);
   if (count($check) < 1) {
      echo "This person is not in the database.";
   }

   if ($values['Source'] == "") {
      $values['Source'] = "ebsite";
   }
   $values['FName'] = urlencode($values['FName']);
   $values['LName'] = urlencode($values['LName']);
   $values['md5email'] = md5(strtolower($values['Email']));
   
   // build reply to user & display
   $m = new HCG_Smarty;
   $m->assign("mail", $values);
   $m->setTplPath($tpl);
   $mail_content2 = $m->fetch($tpl);
   
   echo "<pre>";
   echo $mail_content2;
   echo "</pre>";
}


?>