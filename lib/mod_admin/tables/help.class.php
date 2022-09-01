<?php

require_once 'table.class.php';

class Help extends Default_Table {

   //-----------------------------------------------------------------------
   // class constructor
   //-----------------------------------------------------------------------
   function Help()
   {
      $this->tablename       = 'help';
      $this->dbname          = 'hcg_public';
        
      // call this method to get original field specifications
      // (note that they may be modified at runtime)
      $this->fieldspec = $this->getFieldSpec_original();
        
   }

   //-----------------------------------------------------------------------
   // getFieldSpec_original
   //   set the specifications for this database table
   //-----------------------------------------------------------------------
   function getFieldSpec_original() 
   {
      $this->primary_key      = array();
      $this->unique_keys      = array();
      $this->child_relations  = array();
      $this->parent_relations = array();
        
      // build array of field specifications
      $fieldspec['TaskID']          = array('type' => 'string',
                                            'size' => 64,
                                            'pkey' => 'y',
                                            'required' => 'y',
                                            'uppercase' => 'y');
        
      $fieldspec['HelpText']        = array('type' => 'string',
                                            'size' => 65535,
                                            'required' => 'y',
                                            'control' => 'multiline',
                                            'cols' => 70,
                                            'rows' => 15);
        
      $fieldspec['CreatedDate']     = array('type' => 'datetime',
                                            'size' => 20,
                                            'required' => 'y',
                                            'default' => '2003-01-01 12:00:00',
                                            'autoinsert' => 'y',
                                            'noedit' => 'y');
        
      $fieldspec['CreatedBy']       = array('type' => 'string',
                                            'size' => 16,
                                            'autoinsert' => 'y',
                                            'noedit' => 'y');
        
      $fieldspec['RevisedDate']     = array('type' => 'datetime',
                                            'size' => 20,
                                            'autoupdate' => 'y',
                                            'noedit' => 'y');
        
      $fieldspec['RevisedBy']       = array('type' => 'string',
                                            'size' => 16,
                                            'autoupdate' => 'y',
                                            'noedit' => 'y');
        
      // primary key details 
      $this->primary_key            = array('TaskID');
        
      // unique key details 
      $this->unique_keys            = array();
        
      // child relationship details 
      $this->child_relations        = array();
        
      // parent relationship details 
      $this->parent_relations       = array();
        
      // default sort sequence 
      $this->default_orderby        = 'TaskID';
        
      return $fieldspec;
        
   }
    

   //=======================================================================
   // abstract methods which may be customised as required
   //=======================================================================

   //-----------------------------------------------------------------------
   // _cm_commonValidation
   //   perform validation that is common to INSERT and UPDATE.
   //-----------------------------------------------------------------------
   function _cm_commonValidation ($fieldarray, $originaldata) 
   {
      // replace HTML line breaks with newline
      $pattern[] = "<br>";
      $pattern[] = "<br/>";
      $pattern[] = "<br />";

      $replacement = "\n";

      $fieldarray = str_replace($pattern, $replacement, $fieldarray);

      return $fieldarray;
   }


   //-----------------------------------------------------------------------
   // _cm_getInitialData
   //   Perform custom processing for the getInitialData method.
   //   $fieldarray contains data from the initial $where clause.
   //-----------------------------------------------------------------------
   function _cm_getInitialData ($fieldarray) 
   {
      unset($this->fieldspec['TaskID']['noedit']);
      unset($this->fieldspec['HelpText']['noedit']);

      return $fieldarray;
   }


   //-----------------------------------------------------------------------
   // _cm_pre_getData
   //   perform custom processing before database record(s) are retrieved.
   //-----------------------------------------------------------------------
   function _cm_pre_getData ($where, $where_array) 
   {
      if (isset($_POST['retrieve'])) {
         $array1 = where2array($where);
         $array2 = indexed2assoc($array1);
         $array3 = stripOperators($array2);
         unset($array3['HelpText']);
         $where = array2where($array3);
         return $where;
      } // if
      
      return $where;
   }
 

} // end class

?>
