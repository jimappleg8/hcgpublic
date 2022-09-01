<?php

require_once("DataCleanup.class.php");

// =========================================================================
// formsprocessing.class.php
//   parts taken from "Secure PHP Development" by Mohammed J. Kabir
//   adapted for use with hcg_public framework by Jim Applegate
//
//   The class is designed to consolodate the processing that happens after
//   a form has been submitted and verified.
//
// =========================================================================

   class forms_processing {

      var $_DBI;
      var $_ERRORS;
      var $_FORM_FIELD_VALUES;
      
//      var $FORM_ID;       // settings passed from calling application
//      var $FORM_NAME;
//      var $FORM_DATABASE;
      var $FORM_TABLE;
//      var $ACL_ALLOW_FROM;
//      var $ACL_DENY_FROM;
//      var $FORM_LOG_FILE;
      var $FORM_DEF_FILE;
      var $UPLOAD_FILE;
      var $UPLOAD_FILE_DIR;
      var $UPLOAD_FILE_FIELDS;
//      var $FORM_TEMPLATE;
//      var $SEND_OUTBOUND_MAIL;
//      var $OUTBOUND_MAIL_TEMPLATE;
//      var $SEND_INBOUND_MAIL;
//      var $INBOUND_MAIL_TEMPLATE;
//      var $SHOW_THANKYOU_TEMPLATE;
//      var $AUTO_REDIRECT;
//      var $AUTO_REDIRECT_URL;
      var $_FORM_ERRORS;

      var $_FORM_FIELDS;   // variable loaded from table definition

      var $_REQUIRED;      // variables built from $_FORM_FIELDS
      var $_TYPE;
      var $_VALIDATOR;
      var $_CLEANUP;
      var $_SIZE;
      

      //--------------------------------------------------------------------
      // forms_processing()
      //   constructor.
      //
      //--------------------------------------------------------------------
      function forms_processing(&$dbi)
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
      // setConfigs()
      //
      //--------------------------------------------------------------------
      function setConfigs($settings)
      {
         global $_HCG_GLOBAL;
         
//         $this->FORM_ID = $settings['form_id'];
//         $this->FORM_NAME = $settings['form_name'];
//         $this->FORM_DATABASE = $settings['form_database'];
         $this->FORM_TABLE = $settings['form_table'];
//         $this->ACL_ALLOW_FROM = $settings['acl_allow_from'];
//         $this->ACL_DENY_FROM = $settings['acl_deny_from'];
//         $this->FORM_LOG_FILE = $settings['form_log_file'];
         $this->FORM_DEF_FILE = $settings['form_def_file'];
         $this->UPLOAD_FILE = $settings['upload_file'];
         $this->UPLOAD_FILE_DIR = $settings['upload_file_dir'];
         $this->UPLOAD_FILE_FIELDS = $settings['upload_file_fields'];
         $this->FORM_TEMPLATE = $settings['form_template'];
//         $this->SEND_OUTBOUND_MAIL = $settings['send_outbound_mail'];
//         $this->OUTBOUND_MAIL_TEMPLATE = $settings['outbound_mail_template'];
//         $this->SEND_INBOUND_MAIL = $settings['send_inbound_mail'];
//         $this->INBOUND_MAIL_TEMPLATE = $settings['inbound_mail_template'];
//         $this->SHOW_THANKYOU_TEMPLATE = $settings['show_thankyou_template'];
//         $this->AUTO_REDIRECT = $settings['auto_redirect'];
//         $this->AUTO_REDIRECT_URL = $settings['auto_redirect_url'];
         $this->_FORM_ERRORS = $settings['errors'];

         if ($this->UPLOAD_FILE) {  
            $this->_FILE_LOAD_FIELDS = $this->UPLOAD_FILE_FIELDS;
         }
         
         require $_HCG_GLOBAL['forms_dir'].'/'.$this->FORM_DEF_FILE;
         $this->_FORM_FIELDS = $FORM_SAVE_ARRAY;
         $this->_PROCESS = $FORM_PROCESS_ARRAY;
         $this->_UNPROCESS = $FORM_UNPROCESS_ARRAY;
      }

      //--------------------------------------------------------------------
      // setField()
      //  Allows you to set a single field value. It will overwrite any
      //  existing value if the field exists.
      //
      //--------------------------------------------------------------------
      function setField($field, $value)
      {
         $this->FORM_FIELD_VALUES[$field] = $value;
         return TRUE;
      }

      //--------------------------------------------------------------------
      // unsetField()
      //  Allows you to remove a single field value. It will remove the 
      //  field in both FORM_FIELD_VALUES and _FORM_FIELDS
      //
      //--------------------------------------------------------------------
      function unsetField($field)
      {
         unset($this->FORM_FIELD_VALUES[$field]);
         unset($this->_FORM_FIELDS[$field]);
         return TRUE;
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
      // getFields()
      //  Returns contents of fields as an array.
      //--------------------------------------------------------------------
      function getFields()
      {
         return $this->FORM_FIELD_VALUES;
      }

      //--------------------------------------------------------------------
      // importFields()
      //   imports the form data from the form_class object supplied. This
      //   is the object created by the formsgeneration package.
      //
      //--------------------------------------------------------------------
      function importFields(&$form_obj)
      {
         foreach ($this->_FORM_FIELDS as $field => $type) {
            $this->FORM_FIELD_VALUES[$field] = $form_obj->GetInputValue($field);
         }      
         return TRUE;
      }

      //--------------------------------------------------------------------
      // exportFields()
      //   exports the form data to the form_class object supplied. This
      //   is the object created by the formsgeneration package.
      //
      //--------------------------------------------------------------------
      function exportFields(&$form_obj)
      {
         foreach ($this->_FORM_FIELDS as $field => $type) {
            $form_obj->SetInputValue($field, $this->FORM_FIELD_VALUES[$field]);
         }      
         return TRUE;
      }

      //--------------------------------------------------------------------
      // processData()
      //   process data before saving to a database
      //
      //--------------------------------------------------------------------
      function processData()
      {
         // Create a Data Cleanup object
         $cleanupObj = new DataCleanup();

         foreach ($this->_PROCESS as $field => $cleanupMethods) {

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
      // unprocessData()
      //   process data after retrieving from a database
      //
      //--------------------------------------------------------------------
      function unprocessData()
      {
         // Create a Data Cleanup object
         $cleanupObj = new DataCleanup();

         foreach ($this->_UNPROCESS as $field => $cleanupMethods) {

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
      // selectData()
      //   Select data from the FORM_TABLE
      //
      //--------------------------------------------------------------------
      function selectData($whereArray)
      {
         $conditionList = "";
         $num_conditions = count($whereArray);
         $count = 0;
         foreach ($whereArray as $field => $value) {
            if ($count != 0) {
               $conditionList .= " AND ";
            }
            if ($this->_FORM_FIELDS[$field] == "text") {
               $conditionList .= $field." LIKE '".$value."'";
            } else {
               $conditionList .= $field." = ".$value;
            }
         }
         $query = sprintf("SELECT * FROM %s WHERE %s", $this->FORM_TABLE, $conditionList);
         
         $this->_DBI->SetFetchMode($query);
         $result = $this->_DBI->GetRow($query);
         
         foreach ($this->_FORM_FIELDS as $field => $type) {
            $this->FORM_FIELD_VALUES[$field] = $result[$field];
         }
         
         return ($result == TRUE) ? TRUE  : FALSE;
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
               $s = addslashes($this->FORM_FIELD_VALUES[$field]);
               $s = $this->_DBI->qstr($s, get_magic_quotes_gpc());
               array_push($values, $s);
            } else {
               array_push($values, $this->FORM_FIELD_VALUES[$field]);
            }
         }

         // Now build the SQL INSERT statement.
         $fieldList = implode(',', $fields);
         $valueList = implode(',', $values);
         $stmt = sprintf("INSERT INTO %s (%s) VALUES (%s)", $this->FORM_TABLE, $fieldList, $valueList); 
         //echo $stmt;
         // Execute the statement
         $result = $this->_DBI->Execute($stmt);

         return ($result == TRUE) ? TRUE  : FALSE;
     }


      //--------------------------------------------------------------------
      // updateData()
      //   Update data into the FORM_TABLE
      //
      //--------------------------------------------------------------------
      function updateData($idName, $idValue)
      {
         $valueList = array();

         foreach ($this->_FORM_FIELDS as $field => $type) {

            if (!strcmp($type, 'text')) {
               $value = addslashes($this->FORM_FIELD_VALUES[$field]);
               $value = $this->_DBI->qstr($value, get_magic_quotes_gpc());
            } else {
               $value = $this->FORM_FIELD_VALUES[$field];
            }
            $valueList[] = $field . "=" . $value;
         }

         // Now build the SQL INSERT statement.
         $values = implode(',', $valueList);
         $stmt = sprintf("UPDATE %s SET %s WHERE %s = %s", $this->FORM_TABLE, $values, $idName, $idValue); 

         // Execute the statement
         $result = $this->_DBI->Execute($stmt);

         return ($result == TRUE) ? TRUE  : FALSE;
     }


      //--------------------------------------------------------------------
      // uploadFile()
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
                  $full_path = $GLOBAL[REL_APP_PATH].$this->UPLOAD_FILE_DIR.$_FILES[$fieldName]['name'];
                  move_uploaded_file($_FILES[$fieldName]['tmp_name'], $full_path);   
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
