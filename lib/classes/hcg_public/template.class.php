<?php

global $_HCG_GLOBAL;
global $pcConfig;
require_once($_HCG_GLOBAL['smarty_dir'] . "/Smarty.class.php");

class HCG_Smarty extends Smarty {

   // variables used by the enableFormsess function
   var $fs_root = '';
   var $fs_plugins = '';
   var $_fs_enabled = false;


   //-----------------------------------------------------------------------
   // HCG_Smarty
   //   Constructor function. Sets all the variables for the Smarty class.
   //
   //-----------------------------------------------------------------------

   function HCG_Smarty() {

      global $_HCG_GLOBAL;

      // call constructor
      $this->Smarty();

      $this->template_dir   = $_HCG_GLOBAL['template_dir'];
      $this->compile_dir    = $_HCG_GLOBAL['template_dir'].'_c';
      $this->compile_check  = true; // for development only
      $this->force_compile  = true; // for debugging only
      $this->config_dir     = $_HCG_GLOBAL['lib_dir'] . '/configs';
      $this->plugins_dir[]  = $_HCG_GLOBAL['smarty_dir'] . '/plugins';
      $this->caching        = 0;
      $this->cache_dir      = $_HCG_GLOBAL['lib_dir'] . '/cache';
      $this->debugging_ctrl = 'URL';
      
      // PointComma special plugin
      $this->register_function("errorDisplay", "smarty_function_display_error");
      
      $this->assign("_HCG_GLOBAL", $_HCG_GLOBAL);
   }


   //-----------------------------------------------------------------------
   // setTplPath
   //   Sets the template path based on the current site ID.
   //
   //-----------------------------------------------------------------------

   function setTplPath ($template_file)
   {
      global $_HCG_GLOBAL;

      $site_dir = $_HCG_GLOBAL['doc_root_dir']."/".$_HCG_GLOBAL['local_tpl_dir'];

      $site_tpl = $site_dir . "/" . $template_file;

      if (file_exists($site_tpl)) {

         $this->template_dir = $site_dir;
         $this->compile_dir = $site_dir . "_c";

      }
   }


   //-----------------------------------------------------------------------
   // enableFormsess
   //   Required by the Formsess class. This method has to be called (once)
   //   before formsess features are used.
   //
   //-----------------------------------------------------------------------

   function enableFormsess() 
   {
      // checks for the existence of formsess' main class
      if (!class_exists('formsess')) {
         if (!require $this->fs_root . DIRECTORY_SEPARATOR . 'formsess.class.php') {
            $this->trigger_error("Unable to include {$this->fs_root}/formsess.class.php; formsess is not available");
            return false;
         }
         require $this->fs_root . DIRECTORY_SEPARATOR . 'fs_filter.class.php';
      }
    
      // checks for the existence of the plugins directory 
      if (empty($this->fs_plugins)) {
         $this->fs_plugins = $this->fs_root . '/smarty_plugins';
      }
      if (!is_dir($this->fs_plugins)) {
         $this->trigger_error("Unable to open formsess plugins repository. Check fs_plugins");
         return false;
      }

      // load the prefilters
      if (!$this->_fs_enabled) {
         $this->plugins_dir[] = $this->fs_plugins;
         //$this->plugins_dir = $this->fs_plugins;
         $this->load_filter('pre', 'fs');
         $this->_fs_enabled = true;
      }
   }

}


?>