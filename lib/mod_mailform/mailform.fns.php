<?php

// =========================================================================
// mailform.fns.php
// written by Jim Applegate
//
// =========================================================================

if ( ! defined('DEBUG'))
   define('DEBUG', 0);

require_once 'dbi_adodb.inc.php';
require_once 'formsprocessing.class.php';
require_once 'formsgeneration.inc.php';
require_once 'template.class.php';
require_once 'HTML/QuickForm.php';



//-------------------------------------------------------------------------
// TAG: mailform
//   Uses the FormsGeneration class written by Manuel Lemos.
//
//-------------------------------------------------------------------------

function mailform($settings)
{
   if (DEBUG) echo "Entered the mailform function.<br>";

   global $_HCG_GLOBAL;
   
   $form_sent = false;
   $errors = array();

   $f = new form_class();

   $f->NAME = $settings['form_name'];
   $f->METHOD = "POST";
   $f->ACTION = $_SERVER["PHP_SELF"];
   $f->debug = "OutputDebug";
   $f->ResubmitConfirmMessage = "You have already submitted this form and it has been received. Thank you.";

   include($_HCG_GLOBAL['forms_dir']."/".$settings['form_def_file']);

   for ($i=0; $i<count($FORM_FIELDS_ARRAY); $i++) {
      $f->AddInput($FORM_FIELDS_ARRAY[$i]);
   }

   $f->AddInput(array(
      "TYPE" => "hidden",
      "NAME" => "doit",
      "VALUE" => 1,
   ));

   $f->AddInput(array(
      "TYPE" => "hidden",
      "NAME" => "user_track",
      "VALUE" => 134764475,
   ));

   $f->LoadInputValues($f->WasSubmitted("doit"));

   $verify = array();

   if ($f->WasSubmitted("doit")) {

      if (DEBUG) echo "Form was submitted...<br>";

      if ($f->GetInputValue("user_track") <= $_SESSION['user_last_action']) {
         $process_form = false;
         $form_sent = true;
      } else {
         if (($error_message = $f->Validate($verify)) == "") {
            $process_form = true;
         } else {
            $process_form = false;
            foreach ($error_message as $field => $errormsg) {
               $errors[$field] = htmlentities($errormsg);
            }
         }
      }

   } else {

      $error_message = "";
      $process_form = false;
      $f->SetInputValue("user_track", $_SESSION['user_last_action']+1);

   }

   if ($process_form == true) {

      if (DEBUG) echo "Processing form...<br>";

      $_SESSION['user_last_action']++;

      if (DEBUG) echo "Opening connection to the database...<br>";
      if ($settings['form_database'] == "") {
         $settings['form_database'] = "hcg_public_master";
      }
      $dbi = HCGNewConnection($settings['form_database']);
      $dbi->SetFetchMode(ADODB_FETCH_ASSOC);
      if (DEBUG) echo "Database connection opened.<br><br>";
      $s = new forms_processing($dbi);
      $s->setConfigs($settings);

      $s->importFields($f);

      // simplify test when save_mail_to_db is defined in all instances
      if ($settings['save_mail_to_db'] || ($settings['form_mail'] != "")) {
         if (DEBUG) echo "Saving form to database...<br>";
         $s->insertData();
         if (DEBUG) echo "Form saved to database.<br><br>";
      }
      if ($settings['send_outbound_mail']) {
         $s->sendMail($settings['outbound_mail_template']);
      }
      if ($settings['send_inbound_mail']) {
         if (DEBUG) echo "Sending inbound mail...<br>";
         $s->sendMail($settings['inbound_mail_template']);
         if (DEBUG) echo "Inbound mail sent.<br>";
      }

//      if ($s->processForm()) {
         $form_sent = true;
//      } else {
//         // trigger an error
//         $_SESSION['user_last_action']--;
//         echo "There was a problem with processing the form. ";
//      }
      if (DEBUG) echo "Form processing complete...<br>";
   }

   // create template object for main form
   $t = new HCG_Smarty;

   if (($form_sent == true) && ($settings['show_thankyou_template'] != "")) {
      $t->setTplPath($settings['show_thankyou_template']);
      return $t->fetch($settings['show_thankyou_template']);
   } else {
      $t->assign_by_ref("form", $f);
      $t->assign("error_message", $error_message);
      $t->assign_by_ref("verify", $verify);
      $t->assign("errors", $errors);
      $t->assign("doit", $form_sent);
      $t->register_prefilter("smarty_prefilter_form");
      $t->setTplPath($settings['form_template']);
      $t->fetch($settings['form_template']);
      $t->unregister_prefilter("smarty_prefilter_form");
      return FormCaptureOutput($f, array("EndOfLine"=>"\n"));
   }
}


