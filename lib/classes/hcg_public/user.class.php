<?php

// Extends the patUser class to work with the hcgPublic system. I've 
// created this file so that I don't have to modify the patUser calss 
// directly.

global $_HCG_GLOBAL;
require_once($_HCG_GLOBAL['patuser_dir'] . "/include/patUser.php");

class HCG_patUser extends patUser {

   //-----------------------------------------------------------------------
   // HCG_patUser
   //   Constructor function. Sets all the variables for the patUser class.
   //
   //-----------------------------------------------------------------------

   function HCG_patUser($useSessions = true, $sessionVar = "patUserData", $userIdSequence = "patUserSequence")
   {
      global $_HCG_GLOBAL;

      // call constructor
      $this->__construct( $useSessions, $sessionVar, $userIdSequence );
      
      $this->loginTemplate = "patUserLogin.tpl";
      $this->unauthorizedTemplate = "patUserUnauthorized.tpl";
   }
   
   //-----------------------------------------------------------------------
   // __construct
   //   I'm bypassing this to remove the session_start call which is 
   //   already called by config.inc.php
   //
   //-----------------------------------------------------------------------

   	function __construct( $useSessions = true, $sessionVar = "patUserData", $userIdSequence = "patUserSequence" )
	{
		$this->useSessions		=	$useSessions;
		$this->sessionVar		=	$sessionVar;
		$this->userIdSequence	=	$userIdSequence;

		if( $this->useSessions )
		{
			//session_start();

			//	check, whether register globals is enabled
			if( ini_get( "register_globals" ) )
			{
				session_register( $this->sessionVar );
				if( !isset( $GLOBALS[$this->sessionVar] ) )
					$GLOBALS[$this->sessionVar]		=	array();
				$this->sessionData		=	&$GLOBALS[$this->sessionVar];
			}
			//	register globals is off, session_register is useless :-(
			else
			{
				if( isset( $_SESSION ) )
				{
					if( !isset( $_SESSION[$this->sessionVar] ) )
						$_SESSION[$this->sessionVar]	=	array();
					$this->sessionData		=	&$_SESSION[$this->sessionVar];
				}
				else
				{
					if( !isset( $GLOBALS["HTTP_SESSION_VARS"][$this->sessionVar] ) )
						$GLOBALS["HTTP_SESSION_VARS"][$this->sessionVar]	=	array();
					$this->sessionData		=	&$GLOBALS["HTTP_SESSION_VARS"][$this->sessionVar];
				}
			}
		}
	}

   //-----------------------------------------------------------------------
   // setTemplate()
   //   rewrites this function to use Smarty templates
   //
   //-----------------------------------------------------------------------
   function setTemplate(&$tmpl)
   {
      $this->tmpl = &$tmpl;

      // check whether template is patTemplate object
      if( get_class( $this->tmpl ) == "smarty" || get_parent_class( $this->tmpl ) == "smarty" ) {
         $this->useTemplate = true;
      }
   }
   
