<?php
// User self-registration and email management functions

// pcCreateUser
// pcAssignProfile
// pcConfirmUserEmail
// pcConfirmPassword
// pcActivateUser
// pcDeleteNewUser
// pcSendEmailToken

$pcUserCreationMessages = array(
          // The messages below may be freely customized
          // Note that the files admin/sr*.php are also meant to be used
          // as the basis for a custom set of user management.
  'signup' => array(
    'subj' => 'PointComma email verification for site '.$pcConfig['siteName'],
    'msg' => "Hello %f %l,\n\nThe PointComma system for site ".$pcConfig['siteName']." would like to confirm that this is your email addresss. Please open the following URL in your browser to proceed:\n\n".$pcConfig['adminServer']."sremail.php?u=%u&t=%t\n\nBest regards,\n\n   -- The PointComma robot"
  ),
  'admin' => array(
    'subj' => 'PointComma user moderation request for site '.$pcConfig['siteName'],
    'msg' => "Hello,\n\nUser %u of the PointComma system for site ".$pcConfig['siteName']." would like to obtain further rights. Please open the following URL in your browser to proceed:\n\n".$pcConfig['adminServer']."sradmin.php?u=%u\n\nBest regards,\n\n   -- The PointComma robot"
  ),
  'confirm' => array(
    'subj' => 'PointComma user details for site '.$pcConfig['siteName'],
    'msg' => "Hello %f %l,\n\nYour registration with the PointComma system for site ".$pcConfig['siteName']." is confirmed. Please find below your connection details; keep them in a safe place, they are strictly private.\n\n   User name: %u\n   Password : %p\n\nBest regards,\n\n   -- The PointComma robot"
  ),
  'passwd' => array(
    'subj' => 'PointComma password reset for site '.$pcConfig['siteName'],
    'msg' => "Hello %f %l,\n\nYou have requested a password reset from the PointComma system for site ".$pcConfig['siteName'].". Please open the following URL in your browser to proceed:\n\n".$pcConfig['adminServer']."srpasswd.php?u=%u&t=%t\n\nBest regards,\n\n   -- The PointComma robot"
  )
);