//-------------------------------------------------------------------------
// TAG: contactus_form
//   Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function contactus_form($mailtpl1, $mailtpl2, $mailtpl3 = '')
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';


// tag should allow for 2 emails to go out
//   usually 1 to user
//   usually 1 internal
//   get('mailform.contactus_form', "contactus_mail.tpl", "");
// potentially no emails could be sent, just save to database.

// use

$hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
$siteid = $_HCG_GLOBAL['site_id'];


//echo $hcg_site;
//echo $siteid;

$form_html = "";
$display_response = false;

$elem =& HTML_QuickForm::createElement('advcheckbox', 'Marketing', '', ' Would you like to receive information from '.$hcg_site.' in the future? If yes, leave this box checked. If you have any concerns, please read our <a href="/about_us/privacy.php">privacy policy</a>.', null, array("NO", "YES"));
$elem->setChecked(true);

$form = new HTML_QuickForm('contactus', null, null, null, null, true);

$form->addElement('text', 'FName', 'First Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'LName', 'Last Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'Address1', 'Address Line 1:', array('size' => 35, 'maxlength' => 40));
$form->addElement('text', 'Address2', 'Address Line 2:', array('size' => 35, 'maxlength' => 40));
$form->addElement('text', 'City', 'City:', array('size' => 30, 'maxlength' => 30));
$form->addElement('text', 'State', 'State:', array('size' => 4, 'maxlength' => 2));
$form->addElement('text', 'Country', 'Country:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'Zip', 'Zip/Postal Code:', array('size' => 10, 'maxlength' => 10));
$form->addElement('text', 'Phone', 'Daytime Phone:', array('size' => 14, 'maxlength' => 14));
$form->addElement('text', 'Email', 'Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('text', 'Email2', 'Please Confirm Your Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('textarea', 'Comment', 'Message:', array('cols' => 40, 'rows' => 10, 'wrap' => "virtual"));
$form->addElement($elem);
$form->addElement('advcheckbox', 'Release', '', ' From time to time, we select consumer comments to post on our web site. Please check this box if you would like your comments to be considered.', array('checked' => 'yes'), array("NO", "YES"));
$form->addElement('hidden', 'siteid', $siteid);
$form->addElement('submit', 'Submit', 'Send Message');


$form->applyFilter('FName', 'trim');
$form->applyFilter('LName', 'trim');
$form->applyFilter('Address1', 'trim');
$form->applyFilter('City', 'trim');
$form->applyFilter('State', 'trim');
$form->applyFilter('Country', 'trim');
$form->applyFilter('Zip', 'trim');
$form->applyFilter('Email', 'trim');
$form->applyFilter('Email2', 'trim');
$form->applyFilter('Comment', 'trim');

$form->addFormRule('vMaliciousAttack');

$form->addRule('FName', 'You must enter your first name.', 'required', null, 'client');
$form->addRule('FName', 'Too many characters.', 'maxlength', '25');
$form->addRule('FName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('LName', 'You must enter your last name.', 'required', null, 'client');
$form->addRule('LName', 'Too many characters.', 'maxlength', '25');
$form->addRule('LName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('Address1', 'You must enter your address.', 'required', null, 'client');
$form->addRule('City', 'You must enter your city.', 'required', null, 'client');
$form->addRule('State', 'You must enter your state.', 'required', null, 'client');
$form->addRule('Zip', 'You must enter your zip or postal code. If you do not have a postal code then type the word none.', 'required', null, 'client');
$form->addRule('Email', 'You must enter your email address.', 'required', null, 'client');
$form->addRule('Email', 'This does not appear to be a valid email address.', 'email', null, 'client');
$form->addRule('Email2', 'You must confirm your email address.', 'required', null, 'client');
$form->addRule(array('Email','Email2'), 'The two email fields must match.', 'compare', null, 'client');

if ($form->validate()) {
   $form->freeze();

//   echo "<pre>";
//   print_r($form);
//   echo "</pre>";

   //$form->process('processcontact_form', false);
   //$form->process('processcontact_form', false, $mailtpl1, $mailtpl2);
   processcontact_form($form, $mailtpl1, $mailtpl2, $mailtpl3);
   $display_response = true;

} else {

   // create a new template object
   $tpl = new HCG_Smarty;
   // prepare the renderer for Smarty
   $renderer = &new HTML_QuickForm_Renderer_ArraySmarty($tpl);

   $required_template =
         '{if $error}<span style="color:#F00">
             {$label}</span>
          {else}
             {$label}
             {if $required}
                <span style="color:#F00">*</span>
             {/if}
          {/if}';
   $error_template =
         '{$html}
          {if $error}
             <span style="color:#F00">{$error}</span>
          {/if}';
   $renderer->setRequiredTemplate($required_template);
   $renderer->setErrorTemplate($error_template);

   // assign all variables to the template
   $form->accept($renderer);
   $form_data = $renderer->toArray();
//   echo "<pre>"; print_r($form_data); echo "</pre>";
   $tpl->assign('form_data', $form_data);

   // process the template for display
   $tpl->setTplPath("contactus_form.tpl");
   $form_html = $tpl->fetch("contactus_form.tpl");

}


// end form code


   $results[0] = $display_response;
   $results[1] = $form_html;

   return $results;
}



//-------------------------------------------------------------------------
// TAG: processcontact_form
//   used to process the form data from 'contact' Quickform;
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processcontact_form($form, $mailtpl1, $mailtpl2, $mailtpl3)

{
   global $_HCG_GLOBAL;

   $values = $form->exportValues();

   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO wf_contactus ".
            "(`form_id`, `fname`, `lname`, `address1`, `address2`, `city`, `state`, `zip`, `country`, `phone`, `email`, `comment`, `marketing`, `release`, `submit_ts`) ".
            "VALUES ".
            "(\"".$_HCG_GLOBAL['site_id']."_contactus\", ".
            "\"".$slash_values['FName']."\", ".
            "\"".$slash_values['LName']."\", ".
            "\"".$slash_values['Address1']."\", ".
            "\"".$slash_values['Address2']."\", ".
            "\"".$slash_values['City']."\", ".
            "\"".$slash_values['State']."\", ".
            "\"".$slash_values['Zip']."\", ".
            "\"".$slash_values['Country']."\", ".
            "\"".$slash_values['Phone']."\", ".
            "\"".$slash_values['Email']."\", ".
            "\"".$slash_values['Comment']."\", ".
            "\"".$slash_values['Marketing']."\", ".
            "\"".$slash_values['Release']."\", ".
            "\"".time()."\")";
   $db->Execute($query);

   $values['brand_name'] = get_brand_name($_HCG_GLOBAL['site_id']);
   $values['DateSent'] = date("Y-m-d");
   $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $values);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }
   
   // send safe copies to internal folks (to avoid auto-reply issues)
   if ($mailtpl3 != "") {
      $o = new HCG_Smarty;
      $o->assign("mail", $values);
      $o->setTplPath($mailtpl3);
      $mail_content3 = $o->fetch($mailtpl3);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content3)."\n");
      pclose($fd);
   }

}