   //-----------------------------------------------------------------------
   // requireAuthentication()
   //   rewrites this function to use Smarty templates
   //
   //-----------------------------------------------------------------------
   function requireAuthentication($mode = "displayLogin", $displayOnError = true)
   {
      if( $this->isAuthenticated() ) {
         $uid = $this->getUid();

         // inform the authentication handler about the logged in user
         if (is_object($this->authHandler) && method_exists($this->authHandler, "patUserSetUid")) {
            $this->authHandler->patUserSetUid($uid);
         }
         return $uid;
      }

      switch (strtolower($mode)) {

         case "displaylogin":
            $displayForm = false;

            // get authentication data
            if ($this->useTemplate) {
               $authData = $this->getAuthVars( "post" );
            } else {
               if ($this->getSessionValue("_patUserLoggedOut")) {
                  $this->sendAuthHeader();
               } else {
                  $authData = $this->getAuthVars( "http" );
               }
            }

            // check, whether data is correct
            if (isset($authData[$this->authFields["username"]])
               || isset($authData[$this->authFields["passwd"]])
               || isset($authData[$this->actionVar]))
            {
               if (strlen($authData[$this->authFields["username"]]) < 1 ) {
                  $displayForm = true;
                  $this->setError( patUSER_NEED_USERNAME );
               }
               if (strlen( $authData[$this->authFields["passwd"]] ) < 1) {
                  $displayForm = true;
                  $this->setError(patUSER_NEED_PASSWD);
               }
               if( !$displayForm ) {
                  $data = array($this->authFields["username"] => $authData[$this->authFields["username"]],
                                $this->authFields["passwd"]   => $authData[$this->authFields["passwd"]]);
                  $uid = $this->authenticate( $data );

                  if( !is_int( $uid ) ) {
                     $displayForm   =   true;
                  } else {
                     $this->storeSessionValue("_patUserLoginAttempts", 0);
                     return $uid;
                  }
               }
            } else {
               $displayForm = true;
            }

            // check, whether form should be displayed
            if ($displayForm) {
               $loginAttempts = $this->getSessionValue( "_patUserLoginAttempts" );
               if (!$loginAttempts) {
                  $loginAttempts = 0;
               }
               if ($this->maxLoginAttempts > 0) {
                  if( $loginAttempts >= $this->maxLoginAttempts ) {
                     if ($this->unauthorizedURL) {
                        header( "Location:".$this->unauthorizedURL );
                        exit;
                     }
                     if ($this->useTemplate) {
// start change from original
                        $this->tmpl->assign("PATUSER_ACTION", $this->actionVar);
                        $this->tmpl->assign("PATUSER_REALM", $this->realm);
                        $this->tmpl->assign("PATUSER_LOGINATTEMPTS", $loginAttempts);
                        $this->tmpl->setTplPath($this->unauthorizedTemplate);
                        echo $this->tmpl->fetch($this->unauthorizedTemplate);
// end change from original
                     }
                     exit;
                  }
               }

               $loginAttempts++;
               $this->storeSessionValue( "_patUserLoginAttempts", $loginAttempts );

               if( $this->useTemplate ) {
                  $form_data = $this->authFields;
                  if (isset($authData[$form_data["username"]])) {
                     $form_data["username_value"] = $authData[$form_data["username"]];
                  } else {
                     $form_data["username_value"] = "";
                  }
                  if (isset($authData[ $form_data["passwd"]])) {
                     $form_data["passwd_value"] = $authData[$form_data["passwd"]];
                  } else {
                     $form_data["passwd_value"] = "";
                  }
// start change from original
                  //echo "<pre>"; print_r($form_data); echo "</pre>";
                  $this->tmpl->assign("form_data", $form_data);
                  $this->tmpl->assign("PATUSER_ACTION", $this->actionVar);
                  $this->tmpl->assign("PATUSER_SELF", $this->getSelfUrl());
                  $this->tmpl->assign("PATUSER_REALM", $this->realm);
                  $this->tmpl->assign("PATUSER_LOGINATTEMPTS", ($loginAttempts-1));
// end change from original

                  if ($displayOnError) {
                     $errors = $this->getAllErrors();
                     if (count($errors)) {
// start change from original
                        $this->tmpl->assign("iserror", "yes");
                        $this->tmpl->assign("errors", $errors);
// end change from original
                     }
                  }
// start change from original
                  $this->tmpl->setTplPath($this->loginTemplate);
                  echo $this->tmpl->fetch($this->loginTemplate);
// end change from original
                  exit;
               } else {
                  // no template object, just use HTTP authentication
                  $this->sendAuthHeader();
               }
               return false;
            }
            break;
            
         case "callauthhandler":
            if (!is_object($this->authHandler)) {
               die ("patUser fatal error: called requireAuthentication without callback-object; use setAuthHandler()" );
            }
            // realm
            if (method_exists($this->authHandler, "patUserSetRealm")) {
               $this->authHandler->patUserSetRealm( $this->realm );
            }
            // get authentication data
            $authData = $this->authHandler->patUserGetAuthData();
            $uid = $this->authenticate( $authData );

            if ($uid) {
               // report uid to auth handler
               if (method_exists($this->authHandler, "patUserSetUid")) {
                  $this->authHandler->patUserSetUid($uid);
               }
               return $uid;
            } else {
               // report errors to auth handler
               if (method_exists($this->authHandler, "patUserSetErrors")) {
                  $this->authHandler->patUserSetErrors( $this->getAllErrors() );
               }
               return false;
            }
            break;
         
         // case exit
         default:
            exit;
            break;
      }
   }

}

?>