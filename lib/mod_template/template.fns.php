<?php

require_once("template.class.php");


// ------------------------------------------------------------------------
// TAG: include_tpl
//
// ------------------------------------------------------------------------

function include_tpl($tpl_file, $data_array = array(), $data_name = "")
{
   global $_HCG_GLOBAL;
   
   $t = new HCG_Smarty;

   if (!empty($data_array)) {
      if (empty($data_name)) {
         list($data_name, $extension) = explode(".", $tpl_file);
         $t->assign($data_name, $data_array);
      } else {
         $t->assign($data_name, $data_array);
      }
   }
   
   $t->setTplPath($tpl_file);
   echo $t->fetch($tpl_file);
}

// ------------------------------------------------------------------------
// TAG: include_tpl_array
//   allows for multiple assign statements. The data array is an array in
//   the form 'label' => data
// ------------------------------------------------------------------------

function include_tpl_array($tpl_file, $data_array)
{
   global $_HCG_GLOBAL;
   
   $t = new HCG_Smarty;

   foreach ($data_array as $key => $value) {
      $t->assign($key, $value);
   }
   
   $t->setTplPath($tpl_file);
   echo $t->fetch($tpl_file);
}

?>