function pcCreateUser($profileId, $userName, $password, $firstName, $lastName, $eMail, $profilePassword=false) {
  // Creates a new user according to the supplied profile

  // Returns true on success and false on failure or wrong input

  // The function itelf doesn't supply its reasons for failing
  // however it does populate the global error handling
  // with indications.
  // The new user is inserted in the webusers table with a random
  // emailToken, which is emailed for confirmation. If the profile
  // requires admin validation, status is set to 0; otherwise to 2.

  global $pcConfig;
  if ($profileId != 'admin') {
    $profileId = (int)$profileId;
  }
  $userName = addslashes(stripslashes($userName));
  $firstName = addslashes(stripslashes($firstName));
  $lastName = addslashes(stripslashes($lastName));
  $eMail = addslashes(stripslashes($eMail));

  // 1. Verify entry
  if (empty($userName)) {
    trigger_error('You must enter a user name.',ERROR);
    return false;
  }
  if (empty($password)) {
    trigger_error('You must enter a password.',ERROR);
    return false;
  }
  // Email validation is always required
  if (empty($eMail)) {
    trigger_error('You must enter a valid email address.',ERROR);
    return false;
  }
  
  //check if the mail is valid  
  if (!eregi("^[a-z0-9]+([_\\.-][a-z0-9]+)*"."@([a-z0-9]+([\.-][a-z0-9]{1,})+)*$",$eMail) ) {
    trigger_error("The email you've choosen is not a valid one",ERROR);
    return false;
  }

  // Verify user name validity:
  // at least 2 characters long, max 12
  // start and end with a letter or digit
  // in between - and _ are also allowed
  // no extended characters allowed
  if (!eregi('^[a-z0-9]{1}[a-z0-9_-]{0,10}[a-z0-9]{1}$', $userName)) {
    trigger_error("The user name you've chosen doesn't work. A user name must be composed of at least 2 and at max 12 characters, it can contain    upper- and lower-case letters, numbers, dashes (-) and underscores (_), but no diacritics (accents) or spaces. It must also start and end with a letter or a number.",ERROR);
    return false;
  }

  // 2. Verify profile
  if ($profileId=='admin' && $pcConfig['userSelfRegOpen']) {
    global $pcUserCreationMessages;
    $profile['confirmMsg'] = $pcUserCreationMessages['confirm']['msg'];
    $profile['confirmSubj'] = $pcUserCreationMessages['confirm']['subj'];
    $profile['adminMsg'] = $pcUserCreationMessages['admin']['msg'];
    $profile['adminSubj'] = $pcUserCreationMessages['admin']['subj'];
    $profile['adminEmail'] = $pcConfig['adminEmail'];
    $profile['requireAdminConfirm'] = $pcConfig['userModeration'];
  } else {
    if (empty($profilePassword)) {
      $passwordModif = ' IS NULL';
    } else {
      $passwordModif = '=\''.md5($profilePassword).'\'';
    }
    if ($rsProfile = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'profiles` WHERE profileId='.addslashes($profileId).' && password'.$passwordModif)) {
      $profile = $rsProfile[0];
    } else {
      trigger_error('System error: profile issue.',FATAL);
      return false;
    }
  }

  // 3. Verify if user name is available
  if ($userEmailTest = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE userName=\''.addslashes($userName).'\' || eMail=\''.addslashes($eMail).'\' || newEMail=\''.addslashes($eMail).'\'')) {
    if ($userEmailTest[0]['eMail'] == $eMail || $userEmailTest[0]['newEMail'] == $eMail) {
      trigger_error("The email address you've entered is already present in the system. Please enter another one.",ERROR);
    } else {
      trigger_error("The user name you've chosen is already used by someone else. Please choose another one.",ERROR);
    }
    return false;
  }
  if ($profile['requireAdminConfirm']) {
    $status = 0;
  } else {
    $status = 2;
  }
  if (!pcdb_query('INSERT INTO `'.addslashes($pcConfig['dbPrefix']).'webusers` (userName, password, status, firstName, lastName, createdBy, createdOn) VALUES (\''.addslashes($userName).'\', \''.md5($password).'\', '.addslashes($status).', \''.addslashes($firstName).'\', \''.addslashes($lastName).'\', \'_pcProfile_'.addslashes($profileId).'\', NOW())')) {
    trigger_error("System error: database issue.",FATAL);
    return false;
  }
  $randomToken = pcSendEmailToken($profileId, $userName, $eMail, $profilePassword);
  if (!$randomToken) {
    return false;
  }

  // 4. Send email to the admin email
  if ($profile['requireAdminConfirm']) {
    trigger_error('An email was sent to the moderating administrator.',WARNING);   
    $searchKeys = array('%u');
    $replaceKeys = array($userName);
    $message = str_replace($searchKeys, $replaceKeys, $profile['adminMsg']);
    $sentMessage = false;
    $countSends = 0;
    while (!$sentMessage && $countSends<5) {
      $countSends++;
      $sentMessage = mail($profile['adminEmail'], $profile['adminSubj'], $message, 'From: '.$profile['adminEmail']."\nDate: ".date('D, j M Y H:i:s O')."\nMime-Version: 1.0\nContent-Type: text/plain; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit\nX-Priority: 3 (Normal)\nX-PointComma-LoggedIP: ".$_SERVER['REMOTE_ADDR']."\nReturn-Path: ".$profile['adminEmail']);
    }
    if (!$sentMessage) {
      trigger_error("The confirmation email could not be sent. Please contact the site's administrator.",ERROR);   
      return false;
    } else {
      return true;
    }
  }

  // 5. Pass to profile-handling function
  if ($profileId != 'admin') {
    return pcAssignProfile($profileId, $userName, $profilePassword);
  } else {
    return true;
  }
}

