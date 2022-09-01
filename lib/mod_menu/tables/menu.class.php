<?php

require_once 'table.class.php';

class Menu extends Default_Table {

   //-----------------------------------------------------------------------
   // class constructor
   //-----------------------------------------------------------------------
   function Menu()
   {
      $this->tablename       = 'menu';
      $this->dbname          = 'hcg_public';
        
      // call this method to get original field specifications
      // (note that they may be modified at runtime)
      $this->fieldspec = $this->getFieldSpec_original();
        
   }
    
   //-----------------------------------------------------------------------
   // getFieldSpec_original
   //   set the specifications for this database table
   //-----------------------------------------------------------------------
   function getFieldSpec_original () 
   {
      $this->primary_key      = array();
      $this->unique_keys      = array();
      $this->child_relations  = array();
      $this->parent_relations = array();
        
      // build array of field specifications
      $fieldspec['MenuID']          = array('type' => 'integer',
                                            'size' => 11,
                                            'pkey' => 'y',
                                            'required' => 'y');
        
      $fieldspec['Parent']          = array('type' => 'integer',
                                            'size' => 11);
        
      $fieldspec['Lft']             = array('type' => 'integer',
                                            'size' => 11);
        
      $fieldspec['Rgt']             = array('type' => 'integer',
                                            'size' => 11);
        
      $fieldspec['SiteID']          = array('type' => 'string',
                                            'size' => 2,
                                            'pkey' => 'y',
                                            'required' => 'y');
        
      $fieldspec['MenuText']        = array('type' => 'string',
                                            'size' => 63,
                                            'required' => 'y');
        
      $fieldspec['LinkText']        = array('type' => 'string',
                                            'size' => 63,
                                            'required' => 'n');
        
      $fieldspec['Description']     = array('type' => 'string',
                                            'size' => 65535,
                                            'required' => 'n',
                                            'control' => 'multiline',
                                            'cols' => 70,
                                            'rows' => 15);
        
      $fieldspec['URL']             = array('type' => 'string',
                                            'size' => 255,
                                            'required' => 'y');
        
      $fieldspec['Sort']            = array('type' => 'integer',
                                            'size' => 11);
        
      $fieldspec['NoDelete']        = array('type' => 'integer',
                                            'size' => 1);
        
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
      $this->primary_key                   = array('MenuID');

      // default sort sequence 
      $this->default_orderby               = 'menu.Sort';

      return $fieldspec;

   }

} // end class

?>
