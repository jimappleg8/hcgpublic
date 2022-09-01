<?php

/*////////////////////////////////////
//
// User authentication functions
//
////////////////////////////////////*/

function verifyCredential($userName,$password) {
  global $pcConfig;
  //verify user credential
  if (empty($password)) {
    return false;
  }
  $password=md5($password);
  $credentialQuery=pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix'])."webusers` WHERE userName='".addslashes($userName)."' && password='$password' && status=3");
  if (($credentialQuery) && count($credentialQuery)==1) {
    // user credential is valid
    $userName=stripslashes($userName);
    return $userName;
  }

  //if empty($userName) or empty($password)or userName/password not match
  //sleep(5);
  return false;
}

function generateClearance($userName=false) {
  global $pcConfig;
  //generate current user clearance and register clearance object
  $clearance['generatedOn'] = time();
  if (($userName=='anonymous')||(!$userName)) {
    // if user is anonymous then generate anonymous clearance
    $clearance['userName']='';
    $clearance['rights']= array();
    $clearance['isFrameworkMgr']=false;
    $clearance['isSupervisor']= false;
    $clearance['isModuleMgr']= array();
    $clearance['isModuleSupervisor']= array();
  } else {
    //if user pass verifyCredential then generate user correspondent clearance
    $clearance['userName']=$userName;
    $clearance['isFrameworkMgr']=false;
    $clearance['isSupervisor']=false;
    $clearance['isModuleMgr']= array(); // added to remove notice
    $clearance['isModuleSupervisor']= array(); // added to remove notice
    if ($userQuery=pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix'])."webusers` WHERE userName='".addslashes($userName)."' && status=3")) {
      //get user from webusers to see he is framework manager or supervisor
      $currentUser = $userQuery[0];
      $clearance['isFrameworkMgr'] = ($currentUser['isFrameworkMgr']) || ($clearance['isFrameworkMgr']);
      $clearance['isSupervisor'] = ($currentUser['isSupervisor']) || ($clearance['isSupervisor']);
    } else {
      // check if password exists: throw error if empty
      return generateClearance('anonymous');
    }
    $rights = array();
    if ($assignmentsQuery=pcdb_select('SELECT `'.addslashes($pcConfig['dbPrefix']).'roles`.* FROM `'.addslashes($pcConfig['dbPrefix']).'roles`, `'.addslashes($pcConfig['dbPrefix']).'assignments` WHERE userName=\''.addslashes($userName).'\' && `'.addslashes($pcConfig['dbPrefix']).'assignments`.roleId=`'.addslashes($pcConfig['dbPrefix']).'roles`.roleId')) {
      // Get all roles user is assigned to
      foreach($assignmentsQuery as $currentRole) {
        // Process each role
        $moduleId = $currentRole['moduleId'];
				if ($moduleId != '') {
					// Now set user rights for the role's module
          $clearance['isModuleMgr'][$moduleId] = (
						$currentRole['isModuleMgr']
						|| isset($clearance['isModuleMgr'][$moduleId])
					);
          $clearance['isModuleSupervisor'][$moduleId] = (
						$currentRole['isModuleSupervisor']
						|| isset($clearance['isModuleSupervisor'][$moduleId])
					);
				} else {
          $clearance['isModuleMgr'][$moduleId] = false;
          $clearance['isModuleSupervisor'][$moduleId] =false;
				}
        // Now get user authorizations per role
        if ($authorizationsQuery=pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'authorizations` WHERE roleId='.addslashes($currentRole['roleId']))) {
          foreach($authorizationsQuery as $arrayRights) {
            //get user authorizations from authorizations per role per type
            $typeId = $arrayRights['typeId'];
						//get the hightest value
						$rights[$typeId] = max($arrayRights['writeLevel'], (isset($rights[$typeId])?$rights[$typeId]:0));
          }
        }
      }
    }
    // End dealing with assignments
		$clearance['rights']= $rights;

    if ($clearance['isFrameworkMgr'] || $clearance['isSupervisor']) {
      // Overrides role-based rights for framework manager and supervisor
      if ($allModules = pcdb_select('SELECT moduleId FROM `'.addslashes($pcConfig['dbPrefix']).'modules`')) {
        foreach ($allModules as $oneModule) {
          $moduleId = $oneModule['moduleId'];
          if ($clearance['isFrameworkMgr']) {
            // Creates full module manager rights for framework manager
            $clearance['isModuleMgr'][$moduleId] = true;
          }
          if ($clearance['isSupervisor']) {
            // Creates full module supervisor rights for supervisor
            $clearance['isModuleSupervisor'][$moduleId] = true;
          }
        }
      }
    }
    // End dealing with framework manager and supervisor

    // Now grants managers and supervisors full writing rights to the types in their modules
    foreach ($clearance['isModuleMgr'] as $mgrModuleId => $mgrModuleAuth) {
      // First, managers
      if ($mgrModuleAuth) {
        if ($typesForMgr = pcdb_select('SELECT typeId FROM `'.addslashes($pcConfig['dbPrefix']).'types` WHERE moduleId=\''.addslashes($mgrModuleId).'\'')) {
          foreach($typesForMgr as $oneType) {
            $clearance['rights'][$oneType['typeId']] = 3;
          }
        }
      }
    }
    foreach ($clearance['isModuleSupervisor'] as $supModuleId => $supModuleAuth) {
      // Then supervisors
      if ($supModuleAuth) {
        if ($typesForSuperv = pcdb_select('SELECT typeId FROM `'.addslashes($pcConfig['dbPrefix']).'types` WHERE moduleId=\''.addslashes($supModuleId).'\'')) {
          foreach($typesForSuperv as $oneType) {
            $clearance['rights'][$oneType['typeId']] = 3;
          }
        }
      }
    }
    // End dealing with extended writing rights
  }
  return $clearance;
}

