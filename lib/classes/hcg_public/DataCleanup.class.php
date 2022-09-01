<?php

// =========================================================================
// DataCleanup.class.php
//   from "Secure PHP Development" by Mohammed J. Kabir
//   used unmodified except for changing some comments
// =========================================================================

   class DataCleanup {

      //--------------------------------------------------------------------
      // DataCleanup()
      //   constructor. Does nothing.
      //
      //--------------------------------------------------------------------
      function DataCleanup()
      {

      }

      //--------------------------------------------------------------------
      // cleanup_none()
      //   returns the string with no changes
      //
      //--------------------------------------------------------------------
      function cleanup_none ($str)
      {
         return $str;
      }

      //--------------------------------------------------------------------
      // cleanup_ucwords()
      //   uppercase first character of each word in string
      //
      //--------------------------------------------------------------------
      function cleanup_ucwords ($str)
      {
         return ucwords($str);
      }

      //--------------------------------------------------------------------
      // cleanup_ltrim()
      //   remove all white spaces from left of the string
      //
      //--------------------------------------------------------------------
      function cleanup_ltrim ($str)
      {
        return ltrim($str);
      }

      //--------------------------------------------------------------------
      // cleanup_rtrim()
      //   remove all white spaces from the right of the string
      //
      //--------------------------------------------------------------------
      function cleanup_rtrim ($str)
      {
         return rtrim($str);
      }

      //--------------------------------------------------------------------
      // cleanup_trim()
      //   remove all white spaces from the left and right of the string
      //
      //--------------------------------------------------------------------
      function cleanup_trim ($str)
      {
         return trim($str);
      }

      //--------------------------------------------------------------------
      // cleanup_lower()
      //   convert to lowercase of the given string
      //
      //--------------------------------------------------------------------
      function cleanup_lower($str)
      {
     	 return strtolower($str);
      }
     
      //--------------------------------------------------------------------
      // cleanup_htmlentities()
      //   convert html entities
      //
      //--------------------------------------------------------------------
      function cleanup_htmlentities($str)
      {
     	 return htmlentities($str, ENT_QUOTES);
      }
     
      //--------------------------------------------------------------------
      // cleanup_htmlentitydecode()
      //   convert html entities
      //
      //--------------------------------------------------------------------
      function cleanup_htmlentitydecode($str)
      {
     	 return html_entity_decode($str, ENT_QUOTES);
      }
     
      //--------------------------------------------------------------------
      // cleanup_addslashes()
      //   add slashes to any quote characters
      //
      //--------------------------------------------------------------------
      function cleanup_addslashes($str)
      {
     	 return addslashes($str);
      }

      //--------------------------------------------------------------------
      // cleanup_stripslashes()
      //   strip slashes to any quote characters
      //
      //--------------------------------------------------------------------
      function cleanup_stripslashes($str)
      {
     	 return stripslashes($str);
      }

   } //class


?>
