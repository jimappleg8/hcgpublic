<?php

// =========================================================================
// maillist.adm.php
// written by Jim Applegate
//
// =========================================================================


//-------------------------------------------------------------------------
// TAG: adm_notify_me
//
//-------------------------------------------------------------------------

function adm_notify_me()
{
   global $_HCG_GLOBAL;
   
   $settings['form_name'] = "notify";
   $settings['form_table'] = "notify";
   $settings['acl_allow_from'] = "";
   $settings['acl_deny_from'] = "";
   $settings['form_log_file'] = "";
   $settings['form_def_file'] = "notify.php";
   $settings['upload_file'] = false;
   $settings['upload_file_dir'] = "";
   $settings['upload_file_fields'] = array();
   $settings['form_template'] = "notify_form.tpl";
   $settings['send_outbound_mail'] = false;
   $settings['outbound_mail_template'] = "";
   $settings['send_inbound_mail'] = false;
   $settings['inbound_mail_template'] = "";
   $settings['show_thankyou_template'] = "";
   $settings['auto_redirect'] = false;
   $settings['auto_redirect_url'] = "";
   $settings['errors'] = array();
   
   // detect if the form was submitted
   if (!empty($_HCG_GLOBAL['passed_vars'])) {
      $process_form = true;
   } else {
      $process_form = false;
   }
   $form_sent = false;

   // create template object for main form
   $t = new HCG_Smarty;
   $t->fs_root = $_HCG_GLOBAL['formsess_dir'];
   $t->enableFormsess();
   
   // create formsess object to manipulate form
   $f = new Formsess($settings['form_name']);

   if ($process_form == true) {

      $f->reset("errors");
      $f->performCheck();
      
      if ($f->hasErrors() == false) {

         $s = new FormSubmission();
         $s->setConfigs($settings);
         $s->loadFormDef();
         $s->setupForm();
         if ($s->processForm()) {
            $form_sent = true;
            $f->reset();
         } else {
           // trigger an error
         }
      }
   }
   
      // forward fields to form if needed
   if (!empty($_HCG_GLOBAL['passed_vars']) && $form_sent == false) {
      $f->assignArray($_HCG_GLOBAL['passed_vars']);
   }
   
   // tell the template if it should display the form or a confirmation
   $t->assign("form_sent", $form_sent);

   $t->setTplPath($settings['form_template']);
   echo $t->fetch($settings['form_template']);
      
}


//-------------------------------------------------------------------------
// TAG: adm_csv_export
//
//-------------------------------------------------------------------------

function adm_csv_export ()
{
   require_once $FORMDATA_CLASS;
   
   $fid = $this->getRequestField('fid');
   $mode = $this->getRequestField('mode');

   if (!(in_array($fid, array_keys($GLOBALS['KNOWN_FORMS'])))) {
      $this->alert('FORM_NOT_SELECTED');	
      return;
   }
   $frmData = new FormData($this->dbi, $fid);
   $frmName = substr($GLOBALS['KNOWN_FORMS'][$fid], 0, strpos($GLOBALS['KNOWN_FORMS'][$fid],'.'));
   if ($mode == DOWNLOAD_TYPE_LATEST) {
      $lastDLMaxRec = $frmData->getLastDLRecordID();
      $dataArr = $frmData->getDataAfterRecordID($lastDLMaxRec);            
      $fileName = $frmName.$lastDLMaxRec;
   } else {
      $dataArr = $frmData->getFormData();
      $fileName = $frmName;
   }
   if (!empty($dataArr)) {
      $fp = fopen ($GLOBALS['ROOT_PATH'].$GLOBALS['REL_APP_PATH']."/temp/".$fileName.".csv", "w+");
      while (list($fieldName, $value) = each($dataArr[0])) {
         fwrite($fp, $this->dblquote(stripslashes($fieldName)).",");
      }
      reset($dataArr);
      foreach ($dataArr as $row) {
         fwrite($fp, "\n");
         $lastID = $row->id;
         while (list($fieldName, $value) = each($row)) {
            fwrite($fp, $this->dblquote(stripslashes($value)).",");
         }	
      }
      $frmData->updateDownloadTrack($lastID); 
      header("Location: temp/".$fileName.".csv");
   } else {
      $this->alert('DATASET_EMPTY');	
   }
}

function dblquote($str)
{
   return "\"" . $str . "\"";
}


?>
