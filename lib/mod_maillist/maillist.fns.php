<?php

// =========================================================================
// maillist.fns.php
// written by Jim Applegate
//
// =========================================================================

require_once('mod_mailform/mailform.fns.php');


//-------------------------------------------------------------------------
// TAG: notify_me
//
//-------------------------------------------------------------------------

function notify_me()
{
   global $_HCG_GLOBAL;
   
   $settings['form_id'] = $_HCG_GLOBAL['site_id'];
   $settings['form_name'] = "notify";
   $settings['form_database'] = "hcg_public_master";
   $settings['form_table'] = "wf_notify";
   $settings['acl_allow_from'] = "";
   $settings['acl_deny_from'] = "";
   $settings['form_log_file'] = "";
   $settings['form_def_file'] = "wf_notify.php";
   $settings['upload_file'] = false;
   $settings['upload_file_dir'] = "";
   $settings['upload_file_fields'] = array();
   $settings['form_template'] = "notify_form.tpl";
   $settings['send_outbound_mail'] = false;
   $settings['outbound_mail_template'] = "";
   $settings['send_inbound_mail'] = false;
   $settings['inbound_mail_template'] = "";
   $settings['show_thankyou_template'] = "";
   
   return mailform($settings);
}



?>