function pcAssignProfile($profileId, $userName, $profilePassword=false) {
  // Grants users rights offered by a profile.

  // For now, is is not possible to call this function
  // as a standalone feature for existing users to
  // request more rights--it can only be used as a
  // part of the user self-registration procedure.

  // Returns true on success, false on failure.

  global $pcConfig;
  $profileId = (int)$profileId;

  // 1. Verify profile
  if (empty($profilePassword)) {
    $passwordModif = ' IS NULL';
  } else {
    $passwordModif = '=\''.md5($profilePassword).'\'';
  }
  if ($rsProfile = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'profiles` WHERE profileId='.addslashes($profileId).' && password'.$passwordModif)) {
    $profile = $rsProfile[0];
  } else {    
    trigger_error("System error: profile issue.",FATAL);   
    return false;
  }

  // 2. Assign roles to user
  // TODO move this to a buffer table, to wait for role-specific confirmation for existing users requesting an extra profile
  // and then skip email confirmation below
  $allRoles = explode(';', $profile['defaultRoles']);
  if (is_array($allRoles) && count($allRoles > 0) && $allRoles[0] > 0) {
    foreach ($allRoles as $roleId) {
      if (!pcdb_select('SELECT assignmentId FROM `'.addslashes($pcConfig['dbPrefix']).'assignments` WHERE roleId='.addslashes($roleId).' && userName=\''.addslashes($userName).'\'')) {
        pcdb_query('INSERT INTO `'.addslashes($pcConfig['dbPrefix']).'assignments` SET roleId='.addslashes($roleId).', userName=\''.addslashes($userName).'\'');
      }
    }
  }

  // 3. Perform profile-requested operations

  // 3.1 Send email to the user to ensure address validity
  /*
  TODO plug this in properly!!!
  if (!pcSendEmailToken($profileId, $userName, $eMail, $profilePassword)) {
    sleep(5);
    return false;
  }
  */
  if ($profile['requireAdminConfirm']) {
    // 3.2 Send email to the admin email
    trigger_error("An email was sent to the moderating administrator.",WARNING);
    $searchKeys = array('%u');
    $replaceKeys = array($userName);
    $message = str_replace($searchKeys, $replaceKeys, $profile['adminMsg']);
    $sentMessage = false;
    $countSends = 0;
    while (!$sentMessage && $countSends<5) {
      $countSends++;
      $sentMessage = mail($profile['adminEmail'], $profile['adminSubj'], $message, 'From: '.$profile['adminEmail']."\nDate: ".date('D, j M Y H:i:s O')."\nMime-Version: 1.0\nContent-Type: text/plain; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit\nX-Priority: 3 (Normal)\nX-PointComma-LoggedIP: ".$_SERVER['REMOTE_ADDR']."\nReturn-Path: ".$profile['adminEmail']);
    }
    if (!$sentMessage) {
      trigger_error("The confirmation email could not be sent. Please contact the site's administrator.",FATAL);
      return false;
    } else {
      return true;
    }
  }
  trigger_error("The selected user was successfully activated.",WARNING);
  return true;
}

