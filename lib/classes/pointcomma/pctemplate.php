<?php
/**
 * Project PointComma - Smarty Templating Management - pcTemplate.php
 * 
 * This lib is just an encapsulation for pc of the smarty Template engine
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 28 feb 2005
 * @version 0.1
 * 
 */
 
//load Smarty engine
require($pcConfig['smartyPath'].'Smarty.class.php');

//called  Smarty plugins for PointComma 
require_once($pcConfig['includePath'].$pcConfig['functionFolder'].'pcsmarty_plugin.php');

Class pcSmarty extends Smarty {
  function pcSmarty() {
    global $pcConfig;
     
    // Class Builder.
    // Automatiquely launch at class instanciation    
    $this->Smarty();
     
    $this->template_dir = $pcConfig['wwwPath'].'templates/';
    $this->compile_dir = $pcConfig['includePath'].'templates_c/';
    $this->config_dir = $pcConfig['wwwPath'].'configs/';
    $this->cache_dir = $pcConfig['includePath'].'cache/';
    $this->register_function("errorDisplay", "smarty_function_display_error");
    //$this->debugging=true;
    $this->caching = false;
    }
      
}

?>
