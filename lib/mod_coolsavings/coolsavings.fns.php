<?php

// =========================================================================
// mailform.fns.php
// written by Jim Applegate
//
// =========================================================================

if ( ! defined('DEBUG'))
{
   define('DEBUG', 0);
}

require_once 'dbi_adodb.inc.php';
require_once 'template.class.php';
require_once 'HTML/QuickForm.php';


//-------------------------------------------------------------------------
// TAG: coolsavings_form
//   This is specifically to create a way for users to sign up to receive
//   online coupons from Cool Savings. The idea is to require them to give 
//   us a valid email so we can limit the number of people getting the 
//   coupons. Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function coolsavings_form($tpl = "coolsavings_mail.tpl")
{
   global $_HCG_GLOBAL;

   require_once 'HTML/QuickForm.php';

   $siteid = $_HCG_GLOBAL['site_id'];
   $source = (isset($_HCG_GLOBAL['passed_vars']['source'])) ? $_HCG_GLOBAL['passed_vars']['source'] : '';

   $form_html = "";
   $display_response = "form";

   $form = new HTML_QuickForm('coupon', null, null, null, null, true);

   $form->addElement('text', 'FName', 'First Name:', array('size' => 25, 'maxlength' => 25));
   $form->addElement('text', 'LName', 'Last Name:', array('size' => 25, 'maxlength' => 25));
   $form->addElement('text', 'Email', 'Email:', array('size' => 30, 'maxlength' => 255));
   $form->addElement('text', 'Email2', 'Please Confirm Your Email:', array('size' => 30, 'maxlength' => 255));
   $form->addElement('hidden', 'Source', $source);
   $form->addElement('submit', 'Submit', 'Continue');
   $form->addElement('html', '<tr><td width="120"><img src="/images/dot_clear.gif" width="120" height="1" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td></tr>');

   $form->applyFilter('FName', 'trim');
   $form->applyFilter('LName', 'trim');
   $form->applyFilter('Email', 'trim');
   $form->applyFilter('Email2', 'trim');

   $form->addRule('FName', 'You must enter your last name.', 'required', null, 'client');
   $form->addRule('LName', 'You must enter your last name.', 'required', null, 'client');
   $form->addRule('Email', 'You must enter your email address.', 'required', null, 'client');
   $form->addRule('Email', 'This does not appear to be a valid email address.', 'email', null, 'client');
   $form->addRule('Email2', 'You must confirm your email address.', 'required', null, 'client');
   $form->addRule(array('Email','Email2'), 'The two email fields must match.', 'compare', null, 'client');
   
   if ($form->validate()) {
      $result = processcoolsavings_form($form, $tpl);
      $display_response = $result;
   } else {
      $renderer =& $form->defaultRenderer();
      $form->accept($renderer);
      $form_html = $renderer->toHtml();
   }

   // end form code 

   $results[0] = $display_response;
   $results[1] = $form_html;

   return $results;
}



//-------------------------------------------------------------------------
// processcoolsavings_form
//   used to process the form data from 'coupon' Quickform; 
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processcoolsavings_form($form, $tpl)

{
   global $_HCG_GLOBAL;
  
   $values = $form->exportValues();

   if ($values['Source'] == "") {
      $values['Source'] = "ebsite";
   }
   $values['FName'] = urlencode($values['FName']);
   $values['LName'] = urlencode($values['LName']);
   $values['md5email'] = md5(strtolower($values['Email']));
   
   return $values;
}


?>