function pcConfirmUserEmail($profileId, $userName, $password, $token, $profilePassword=false) {
  // Verifies the supplied information and activates
  // self-registered users, according to the selected profile.
  // Returns true on success and false on failure.

  global $pcConfig;
  if ($profileId != 'admin') {
    $profileId = (int)$profileId;
  }
  $token = (int)$token;

  // 1. Verify profile
  if ($profileId=='admin') {
    global $pcUserCreationMessages;
    $profile['confirmMsg'] = $pcUserCreationMessages['confirm']['msg'];
    $profile['confirmSubj'] = $pcUserCreationMessages['confirm']['subj'];
    $profile['adminEmail'] = $pcConfig['adminEmail'];
  } else {
    if (empty($profilePassword)) {
      $passwordModif = ' IS NULL';
    } else {
      $passwordModif = '=\''.md5($profilePassword).'\'';
    }
    if ($rsProfile = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'profiles` WHERE profileId='.addslashes($profileId).' && password'.$passwordModif)) {
      $profile = $rsProfile[0];
    } else {
      trigger_error("System error: profile issue.",FATAL);
      return false;
    }
  }

  // 2. Update entry if correct
  $rsUser = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE userName=\''.addslashes($userName).'\'');
  $user = $rsUser[0];
  if ($user['status'] > 3) {
    trigger_error("The selected user was not found.",ERROR);
    return false;
  }
  $newStatus = bindec(substr('000'.decbin($user['status']), -3, 2).'1');
  if ($user['activeSince'] == '0000-00-00 00:00:00') {
    $activeSince = ', activeSince=NOW()';
  }
  if (pcdb_update('UPDATE `'.addslashes($pcConfig['dbPrefix']).'webusers` SET emailToken=NULL, eMail=newEMail, newEMail=NULL, status='.$newStatus.$activeSince.' WHERE userName=\''.addslashes($userName).'\' && password=\''.md5($password).'\' && emailToken=\''.md5($token).'\'') == 1) {
    $rsUser = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE userName=\''.addslashes($userName).'\'');
    $user = $rsUser[0];
  } else {
    trigger_error("The selected user was not found.",ERROR);
    return false;
  }

  // 3. Perform profile-related operations
  trigger_error("An email was sent to your address to confirm its validity.",WARNING);
  $searchKeys = array('%p', '%u', '%f', '%l');
  $replaceKeys = array($password, $user['userName'], $user['firstName'], $user['lastName']);
  $message = str_replace($searchKeys, $replaceKeys, $profile['confirmMsg']);
  $sentMessage = false;
  $countSends = 0;
  while (!$sentMessage && $countSends<5) {
    $countSends++;
    $sentMessage = mail($user['eMail'], $profile['confirmSubj'], $message, 'From: '.$profile['adminEmail']."\nDate: ".date('D, j M Y H:i:s O')."\nMime-Version: 1.0\nContent-Type: text/plain; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit\nX-Priority: 3 (Normal)\nX-PointComma-LoggedIP: ".$_SERVER['REMOTE_ADDR']."\nReturn-Path: ".$profile['adminEmail']);
  }
  if (!$sentMessage) {
    trigger_error("The confirmation email could not be sent. Please contact the site's administrator.",FATAL);
    return false;
  } else {
    trigger_error("The selected email address was successfully confirmed.",WARNING);
    return true;
  }
}

function pcConfirmPassword($profileId, $userName, $password, $token, $profilePassword=false) {
  // Verifies the supplied information and changes the password
  // of users who've forgotten it, according to the selected profile.
  // Returns true on success and false on failure

  global $pcConfig;
  if ($profileId != 'admin') {
    $profileId = (int)$profileId;
  }
  $token = (int)$token;
  $userName = addslashes(stripslashes($userName));

  // 1. Verify profile
  if ($profileId=='admin') {
    global $pcUserCreationMessages;
    $profile['confirmMsg'] = $pcUserCreationMessages['confirm']['msg'];
    $profile['confirmSubj'] = $pcUserCreationMessages['confirm']['subj'];
    $profile['adminEmail'] = $pcConfig['adminEmail'];
  } else {
    if (empty($profilePassword)) {
      $passwordModif = ' IS NULL';
    } else {
      $passwordModif = '=\''.md5($profilePassword).'\'';
    }
    if ($rsProfile = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'profiles` WHERE profileId='.addslashes($profileId).' && password'.$passwordModif)) {
      $profile = $rsProfile[0];
    } else {     
      trigger_error("System error: profile issue.",FATAL);
      return false;
    }
  }

  // 2. Update entry if correct
  if (pcdb_update('UPDATE `'.addslashes($pcConfig['dbPrefix']).'webusers` SET emailToken=NULL, password=\''.md5($password).'\' WHERE userName=\''.addslashes($userName).'\' && emailToken=\''.md5($token).'\' && status=3 && newEMail IS NULL') == 1) {
    $rsUser = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE userName=\''.addslashes($userName).'\'');
    $user = $rsUser[0];
    setGlobal('lastAdminUpdateOn', time());
    setGlobal('lastAdminUpdateBy', '_pcUserLib');
  } else {
    trigger_error("The selected user was not found.",ERROR);
    return false;
  }

  // 3. Perform profile-related operations
  trigger_error("An email was sent to your address to confirm its validity.",WARNING);
  $searchKeys = array('%p', '%u', '%f', '%l');
  $replaceKeys = array($password, $user['userName'], $user['firstName'], $user['lastName']);
  $message = str_replace($searchKeys, $replaceKeys, $profile['confirmMsg']);
  $sentMessage = false;
  $countSends = 0;
  while (!$sentMessage && $countSends<5) {
    $countSends++;
    $sentMessage = mail($user['eMail'], $profile['confirmSubj'], $message, 'From: '.$profile['adminEmail']."\nDate: ".date('D, j M Y H:i:s O')."\nMime-Version: 1.0\nContent-Type: text/plain; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit\nX-Priority: 3 (Normal)\nX-PointComma-LoggedIP: ".$_SERVER['REMOTE_ADDR']."\nReturn-Path: ".$profile['adminEmail']);
  }
  if (!$sentMessage) {
    trigger_error("The confirmation email could not be sent. Please contact the site's administrator.",FATAL);
    return false;
  } else {
    trigger_error("The selected email address was successfully confirmed.",WARNING);
    return true;
  }
}