function pcLogin($strUserName='anonymous',$strPassword='') {
    global $pcConfig;

    // lets consider that verifyCredential is secured enough on $strUsername sanitation
    // TODO : check it really

    // Credential accepted
    if ($strTempId = verifyCredential($strUserName,$strPassword)) {
      $clearance=generateClearance($strTempId);
      $_SESSION['clearance'] = $clearance;
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Logged user in as '.\$strUserName,3)");
      return true;
    }
    elseif ($pcConfig['anonymousLogin'] and ($strUserName == 'anonymous')){
      $clearance=generateClearance('anonymous');
      $_SESSION['clearance'] = $clearance;
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'AnonymousLogged',3)");
	  return true;
    }
    else
    { //login failed
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Login Failed: Incorrect user name or password',3,'User name: '.\$strUserName.', pcLoginPassword: '.\$strPassword)");
      trigger_error('Login failed: incorrect user name or password');
      return false;
    }
  }

function pcRegenerateClearance() {
  global $pcConfig;
  if (pcIsLogged()) {
    $clearance=generateClearance($_SESSION['clearance']['userName']);
    $_SESSION['clearance'] = $clearance;
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Clearance Reloaded',5)");
    return true;
  }
  else {
    return false;
  }
}

function pcIsLogged() {
  global $pcConfig;
  if (isset($_SESSION['clearance'])) {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'IsloggedTrue',1,'This User is logged:".$_SESSION['clearance']['userName']."')");
    return $_SESSION['clearance']['userName'];
  }
  else {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'IsloggedFalse',1)");
    return false;
  }
}

function pcLogout() {
  global $pcConfig;

  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'LoggingOut',3)");
  // Unset all of the session variables.
  unset($_SESSION);

  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (isset($_COOKIE[session_name()])) {
    $CookieInfo = session_get_cookie_params();
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Cookie Found, delete it',1)");
    if ( (empty($CookieInfo['domain'])) && (empty($CookieInfo['secure'])) ) {
       setcookie(session_name(), '', time()-3600, $CookieInfo['path']);
    } elseif (empty($CookieInfo['secure'])) {
       setcookie(session_name(), '', time()-3600, $CookieInfo['path'], $CookieInfo['domain']);
    } else {
       setcookie(session_name(), '', time()-3600, $CookieInfo['path'], $CookieInfo['domain'], $CookieInfo['secure']);
    }
    unset($_COOKIE[session_name()]);
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Cookie deleted',1)");
  }
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement', 'Destroy Session',1)");
  session_destroy();
}

// end User authentication functions

?>