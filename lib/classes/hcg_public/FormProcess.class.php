<?php

// =========================================================================
// FormProcess.class.php
//   from "Secure PHP Development" by Mohammed J. Kabir
//   adapted for use with hcg_public framework by Jim Applegate
//   * indicates a method that should not be used.
//
//   The class is designed to consolodate the processing that happens after
//   a form has been submitted and verified. The original class included
//   verification, but I've given that task to the FormsGenerate class.
//
// =========================================================================

   class FormProcess {

      var $_DBI;
      var $_ERRORS;
      var $_FORM_FIELD_VALUES;
      
      var $FORM_ID;       // settings passed from calling application
      var $FORM_NAME;
      var $FORM_DATABASE;
      var $FORM_TABLE;
      var $ACL_ALLOW_FROM;
      var $ACL_DENY_FROM;
      var $FORM_LOG_FILE;
      var $FORM_DEF_FILE;
      var $UPLOAD_FILE;
      var $UPLOAD_FILE_DIR;
      var $UPLOAD_FILE_FIELDS;
      var $FORM_TEMPLATE;
      var $SEND_OUTBOUND_MAIL;
      var $OUTBOUND_MAIL_TEMPLATE;
      var $SEND_INBOUND_MAIL;
      var $INBOUND_MAIL_TEMPLATE;
      var $SHOW_THANKYOU_TEMPLATE;
      var $AUTO_REDIRECT;
      var $AUTO_REDIRECT_URL;
      var $_FORM_ERRORS;

      var $_FORM_FIELDS;   // variable loaded from table definition

      var $_REQUIRED;      // variables built from $_FORM_FIELDS
      var $_TYPE;
      var $_VALIDATOR;
      var $_CLEANUP;
      var $_SIZE;
      

      //--------------------------------------------------------------------
      // FormProcess()
      //   constructor.
      //
      //--------------------------------------------------------------------
      function FormProcess($dbi)
      {
         $this->_DBI = $dbi;
         $this->_ERRORS = array();
      } 

      //--------------------------------------------------------------------
      // hasError()
      //   Returns TRUE if there are errors in submitted data
      //
      //--------------------------------------------------------------------
      function hasError()
      {
         return (count($this->_ERRORS) <= 0) ? FALSE : TRUE;
      }

      //--------------------------------------------------------------------
      // getErrors()
      //   Return error messages
      //
      //--------------------------------------------------------------------
      function getErrors()
      {
         return $this->_ERRORS;
      }

      //--------------------------------------------------------------------
      // *getErrorMessage()
      //   Return error messages for given language
      //
      //--------------------------------------------------------------------
      function getErrorMessage($lang = null, $err = null)
      {
         if (! $err) {
             $err = $this->_ERRORS;
         }

         if (is_array($err)) {
            foreach ($err as $k) {
               $k = 'ERROR_' . strtoupper($k);
               $errMsg[] = $this->_FORM_ERRORS[$lang][$k];
            }
            return '\n' . implode('\n', $errMsg);
         } else {
             $err = strtoupper($err);
             return $this->_FORM_ERRORS[$lang][$err];
         }
      }

      //--------------------------------------------------------------------
      // *setupForm()
      //   setup form's configuraiton
      //
      //--------------------------------------------------------------------
      function setupForm()
      {
         foreach ($this->_FORM_FIELDS as $field => $config) {
            // Breakdown the configuration for each form field
            list($requiredFlag,
                 $fieldType,
                 $sizeInfo,
                 $validator,
                 $cleanupMethods)     = explode(':', $config);

             $this->_REQUIRED[$field] = ($requiredFlag) ? TRUE : FALSE;

             $this->_TYPE[$field]     = strtolower($fieldType);

             $this->_VALIDATOR[$field]= strtolower($validator);
             $this->_SIZE[$field]     = strtolower($sizeInfo);
             $this->_CLEANUP[$field]  = strtolower($cleanupMethods);
         }
      }

      //--------------------------------------------------------------------
      // setConfigs()
      //
      //--------------------------------------------------------------------
      function setConfigs($settings)
      {
         $this->FORM_ID = $settings['form_id'];
         $this->FORM_NAME = $settings['form_name'];
         $this->FORM_DATABASE = $settings['form_database'];
         $this->FORM_TABLE = $settings['form_table'];
         $this->ACL_ALLOW_FROM = $settings['acl_allow_from'];
         $this->ACL_DENY_FROM = $settings['acl_deny_from'];
         $this->FORM_LOG_FILE = $settings['form_log_file'];
         $this->FORM_DEF_FILE = $settings['form_def_file'];
         $this->UPLOAD_FILE = $settings['upload_file'];
         $this->UPLOAD_FILE_DIR = $settings['upload_file_dir'];
         $this->UPLOAD_FILE_FIELDS = $settings['upload_file_fields'];
         $this->FORM_TEMPLATE = $settings['form_template'];
         $this->SEND_OUTBOUND_MAIL = $settings['send_outbound_mail'];
         $this->OUTBOUND_MAIL_TEMPLATE = $settings['outbound_mail_template'];
         $this->SEND_INBOUND_MAIL = $settings['send_inbound_mail'];
         $this->INBOUND_MAIL_TEMPLATE = $settings['inbound_mail_template'];
         $this->SHOW_THANKYOU_TEMPLATE = $settings['show_thankyou_template'];
         $this->AUTO_REDIRECT = $settings['auto_redirect'];
         $this->AUTO_REDIRECT_URL = $settings['auto_redirect_url'];
         $this->_FORM_ERRORS = $settings['errors'];

         if ($this->UPLOAD_FILE) {  
            $this->_FILE_LOAD_FIELDS = $this->UPLOAD_FILE_FIELDS;
         }
         
         $this->loadSaveDef();
      }

      //--------------------------------------------------------------------
      // setFields()
      //
      //--------------------------------------------------------------------
      function setFields($fields)
      {
         $this->FORM_FIELD_VALUES = $fields;
         return TRUE;
      }

      //--------------------------------------------------------------------
      // loadSaveDef()
      //
      //--------------------------------------------------------------------
      function loadSaveDef()
      {
         global $_HCG_GLOBAL;

         require $_HCG_GLOBAL['forms_dir'].'/'.$this->FORM_DEF_FILE;

         $this->_FORM_FIELDS = $FORM_SAVE_ARRAY;
      }

      //--------------------------------------------------------------------
      // processForm()
      //
      //--------------------------------------------------------------------
      function processForm()                     
      {
         // Cleanup
         //$this->cleanupData();

         // Insert data to database
         if (! $this->insertData()) {
            return DATABASE_FAILURE;
         }

         //upload attachment file to directory
         //$status = $this->uploadFile();
         //if ($status != 1) {
         //   return $status;
         //}

         // Send outbound and inbound emails
         if ($this->SEND_OUTBOUND_MAIL) {
            $this->sendMail($this->OUTBOUND_MAIL_TEMPLATE);
         }

         if ($this->SEND_INBOUND_MAIL) {
            $this->sendMail($this->INBOUND_MAIL_TEMPLATE);
         }

         return TRUE;
      }

      //--------------------------------------------------------------------
      // *cleanupData()
      //   cleanup data
      //
      //--------------------------------------------------------------------
      function cleanupData()
      {
         // Create a Data Cleanup object
         $cleanupObj = new DataCleanup();

         foreach ($this->_CLEANUP as $field => $cleanupMethods) {

            // Make a list of cleanup methods to apply for the current field
            $methodList = explode('|', $cleanupMethods);

            // For each cleanup method apply it
            foreach ($methodList as $func) {
               // Cleanup method names are cleanup_NAME
               $method = 'cleanup_' . $func;
               // Call the cleanup method with data from $this->FORM_FIELD_VALUES
               // store the data back in the same place
               $this->FORM_FIELD_VALUES[$field] = $cleanupObj->$method($this->FORM_FIELD_VALUES[$field]);
            }
         }
         return TRUE;
      }

      //--------------------------------------------------------------------
      // insertData()
      //   Insert data into the FORM_TABLE
      //
      //--------------------------------------------------------------------
      function insertData()
      {
         $fields = array();
         $values = array();
         foreach ($this->_FORM_FIELDS as $field => $type) {
            array_push($fields, $field);
            // If data type is text then quote(addslashes())
            if (!strcmp($type, 'text')) {
               array_push($values, $this->_DBI->quote(addslashes($this->FORM_FIELD_VALUES[$field])));
            } else {
               array_push($values, $this->FORM_FIELD_VALUES[$field]);
            }
         }
         array_push($fields, 'form_id');
         array_push($values, $this->_DBI->quote(addslashes($this->FORM_ID)));
         array_push($fields, 'submit_ts');
         array_push($values, mktime());

         // Now build the SQL INSERT statement.
         $fieldList = implode(',', $fields);
         $valueList = implode(',', $values);
         $stmt = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->FORM_TABLE, $fieldList, $valueList); 
         //echo $stmt;
         // Execute the statement
         $result = $this->_DBI->query($stmt);

         return ($result == TRUE) ? TRUE  : FALSE;
     }
     
      //--------------------------------------------------------------------
      // *uploadFile()
      //   upload attachment file to the specified directory in the config
      //
      //--------------------------------------------------------------------
      function uploadFile()
      {
         $dataValidatorObj = new DataValidator();

         if (!empty($this->_FILE_LOAD_FIELDS)) {
            foreach ($this->_FILE_LOAD_FIELDS as $fieldName => $value) {

               list($requiredFlag, $sizeInfo)  =  explode(':', $value);

               if ($requiredFlag) { 
                  if (empty($_FILES[$fieldName]['name'])) {
                     return MISSING_REQUIRED_VALUES;
                  }
               }

               if (!empty($_FILES[$fieldName]['name'])) {
                  if( !$dataValidatorObj->validate_file_size($sizeInfo, $_FILES[$fieldName]['size']) ) {
                     return INVALID_FILE_SIZE;
                  }
                  move_uploaded_file($_FILES[$fieldName]['tmp_name'], $GLOBAL[REL_APP_PATH].$this->UPLOAD_FILE_DIR.$_FILES[$fieldName]['name']);   
               }
            }
         }
         return TRUE;
      }

      //--------------------------------------------------------------------
      // sendMail()
      //
      //--------------------------------------------------------------------
      function sendMail($msgTemplate)
      {
         global $_HCG_GLOBAL;
         
         // We use the sendmail program directly because that allows us to 
         // include the e-mail header as part of the email template.
         $sendmail = ini_get('sendmail_path');
         if (empty($sendmail)) {
            $sendmail = "/usr/sbin/sendmail -t ";
         }
         
         $m = new HCG_Smarty;
         $m->assign("mail", $this->FORM_FIELD_VALUES);
         $m->setTplPath($msgTemplate);
         $mail_content = $m->fetch($msgTemplate);
      
         $fd = popen($sendmail,"w");
         fputs($fd, stripslashes($mail_content)."\n");
         pclose($fd);

      }

   }//class


?>