function pcActivateUser($profileId, $userName, $profilePassword=false) {
  // Switches a new user from frozen to active state
  // Limited to module supervisors

  global $pcConfig;
  if ($profileId != 'admin') {
    $profileId = (int)$profileId;
  }
  $userName = addslashes(stripslashes($userName));

  // 1. Verify profile
  if ($profileId=='admin') {
    $clearance = unserialize(CLEARANCE);
    $canSeeUsers = $clearance['isSupervisor'];
    foreach ($clearance['isModuleSupervisor'] as $oneModuleSup) {
      if ($oneModuleSup) {
        $canSeeUsers = true;
      }
    }
    if (!$canSeeUsers) {
      trigger_error("You are not allowed to perform the requested action.",ERROR);
      return false;
    }
  } else {
    if (empty($profilePassword)) {
      $passwordModif = ' IS NULL';
    } else {
      $passwordModif = '=\''.md5($profilePassword).'\'';
    }
    if ($rsProfile = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'profiles` WHERE profileId='.addslashes($profileId).' && password'.$passwordModif)) {
      $profile = $rsProfile[0];
    } else {
      trigger_error("System error: profile issue.",FATAL);
      return false;
    }
    $clearance = unserialize(CLEARANCE);
    if (!$clearance['isModuleSupervisor'][$profile['moduleId']]) {
      trigger_error("You are not allowed to perform the requested action.",ERROR);
      return false;
    }
  }

  // 2. Update entry if correct
  if (pcdb_query('UPDATE `'.addslashes($pcConfig['dbPrefix']).'webusers` SET status=(status+2) WHERE userName=\''.addslashes($userName).'\' && status<2')){
    setGlobal('lastAdminUpdateOn', time());
    setGlobal('lastAdminUpdateBy', $clearance['userName']);
    trigger_error("The selected user was successfully activated.",WARNING);
    return true;
  } else {
    trigger_error("System error: user update not completed.",FATAL);
    return false;
  }
}

function pcDeleteNewUser($userName) {
  // This will delete a user and its assignments,
  // but it won't work on active users to prevent
  // data corruption.
  // TODO: a scavenguser who'd take over all content
  // of deleted users?
  $clearance = unserialize(CLEARANCE);
  $canSeeUsers = $clearance['isSupervisor'];
  global $pcConfig;
  foreach ($clearance['isModuleSupervisor'] as $oneModuleSup) {
    if ($oneModuleSup) {
      $canSeeUsers = true;
    }
  }
  if (!$canSeeUsers) {
    trigger_error("You are not allowed to perform the requested action.",ERROR);
    return false;
  }
  $userName = addslashes(stripslashes($userName));
  $delRs = pcdb_update('DELETE FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE userName=\''.addslashes($userName).'\' && status<3');
  if ($delRs > 0) {
    pcdb_query('DELETE FROM `'.addslashes($pcConfig['dbPrefix']).'assignments` WHERE userName=\''.addslashes($userName).'\'');
    setGlobal('lastAdminUpdateOn', time());
    setGlobal('lastAdminUpdateBy', $clearance['userName']);
    trigger_error("The selected user was successfully deleted from the system.",WARNING);
    return true;
  } else {
    trigger_error("There was an error deleting the selected user.",FATAL);
    return false;
  }
}

