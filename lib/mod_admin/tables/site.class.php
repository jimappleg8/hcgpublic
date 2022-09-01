<?php

require_once 'table.class.php';

class Site extends Default_Table {

   //-----------------------------------------------------------------------
   // class constructor
   //-----------------------------------------------------------------------
   function Site()
   {
      $this->tablename       = 'site';
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
      $fieldspec['SiteID']          = array('type' => 'string',
                                            'size' => 2,
                                            'pkey' => 'y',
                                            'required' => 'y');
        
      $fieldspec['BrandName']       = array('type' => 'string',
                                            'size' => 127,
                                            'required' => 'y');
        
      $fieldspec['LiveURL']         = array('type' => 'string',
                                            'size' => 127,
                                            'required' => 'n');
        
      $fieldspec['LiveDir']         = array('type' => 'string',
                                            'size' => 25,
                                            'required' => 'n');
        
      $fieldspec['StageURL']        = array('type' => 'string',
                                            'size' => 127,
                                            'required' => 'n');
        
      $fieldspec['StageDir']        = array('type' => 'string',
                                            'size' => 25,
                                            'required' => 'n');
        
      $fieldspec['DevURL']          = array('type' => 'string',
                                            'size' => 127,
                                            'required' => 'n');
        
      $fieldspec['DevDir']          = array('type' => 'string',
                                            'size' => 25,
                                            'required' => 'n');
        
      $fieldspec['StoreID']         = array('type' => 'string',
                                            'size' => 128);

      $fieldspec['AdmMenuRoot']     = array('type' => 'integer',
                                            'size' => 11);

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
      $this->primary_key            = array('SiteID');

      // default sort sequence 
      $this->default_orderby        = 'site.SiteID';

      return $fieldspec;

   }
        
    
} // end class

?>
