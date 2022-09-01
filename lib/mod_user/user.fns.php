<?php

// =========================================================================
// user.fns.php
// written by Jim Applegate
//
// =========================================================================

require_once 'user.inc.php';
require_once 'dbi_adodb.inc.php';


// ------------------------------------------------------------------------
// TAG: auto_register
// This is currently used by the WestSoy website. A similar script is 
// used on the Imagine website and they should probably be reconciled.
// ------------------------------------------------------------------------

function auto_register($mailtpl1 = 'autoregister_mail.tpl', $mailtpl2 = 'autoregister_notice.tpl') 
{
   global $_HCG_GLOBAL;

   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

   $form_html = "";
   $display_response = false;

   $form = new HTML_QuickForm('autoregister', null, null, null, null, true);

   $form->addElement('text', 'fullname', 'Full Name:', array('size' => 30, 'maxlength' => 128));
   $form->addElement('text', 'company', 'Company Affiliation:', array('size' => 30, 'maxlength' => 128));
   $form->addElement('text', 'email', 'Email:', array('size' => 30, 'maxlength' => 255));
   $form->addElement('text', 'email2', 'Confirm Email:', array('size' => 30, 'maxlength' => 255));
   $form->addElement('text', 'dayphone', 'Daytime Phone:', array('size' => 30, 'maxlength' => 14));
   $form->addElement('submit', 'Submit', 'Register');

   $form->applyFilter('fullname', 'trim');
   $form->applyFilter('company', 'trim');
   $form->applyFilter('email', 'trim');
   $form->applyFilter('email', 'trim');
   $form->applyFilter('email2', 'trim');

   $form->addRule('fullname', 'You must enter your name.', 'required', null, 'client');
   $form->addRule('company', 'You must enter your company name.', 'required', null, 'client');
   $form->addRule('email', 'You must enter your email address.', 'required', null, 'client');
   $form->addRule('email', 'This does not appear to be a valid email address.', 'email', null, 'client');
   $form->addRule('email2', 'You must confirm your email address.', 'required', null, 'client');
   $form->addRule(array('email','email2'), 'The two email fields must match.', 'compare', null, 'client');
   $form->addRule('dayphone', 'You must enter a daytime phone number.', 'required', null, 'client');

   if ($form->validate())
   {
      $form->freeze();
      _auto_register($form, $mailtpl1, $mailtpl2);
      $display_response = true;
   }

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
   $tpl->assign('display_response', $display_response);

   // process the template for display
   $tpl->setTplPath("autoregister_form.tpl");
   $form_html = $tpl->fetch("autoregister_form.tpl");

   return $form_html;
}


//-------------------------------------------------------------------------
// TAG: _auto_register
//   used to process the auto-register form data
//-------------------------------------------------------------------------

function _auto_register($form, $mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   $values = $form->exportValues();

   foreach ($values as $key=>$value)
   {
      $slash_values[$key] = addslashes($value);
   }

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO wf_autoregister ".
            "(form_id, fullname, company, email, dayphone, submit_ts) ".
            "VALUES ".
            "(\"".$_HCG_GLOBAL['site_id']."_press\", ".
            "\"".$slash_values['fullname']."\", ".
            "\"".$slash_values['company']."\", ".
            "\"".$slash_values['email']."\", ".
            "\"".$slash_values['dayphone']."\", ".
            "\"".time()."\")";
   $db->Execute($query);

   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail))
   {
      $sendmail = "/usr/sbin/sendmail -t ";
   }
   
   $m = new HCG_Smarty;
   $m->assign("mail", $values);
   $m->setTplPath($mailtpl1);
   $mail_content = $m->fetch($mailtpl1);
   $fd = popen($sendmail,"w");
   fputs($fd, stripslashes($mail_content)."\n");
   pclose($fd);

   $n = new HCG_Smarty;
   $n->assign("mail", $values);
   $n->setTplPath($mailtpl2);
   $mail_content2 = $n->fetch($mailtpl2);
   $fd = popen($sendmail,"w");
   fputs($fd, stripslashes($mail_content2)."\n");
   pclose($fd);

}


?>