function pcSendEmailToken($profileId, $userName, $eMail, $profilePassword=false) {
  // Sends an email to $eMail with a confirmation token
  // according to the specified profile's email template

  global $pcConfig;

  // 0. Verify entry
  if (empty($eMail)) {
    trigger_error("You must enter a valid email address.",ERROR);
    return false;
  }
  
  //check if the mail is valid  
  if (!eregi("^[a-z0-9]+([_\\.-][a-z0-9]+)*"."@([a-z0-9]+([\.-][a-z0-9]{1,})+)*$",$eMail) ) {
    trigger_error("The email you've choosen is not a valid one",ERROR);
    return false;
  }
  
  // 1. Verify profile
  if ($profileId=='admin') {
    global $pcUserCreationMessages;
    $profile['signupMsg'] = $pcUserCreationMessages['signup']['msg'];
    $profile['signupSubj'] = $pcUserCreationMessages['signup']['subj'];
    $profile['adminEmail'] = $pcConfig['adminEmail'];
  } else {
    if (empty($profilePassword)) {
      $passwordModif = ' IS NULL';
    } else {
      $passwordModif = '=\''.md5($profilePassword).'\'';
    }
    if ($rsProfile = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'profiles` WHERE profileId='.addslashes($profileId).' && password'.$passwordModif)) {
      $profile = $rsProfile[0];
    } else {
      trigger_error("System error: profile issue.",FATAL);
      return false;
    }
  }

  // 2. Verify email uniqueness
  if (pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE eMail=\''.addslashes($eMail).'\'')) {
    trigger_error("The email address you've entered is already present in the system. Please enter another one.",ERROR);
    return false;
  }

  // 3. Send confirmation email
  mt_srand((double)microtime()*943426);
  $randomToken = mt_rand(100000, 999999);
  if ($rsUser = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE userName=\''.addslashes($userName).'\'')) {
    $user = $rsUser[0];
    $rsUpdate = pcdb_update('UPDATE `'.addslashes($pcConfig['dbPrefix']).'webusers` SET newEMail=\''.addslashes($eMail).'\', emailToken=\''.md5($randomToken).'\' WHERE userName=\''.addslashes($userName).'\'');
    if ($rsUpdate != 1) {
      trigger_error("System error: database issue.",FATAL);
      return false;
    }
    $searchKeys = array('%t', '%u', '%f', '%l');
    $replaceKeys = array($randomToken, $user['userName'], $user['firstName'], $user['lastName']);
    $message = str_replace($searchKeys, $replaceKeys, $profile['signupMsg']);
    $sentMessage = false;
    $countSends = 0;
    while (!$sentMessage && $countSends<5) {
      $countSends++;
      $sentMessage = mail($eMail, $profile['signupSubj'], $message, 'From: '.$profile['adminEmail']."\nDate: ".date('D, j M Y H:i:s O')."\nMime-Version: 1.0\nContent-Type: text/plain; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit\nX-Priority: 3 (Normal)\nX-PointComma-LoggedIP: ".$_SERVER['REMOTE_ADDR']."\nReturn-Path: ".$profile['adminEmail']);
    }
    if (!$sentMessage) {
      trigger_error("The confirmation email could not be sent. Please contact the site's administrator.",FATAL);
      return false;
    }
  } else {
    trigger_error("The selected user was not found.",ERROR);
    return false;
  }
  trigger_error("An email was sent to your address to confirm its validity.",WARNING);
  return $randomToken;
}