//-------------------------------------------------------------------------
// vMaliciousAttack
//   This is to test for a specific attack we've been receiving, but it
//   be expanded to check for other attacks as needed.
//
//-------------------------------------------------------------------------
function vMaliciousAttack($data)
{
   // 9/2005 - someone keeps sending lots of emails with fake email
   // addresses entered in all fields
   if ($data['FName'] == $data['Email']) {
      return array('FName' => "This field content is not permitted.");
   }

   // 9/2005 - someone also seems to be trying to send a multi-part message
   // as part of the comments.
   if (strpos("x".$data['Comment'], "Content-Type: multipart/mixed;")) {
      return array('Comment' => "This field content is not permitted.");
   }
   return true;
}


//-------------------------------------------------------------------------
// TAG: webmaster_form
//   Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function webmaster_form($mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';


// tag should allow for 2 emails to go out
//   usually 1 to user
//   usually 1 internal
//   get('mailform.contactus_form', "contactus_mail.tpl", "");
// potentially no emails could be sent, just save to database.

// use

$hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
$siteid = $_HCG_GLOBAL['site_id'];


//echo $hcg_site;
//echo $siteid;

$form_html = "";
$display_response = false;

$form = new HTML_QuickForm('webmaster', null, null, null, null, true);

$form->addElement('text', 'FName', 'First Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'LName', 'Last Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'Email', 'Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('text', 'Email2', 'Please Confirm Your Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('textarea', 'Comment', 'Message:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));

$form->addElement('hidden', 'siteid', $siteid);

$form->addElement('submit', 'Submit', 'Send Message');


$form->applyFilter('FName', 'trim');
$form->applyFilter('LName', 'trim');
$form->applyFilter('Email', 'trim');
$form->applyFilter('Email2', 'trim');

$form->addFormRule('vMaliciousAttack');

$form->addRule('FName', 'You must enter your last name.', 'required', null, 'client');
$form->addRule('FName', 'Too many characters.', 'maxlength', '25');
$form->addRule('FName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('LName', 'You must enter your last name.', 'required', null, 'client');
$form->addRule('LName', 'Too many characters.', 'maxlength', '25');
$form->addRule('LName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('Email', 'You must enter your email address.', 'required', null, 'client');
$form->addRule('Email', 'This does not appear to be a valid email address.', 'email', null, 'client');
$form->addRule('Email2', 'You must confirm your email address.', 'required', null, 'client');
$form->addRule(array('Email','Email2'), 'The two email fields must match.', 'compare', null, 'client');

if ($form->validate()) {
   processwebmaster_form($form, $mailtpl1, $mailtpl2);
   $display_response = true;
} else {

   // create a new template object
   $tpl = new HCG_Smarty;
   // prepare the renderer for Smarty
   $renderer = &new HTML_QuickForm_Renderer_ArraySmarty($tpl);

   $required_template =
         '{if $error}<span style="color:#F00">
             {$label}</span>
          {else}
             {$label}
             {if $required}
                <span style="color:#F00">*</span>
             {/if}
          {/if}';
   $error_template =
         '{$html}
          {if $error}
             <span style="color:#F00">{$error}</span>
          {/if}';
   $renderer->setRequiredTemplate($required_template);
   $renderer->setErrorTemplate($error_template);

   // assign all variables to the template
   $form->accept($renderer);
   $form_data = $renderer->toArray();
//   echo "<pre>"; print_r($form_data); echo "</pre>";
   $tpl->assign('form_data', $form_data);

   // process the template for display
   $tpl->setTplPath("webmaster_form.tpl");
   $form_html = $tpl->fetch("webmaster_form.tpl");

}

// end form code

   $results[0] = $display_response;
   $results[1] = $form_html;

   return $results;
}



//-------------------------------------------------------------------------
// TAG: processwebmaster_form
//   used to process the form data from 'contact' Quickform;
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processwebmaster_form($form, $mailtpl1, $mailtpl2)

{
   global $_HCG_GLOBAL;

   $values = $form->exportValues();

   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO wf_webmaster ".
            "(`form_id`, `fullname`, `email`, `comment`, `submit_ts`) ".
            "VALUES ".
            "(\"".$_HCG_GLOBAL['site_id']."_webmaster\", ".
            "\"".$slash_values['FName']." ".$slash_values['LName']."\", ".
            "\"".$slash_values['Email']."\", ".
            "\"".$slash_values['Comment']."\", ".
            "\"".time()."\")";
   $db->Execute($query);

   $values['fullname'] = $values['FName']." ".$values['LName'];
   $values['brand_name'] = get_brand_name($_HCG_GLOBAL['site_id']);
   $values['DateSent'] = date("Y-m-d");
   $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $values);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }
}


//-------------------------------------------------------------------------
// TAG: askthedoctor_form
//   Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function askthedoctor_form($md_name, $mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';


// tag should allow for 2 emails to go out
//   usually 1 to user
//   usually 1 internal
//   get('mailform.contactus_form', "contactus_mail.tpl", "");
// potentially no emails could be sent, just save to database.

// use

$hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
$siteid = $_HCG_GLOBAL['site_id'];


//echo $hcg_site;
//echo $siteid;

$form_html = "";
$display_response = false;

$form = new HTML_QuickForm('askthedoctor', null, null, null, null, true);

$form->addElement('text', 'FName', 'First Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'LName', 'Last Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'Email', 'Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('text', 'Email2', 'Please Confirm Your Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('textarea', 'Question', 'Question:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));

$form->addElement('hidden', 'siteid', $siteid);
$form->addElement('hidden', 'doctor', $md_name);

$form->addElement('submit', 'Submit', 'Send Question');

$form->addElement('html', '<tr><td width="120"><img src="/images/dot_clear.gif" width="120" height="1" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td></tr>');


$form->applyFilter('FName', 'trim');
$form->applyFilter('LName', 'trim');
$form->applyFilter('Email', 'trim');
$form->applyFilter('Email2', 'trim');


$form->addRule('FName', 'You must enter your first name.', 'required', null, 'client');
$form->addRule('FName', 'Too many characters.', 'maxlength', '25');
$form->addRule('FName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('LName', 'You must enter your last name.', 'required', null, 'client');
$form->addRule('LName', 'Too many characters.', 'maxlength', '25');
$form->addRule('LName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('Email', 'You must enter your email address.', 'required', null, 'client');
$form->addRule('Email', 'This does not appear to be a valid email address.', 'email', null, 'client');
$form->addRule('Email2', 'You must confirm your email address.', 'required', null, 'client');
$form->addRule(array('Email','Email2'), 'The two email fields must match.', 'compare', null, 'client');

if ($form->validate()) {
   processaskthedoctor_form($form, $mailtpl1, $mailtpl2);
   $display_response = true;
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
// TAG: processaskthedoctor_form
//   used to process the form data from 'contact' Quickform;
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processaskthedoctor_form($form, $mailtpl1, $mailtpl2)

{
   global $_HCG_GLOBAL;

   $values = $form->exportValues();

   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO wf_askthedoctor ".
            "(`form_id`, `doctor`, `fullname`, `email`, `question`, `datesent`, `submit_ts`) ".
            "VALUES ".
            "(\"".$_HCG_GLOBAL['site_id']."_askthedoctor\", ".
            "\"".$slash_values['doctor']."\", ".
            "\"".$slash_values['FName']." ".$slash_values['LName']."\", ".
            "\"".$slash_values['Email']."\", ".
            "\"".$slash_values['Question']."\", ".
            "\"".date("Y-m-d")."\", ".
            "\"".time()."\")";
   $db->Execute($query);

   $values['fullname'] = $values['FName']." ".$values['LName'];
   $values['brand_name'] = get_brand_name($_HCG_GLOBAL['site_id']);
   $values['DateSent'] = date("Y-m-d");
   $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $values);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }
}

//-------------------------------------------------------------------------
// TAG: disclaimer_alert
//   inserts the JavaScript for disclaimer if set to display
//-------------------------------------------------------------------------

function disclaimer_alert()
{
   global $_HCG_GLOBAL;

   if ($_HCG_GLOBAL['display_mail_disclaimer'] == true) {

      echo "<!-- Alert Code -->\n";
      echo "<script language=\"JavaScript\">\n";
      echo "function CONFIRM()\n";
      echo "{\n";
      echo '   alert("'.$_HCG_GLOBAL['mail_disclaimer'].' ");'."\n";
      echo "   return \" \";\n";
      echo "}\n";
      echo "document.write(CONFIRM());\n";
      echo "</script>  \n";
      echo "<!-- end Alert Code -->\n";
   }
}

//-------------------------------------------------------------------------
// TAG: disclaimer_text
//   inserts the HTML copy if set to display
//-------------------------------------------------------------------------

function disclaimer_text()
{
   global $_HCG_GLOBAL;

   if ($_HCG_GLOBAL['display_mail_disclaimer'] == true) {

      echo "<p><strong>".$_HCG_GLOBAL['mail_disclaimer']."</strong></p>";
   }
}


//-------------------------------------------------------------------------
// TAG: submitrecipe_form
//   Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function submitrecipe_form($mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';


// tag should allow for 2 emails to go out
//   usually 1 to user
//   usually 1 internal
//   get('mailform.contactus_form', "contactus_mail.tpl", "");
// potentially no emails could be sent, just save to database.

// use

$hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
$siteid = $_HCG_GLOBAL['site_id'];


//echo $hcg_site;
//echo $siteid;

$form_html = "";
$display_response = false;

$form = new HTML_QuickForm('submitrecipe', null, null, null, null, true);

$form->addElement('text', 'FName', 'First Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'LName', 'Last Name:', array('size' => 25, 'maxlength' => 25));
$form->addElement('text', 'Phone', 'Daytime Phone:', array('size' => 25, 'maxlength' => 14));
$form->addElement('text', 'Email', 'Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('text', 'Email2', 'Please Confirm Your Email:', array('size' => 30, 'maxlength' => 255));
$form->addElement('textarea', 'iList', 'Ingredient list:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));
$form->addElement('textarea', 'Directions', 'Directions:', array('cols' => 44, 'rows' => 10, 'wrap' => "virtual"));

$form->addElement('hidden', 'siteid', $siteid);

$form->addElement('submit', 'Submit', 'Send Message');

$form->addElement('html', '<tr><td width="120"><img src="/images/dot_clear.gif" width="120" height="1" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td></tr>');


$form->applyFilter('FName', 'trim');
$form->applyFilter('LName', 'trim');
$form->applyFilter('Email', 'trim');
$form->applyFilter('Email2', 'trim');
$form->applyFilter('Phone', 'trim');


$form->addRule('FName', 'You must enter your first name.', 'required', null, 'client');
$form->addRule('FName', 'Too many characters.', 'maxlength', '25');
$form->addRule('FName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('LName', 'You must enter your last name.', 'required', null, 'client');
$form->addRule('LName', 'Too many characters.', 'maxlength', '25');
$form->addRule('LName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('Email', 'You must enter your email address.', 'required', null, 'client');
$form->addRule('Email', 'This does not appear to be a valid email address.', 'email', null, 'client');
$form->addRule('Email2', 'You must confirm your email address.', 'required', null, 'client');
$form->addRule(array('Email','Email2'), 'The two email fields must match.', 'compare', null, 'client');
$form->addRule('iList', 'You must enter your ingredient list.', 'required', null, 'client');
$form->addRule('Directions', 'You must enter your recipe directions.', 'required', null, 'client');


if ($form->validate()) {
   processsubmitrecipe_form($form, $mailtpl1, $mailtpl2);
   $display_response = true;
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
// TAG: processsubmitrecipe_form
//   used to process the form data from 'contact' Quickform;
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processsubmitrecipe_form($form, $mailtpl1, $mailtpl2)

{
   global $_HCG_GLOBAL;

   $values = $form->exportValues();

   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO wf_submitrecipe ".
            "(`form_id`, `fullname`, `email`, `phone`, `ilist`, `directions`, `submit_ts`) ".
            "VALUES ".
            "(\"".$_HCG_GLOBAL['site_id']."_submitrecipe\", ".
            "\"".$slash_values['FName']." ".$slash_values['LName']."\", ".
            "\"".$slash_values['Email']."\", ".
            "\"".$slash_values['Phone']."\", ".
            "\"".$slash_values['iList']."\", ".
            "\"".$slash_values['Directions']."\", ".
            "\"".time()."\")";
   $db->Execute($query);

   $values['fullname'] = $values['FName']." ".$values['LName'];
   $values['brand_name'] = get_brand_name($_HCG_GLOBAL['site_id']);
   $values['DateSent'] = date("Y-m-d");
   $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $values);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }
}


//-------------------------------------------------------------------------
// TAG: csdistributor_form
//   Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function csdistributor_form($mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';


// tag should allow for 2 emails to go out
//   usually 1 to user
//   usually 1 internal
//   get('mailform.contactus_form', "contactus_mail.tpl", "");
// potentially no emails could be sent, just save to database.

// use

$hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
$siteid = $_HCG_GLOBAL['site_id'];


//echo $hcg_site;
//echo $siteid;

$form_html = "";
$display_response = false;

$form = new HTML_QuickForm('csdistributor', null, null, null, null, true);

$form->addElement('radio', 'region', 'Main Distribution Point:', 'Western US &nbsp;&nbsp;', 'W');
$form->addElement('radio', 'region', '', 'Eastern US &nbsp;&nbsp;', 'E');
$form->addElement('radio', 'region', '', 'Canada &nbsp;&nbsp;', 'C');
$form->addElement('radio', 'region', '', 'International &nbsp;&nbsp;', 'I');
$form->addElement('text', 'FName', 'First Name:', array('size' => 30, 'maxlength' => 25, 'style' => "width: 200px"));
$form->addElement('text', 'LName', 'Last Name:', array('size' => 30, 'maxlength' => 25, 'style' => "width: 200px"));
$form->addElement('text', 'CName', 'Company Name:', array('size' => 30, 'maxlength' => 25, 'style' => "width: 200px"));
$form->addElement('text', 'Phone', 'Phone Number:', array('size' => 30, 'maxlength' => 25, 'style' => "width: 200px"));
$form->addElement('text', 'Email', 'Email:', array('size' => 30, 'maxlength' => 255, 'style' => "width: 200px"));
$form->addElement('text', 'Email2', 'Please Confirm Your Email:', array('size' => 30, 'maxlength' => 255, 'style' => "width: 200px"));
$form->addElement('textarea', 'Inquiry', 'Inquiry:', array('cols' => 30, 'rows' => 5, 'wrap' => "virtual", 'style' => "width: 285px"));

$form->addElement('hidden', 'siteid', $siteid);

$form->addElement('submit', 'Submit', 'Send Inquiry', 'class="buttonsubmitlarge"');

$form->addElement('html', '<tr><td width="120"><img src="/images/dot_clear.gif" width="120" height="1" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td></tr>');


$form->applyFilter('FName', 'trim');
$form->applyFilter('LName', 'trim');
$form->applyFilter('Phone', 'trim');
$form->applyFilter('Email', 'trim');
$form->applyFilter('Email2', 'trim');

$form->addRule('region', 'You must specify your distribution region.', 'required', null, 'client');
$form->addRule('FName', 'You must enter your first name.', 'required', null, 'client');
$form->addRule('FName', 'Too many characters.', 'maxlength', '25');
$form->addRule('FName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('LName', 'You must enter your last name.', 'required', null, 'client');
$form->addRule('LName', 'Too many characters.', 'maxlength', '25');
$form->addRule('LName', 'Invalid characters.', 'regex', '/^[A-Za-z\- ]+$/');
$form->addRule('CName', 'You must enter your company name.', 'required', null, 'client');
$form->addRule('Phone', 'You must enter your phone number.', 'required', null, 'client');
$form->addRule('Email', 'You must enter your email address.', 'required', null, 'client');
$form->addRule('Email', 'This does not appear to be a valid email address.', 'email', null, 'client');
$form->addRule('Email2', 'You must confirm your email address.', 'required', null, 'client');
$form->addRule(array('Email','Email2'), 'The two email fields must match.', 'compare', null, 'client');
$form->addRule('Inquiry', 'You must enter the nature of your inquiry.', 'required', null, 'client');

if ($form->validate()) {
   processcsdistributor_form($form, $mailtpl1, $mailtpl2);
   $display_response = true;
} else {

   // create a new template object
   $tpl = new HCG_Smarty;
   // prepare the renderer for Smarty
   $renderer = &new HTML_QuickForm_Renderer_ArraySmarty($tpl);

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
//   echo "<pre>"; print_r($form_data); echo "</pre>";
   $tpl->assign('form_data', $form_data);

   // process the template for display
   $tpl->setTplPath("csdistributor_form.tpl");
   $form_html = $tpl->fetch("csdistributor_form.tpl");

}

// end form code

   $results[0] = $display_response;
   $results[1] = $form_html;

   return $results;
}



//-------------------------------------------------------------------------
// TAG: processcsdistributor_form
//   used to process the form data from 'contact' Quickform;
//   includes: save to db and sending internal and autoreply emails
//-------------------------------------------------------------------------

function processcsdistributor_form($form, $mailtpl1, $mailtpl2)

{
   global $_HCG_GLOBAL;

   $values = $form->exportValues();

  if ($values['region'] == "W")  {
      $values['to'] = "Debby Damgaard <ddamgaar@hain-celestial.com>";
  } elseif ($values['region'] == "E") {
      $values['to'] = "Tom Raymont <traymont@hain-celestial.com>";
  } elseif ($values['region'] == "C") {
      $values['to'] = "Lynn Vinnai <lynnv@Hain-Celestial.com>";  
  } else {
      $values['to'] = "Arlene Indal <AIndal@Hain-Celestial.com>, Valentina Muru <vmuru@Hain-Celestial.com>";
  }



   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }


   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO wf_csdistributor ".
            "(`form_id`, `fullname`, `company_name`, `email`, `inquiry`, `datesent`, `submit_ts`) ".
            "VALUES ".
            "(\"".$_HCG_GLOBAL['site_id']."_csdistributor\", ".
            "\"".$slash_values['FName']." ".$slash_values['LName']."\", ".
            "\"".$slash_values['CName']."\", ".
            "\"".$slash_values['Email']."\", ".
            "\"".$slash_values['Inquiry']."\", ".
            "\"".date("Y-m-d")."\", ".
            "\"".time()."\")";
   $db->Execute($query);

   $values['fullname'] = $values['FName']." ".$values['LName'];
   $values['brand_name'] = get_brand_name($_HCG_GLOBAL['site_id']);
   $values['DateSent'] = date("Y-m-d");
   $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $values);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }
}


?>
