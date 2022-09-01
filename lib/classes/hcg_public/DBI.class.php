<?php

// =========================================================================
// DBI.class.php
//   Database abstraction class. This class provides database abstraction 
//   using ADODB.
//
//   from "Secure PHP Development" by Mohammed J. Kabir
//   Adapted for use with hcg_public framework by Jim Applegate
//
// =========================================================================

   define('DBI_LOADED', TRUE);
   
   global $_HCG_GLOBAL;
   require_once($_HCG_GLOBAL['adodb_dir'] . '/adodb.inc.php');

   class DBI {

      var $VERSION = "1.0.0";

      var $db_type;     // connection information a la ADODB
      var $db_host;
      var $db_user;
      var $db_pass;
      var $db_name;
      
      var $connected;   // boolean
      var $dbh;         // connection object (handle)
      
      var $error;
      var $error_type;


      //--------------------------------------------------------------------
      // DBI()
      //   constructor.
      //
      //--------------------------------------------------------------------
      function DBI($db)
      {
         global $_HCG_GLOBAL;
         
         $this->db_type = $_HCG_GLOBAL["db"][$db]["type"];
         $this->db_host = $_HCG_GLOBAL["db"][$db]["host"];
         $this->db_user = $_HCG_GLOBAL["db"][$db]["user"];
         $this->db_pass = $_HCG_GLOBAL["db"][$db]["pass"];
         $this->db_name = $_HCG_GLOBAL["db"][$db]["name"];
         
         $this->connect();
         if ($this->connected == TRUE) {
             // set default mode for all resultset
             $this->dbh->setFetchMode(ADODB_FETCH_NUM);
         } 
      }

      //--------------------------------------------------------------------
      // connect()
      //   connect to the database.
      //
      //--------------------------------------------------------------------
      function connect()
      {
         $this->dbh = &ADONewConnection($this->db_type);
         
         $status = $this->dbh->PConnect($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

         if ($status == FALSE) { // a connection error has occurred
             $this->connected = FALSE;
             $this->error = $this->dbh->ErrorMsg();
         } else {
             $this->connected = TRUE;
         }

         return $this->connected;
      }

      //--------------------------------------------------------------------
      // isConnected()
      //   connect to the database.
      //
      //--------------------------------------------------------------------
      function isConnected()
      {
         return $this->connected;
      }

      //--------------------------------------------------------------------
      // disconnect()
      //   disconnect from the database.
      //
      //--------------------------------------------------------------------
      function disconnect()
      {
         if (isset($this->dbh)) {
            $this->dbh->Close();
            return 1;
         } else {
            return 0;
         }
      }

      //--------------------------------------------------------------------
      // query($statement, [$method])
      //   Executes the given SQL statement. If a method name is provided
      //   it calls that method with the $statement as a parameter.
      //
      //--------------------------------------------------------------------
      function query($statement, $method = "")
      {
         if ($method != "") {
            if (method_exists($this->dbh, $method)) {
               $result = $this->dbh->{$method}($statement);
            } else {
               $this->setError("Requested method is not available.");
               return null;
            }
         } else {
            $result = $this->dbh->Execute($statement);
         }
         if ($result == FALSE) {
            $this->setError("ADODB: ".$this->dbh->ErrorMsg());
            return FALSE;
         } else {
            return $result;
         }
      }

      //--------------------------------------------------------------------
      // setError()
      //
      //--------------------------------------------------------------------
      function setError($msg = null)
      {
          global $TABLE_DOES_NOT_EXIST, $TABLE_UNKNOWN_ERROR;
          $this->error = $msg;
          if (strpos($msg, 'no such table')) {
             $this->error_type = $TABLE_DOES_NOT_EXIST;
          } else {
             $this->error_type = $TABLE_UNKNOWN_ERROR;
          }
      }

      //--------------------------------------------------------------------
      // isError()
      //
      //--------------------------------------------------------------------
      function isError()
      {
         return (!empty($this->error)) ? 1 : 0;
      }

      //--------------------------------------------------------------------
      // isErrorType()
      //
      //--------------------------------------------------------------------
      function isErrorType($type = null)
      {
          return ($this->error_type == $type) ? 1 : 0;
      }

      //--------------------------------------------------------------------
      // getError()
      //
      //--------------------------------------------------------------------
      function getError()
      {
          return $this->error;
      }

      //--------------------------------------------------------------------
      // resetError()
      //
      //--------------------------------------------------------------------
      function resetError()
      {
          $this->error = "";
          return TRUE;
      }

      //--------------------------------------------------------------------
      // quote()
      //
      //--------------------------------------------------------------------
      function quote($str)
      {
          return "'" . $str . "'";
      }

      //--------------------------------------------------------------------
      // apiVersion()
      //
      //--------------------------------------------------------------------
      function apiVersion()
      {
         return $this->$VERSION;
      }
   }

?>