function pcForgottenPassword($profileId, $userNameOrEmail, $profilePassword=false) {
  // Sends an email to $eMail with a confirmation token
  // according to the specified profile's password template.
  // A forgotten password request cancels any pending
  // email change request.

  global $pcConfig;

  // 1. Verify profile
  if ($profileId=='admin') {
    global $pcUserCreationMessages;
    $profile['passwdMsg'] = $pcUserCreationMessages['passwd']['msg'];
    $profile['passwdSubj'] = $pcUserCreationMessages['passwd']['subj'];
    $profile['adminEmail'] = $pcConfig['adminEmail'];
  } else {
    if (empty($profilePassword)) {
      $passwordModif = ' IS NULL';
    } else {
      $passwordModif = '=\''.md5($profilePassword).'\'';
    }
    if ($rsProfile = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'profiles` WHERE profileId='.addslashes($profileId).' && password'.$passwordModif)) {
      $profile = $rsProfile[0];
    } else {
      trigger_error("System error: profile issue.",FATAL);
      return false;
    }
  }

  // 2. Verify user
  if ($rsUser = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` WHERE userName=\''.addslashes($userNameOrEmail).'\' || eMail=\''.addslashes($userNameOrEmail).'\' && status=3')) {
    $user = $rsUser[0];
  } else {
    trigger_error("The selected user was not found.",ERROR);
    return false;
  }

  // 3. Send confirmation email
  mt_srand((double)microtime()*943426);
  $randomToken = mt_rand(100000, 999999);
  $rsUpdate = pcdb_update('UPDATE `'.addslashes($pcConfig['dbPrefix']).'webusers` SET newEMail=NULL, emailToken=\''.md5($randomToken).'\' WHERE userName=\''.addslashes($user['userName']).'\'');
  if ($rsUpdate != 1) {   
    trigger_error("System error: database issue.",FATAL);
    return false;
  }
  $searchKeys = array('%t', '%u', '%f', '%l');
  $replaceKeys = array($randomToken, $user['userName'], $user['firstName'], $user['lastName']);
  $message = str_replace($searchKeys, $replaceKeys, $profile['passwdMsg']);
  $sentMessage = false;
  $countSends = 0;
  while (!$sentMessage && $countSends<5) {
    $countSends++;
    $sentMessage = mail($user['eMail'], $profile['passwdSubj'], $message, 'From: '.$profile['adminEmail']."\nDate: ".date('D, j M Y H:i:s O')."\nMime-Version: 1.0\nContent-Type: text/plain; charset=ISO-8859-1\nContent-Transfer-Encoding: 8bit\nX-Priority: 3 (Normal)\nX-PointComma-LoggedIP: ".$_SERVER['REMOTE_ADDR']."\nReturn-Path: ".addslashes($profile['adminEmail']));
  }
  if (!$sentMessage) {
    trigger_error("The confirmation email could not be sent. Please contact the site's administrator.",FATAL);
    return false;
  } else {
    trigger_error("The selected email address was successfully confirmed.",WARNING);
    return true;
  }
}


function pcModifyUser($firstName=false, $lastName=false, $password=false, $userName=false) {
  // Change user details for user $userName
  // defaults to the current user
  global $pcConfig;
  $clearance = unserialize(CLEARANCE);
  if ($userName) {
    // Trying to modify somebody else
    $canSeeUsers = $clearance['isSupervisor'];
    foreach ($clearance['isModuleSupervisor'] as $oneModuleSup) {
      if ($oneModuleSup) {
        $canSeeUsers = true;
      }
    }
    if (!$canSeeUsers && $userName != $clearance['userName']) {
      error_trigger("You are not allowed to perform the requested action.",ERROR);
      return false;
    }
  } else {
    // Modifying the user's own record
    $userName = $clearance['userName'];
  }
  if (!empty($firstName)) {
    $qStr[] = 'firstName=\''.addslashes($firstName).'\'';
  }
  if (!empty($lastName)) {
    $qStr[] = 'lastName=\''.addslashes($lastName).'\'';
  }
  if (!empty($password)) {
    $qStr[] = 'password=\''.md5($password).'\'';
  }
  if (pcdb_update('UPDATE `'.addslashes($pcConfig['dbPrefix']).'webusers` SET '.implode(', ', $qStr).' WHERE userName=\''.addslashes($userName).'\'') == 1) {
    return true;
  } else {
    trigger_error("The selected user was not found.",ERROR);
    return false;
  }
}
