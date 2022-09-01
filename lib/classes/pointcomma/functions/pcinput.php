<?php

function setGlobal($key, $value=false) {
  // Stores the key/value pair in the global table; if value is false, deletes key; returns boolean success indicator.
  global $pcConfig;
  if ($value) {
    //test if the value already exist
    $arraySettingKey = pcdb_select("SELECT setting FROM `" . addslashes($pcConfig['dbPrefix']) . "global` WHERE setting Like '" . addslashes($key) . "'");

    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcRightManagement','Set Global value',2,'key: '.\$key.' , value: '.\$value.', exist? '.\$arraySettingKey[0]['setting'])");

    if (empty($arraySettingKey[0]['setting'])) {
      pcdb_query('INSERT INTO `'.addslashes(addslashes($pcConfig['dbPrefix']))."global` (setting, value) VALUES ('".addslashes($key)."', '".addslashes($value)."')");
      return true;
    } else if (pcdb_query('UPDATE `'.addslashes($pcConfig['dbPrefix'])."global` SET value='".addslashes($value)."' WHERE setting='".addslashes($key)."'")) {
      return true;
    } else {
      // TODO: add error management: couldn't do what was asked
      return false;
    }
  } else {
    // TODO: add error management: could or couldn't do...
    return pcdb_query('DELETE FROM `'.addslashes($pcConfig['dbPrefix'])."global` WHERE key='".addslashes($key)."'");
  }
}

function getWriteAuth($typeId, $createdBy, $updatedBy) {
  // Checks if current user may write an object with the given values. Returns the highest pubState allowed, or false on denied
  global $pcConfig;
  if (defined('CLEARANCE')) {
    // User is logged in.
    $clearance = unserialize(CLEARANCE);

    if ($createdBy==$clearance['userName'] && $updatedBy==$clearance['userName'] && $clearance['rights'][$typeId] >= 1) {
        // Item created the user, nobody else has touched it, he can edit and update starting at writing right 1

      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement','Item created by the user, nobody else has touched it',3,'Max pubstate set to 9 or 1')");

      return ($clearance['rights'][$typeId] > 1) ? 9 : 1;
        // if the user has only submit rights, return max pubState 1, else return max pubState 9
    } else if (
      ($createdBy==$clearance['userName'] && $clearance['rights'][$typeId] >= 2)
        // Item created the user, but it's been modified by somebody else, he can delete starting at writing right 2
      || ($clearance['rights'][$typeId] >= 3)
        // Item not created by the user, but user has writing rights 3
    ) {
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement','Item created the user, but it s been modified and user has writing rights 2 or Item not created by the user, but user has writing rights 3',3,'Max pubstate set to 9 or 1')");

      return 9;
    } else {
      // User doesn't have the propre rights
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement','User doesn t have the proper rights',4)");
      return false;
    }
  } else {
    // User isn't logged in
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement','Submission forbidden, user not logged in',4)");
    trigger_error('user not logged in',ERROR);
    return false;
  }
}

function uploadFile($file, $file_size, $file_name, $modifier, $previous, $newfilename, $limitext=false) {
  global $pcConfig;
  $limitext = ';'.$limitext.';';
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement','Upload file attempt',2,\"file \$file, size \$file_size, previous \$previous, filename: \$file_name, newfilename: \$newfilename\")");

  if ($file_size > 0) {
		
    if (!is_dir($pcConfig['upload']['dir'].$modifier)) {
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"The directory where you're trying to upload a file doesn't exist\",6,'Directory: '.\$pcConfig['upload']['dir'].\$modifier)");
      trigger_error("The directory where you're trying to upload a file doesn't exist", ERROR);
      return $previous;
    }
    //Determine the extension of the uploaded file
    $extension = strtolower(substr($file_name, strrpos($file_name, '.'), strlen($file_name)));

    //filename fourni ou pas?
    if (!$newfilename) {
        //use the name of the upload file
        $newfilename = str_replace(' ', '_', strtolower(substr($file_name, 0, strlen($file_name) - strlen($extension))));
    }
    // sanitize the newfilename
    $newfilename = pcSanitizeStr($newfilename,'/[^a-z0-9_\.\-]/');

    //save for further modication
    $savefilename = $newfilename;

    //add the extension to the file name
    $newfilename = $modifier.'/'.$newfilename.$extension;

    //create the full path
    $returnpath = $pcConfig['upload']['dir'].$newfilename;

    //boolean is the extension allowed
    $goodext = (strstr($limitext, ';'.substr($extension, 1).';')) ? true : false;

    //test if the newfilename doesn't override another file
    if (($previous != $newfilename) and  (file_exists($returnpath))){
      //in this case add a prefix to the file
      //look for a free prefix
      $i = 1;
      while(file_exists($pcConfig['upload']['dir'].$modifier.'/'.$savefilename.'-'.$i.$extension)){
        $i++;
      }
      $newfilename = $modifier.'/'.$savefilename.'-'.$i.$extension;
      $returnpath = $pcConfig['upload']['dir'].$modifier.'/'.$savefilename.'-'.$i.$extension;

      //send a warning to the user
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement','The requested file name already exist, the uploaded filename has been modified to '.\$savefilename.'-'.\$i.\$extension,5,'File already exist in another item, overwrite of '.\$pcConfig['upload']['dir'].\$modifier.'/'.\$savefilename.\$extension.' refused')");
      trigger_error('The requested file name already exist, the uploaded filename has been modified to '.$savefilename.'-'.$i.$extension, WARNING);
    }

    //Test the validity of the file
    if($file_size > $pcConfig['upload']['maxSize']) {
      trigger_error("The file you're trying to upload is too large, please choose a smaller file", ERROR);

    } elseif ($limitext != ';;' && !$goodext) {
      trigger_error("The file you're trying to upload is not of the required type, please check the file's extension", ERROR);
			
    } elseif(@!move_uploaded_file($file, $returnpath)) {
				//security test:
				//test if the file come from an upload or has been tricked
				
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"An unknown error occurred when uploading the file. The upload failed\",8,\"Trying to copy \$file to \$returnpath\")");
        trigger_error("An unknown error occurred when uploading the file. The upload failed", ERROR);

    } else {
        $returnpath = substr($returnpath, strlen($pcConfig['upload']['dir']));
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"A file was saved on the server\",3,\"as \$returnpath\")");
        if (($previous != '') && ($previous != $newfilename)) {
            @unlink($pcConfig['upload']['dir'].$previous);
            assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"A file was deleted from the server\",3,\"File was \$previous\")");
        }
        return $returnpath;
    }
		return false;
  } elseif (!$previous){
    //no previous file
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"No uploaded file, no old file\",4)");
    return false;
  } elseif (!$newfilename) {
    // no upload but a previous file and no filename
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"No uploaded file, kept the old one\",3,\"\$file, \$file_size, as \$previous\")");
    //beware must return it without full path
    return $previous;
  } else {
        // no upload but a previous file and a filename
        // rename the previous file with the filename

        //Determine the extension of the uploaded file
        $extension = strtolower(substr(basename($previous), strrpos(basename($previous), '.'), strlen(basename($previous))));

        // sanitize the newfilename
        $newfilename = pcSanitizeStr($newfilename,'/[^a-z0-9_\.\-]/');

        //add the extensions to the file name
        $newfilename = $modifier.'/'.$newfilename.$extension;

        if (($newfilename!=$previous) && (file_exists($pcConfig['upload']['dir'].$newfilename))) {
            //test if the newfilename doesn't override another file
            trigger_error("You try to rename a file with an already existing file: it failed, please choose another name",ERROR);
            assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"Trying to rename a file with an already existing name\",6,\"File already exist in another item, overwrite of \$previous refused\")");
            return $previous;
        } else {
            //rename the file
            rename($pcConfig['upload']['dir'].$previous,$pcConfig['upload']['dir'].$newfilename);
            assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"Rename a file\",3,\$pcConfig['upload']['dir'].\$previous. ' renamed into '. \$pcConfig['upload']['dir'].\$newfilename)");
            return $newfilename;
        }

  }
}

function pcDeleteFile($file) {
  global $pcConfig;
  // TODO: make sure that the current user has the right to delete the file
  if (is_file($pcConfig['upload']['dir'].$file)) {
    return unlink($pcConfig['upload']['dir'].$file);
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"Delete a file\",3,'File : '.\$file)");
  } else {
    trigger_error("The File you try to delete does not exist",ERROR);
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcCharManagement',\"Delete a file that does not exist\",6,'File : '.\$file)");
    return false;
  }
}

function writeItem($itemId, $state = -1, $typeId=0, $chars=false) {
  // When provided with all char of an item of type $type in the $item array, inserts that item as a new object in publication state $state in the PointComma database, and returns its itemId
  global $pcConfig;
  $clearance = unserialize(CLEARANCE);
  $itemId = (int)$itemId;
  $typeId = pcSanitizeStr($typeId,'/[^a-z0-9_]/', 18);


  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Writing an Item',2,\"ItemId: \$itemId, PubState: \$state,typeid: \$typeId \")");

  if ($state == 0 && $itemId == 0) {
    // Ignore request
    return true;
  }

  // First, set up values to see if we can write
  if ($itemId>0) {
    // Modify existing item
    $existItem = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'items` WHERE itemId='.$itemId);

    // test if the item exist, SECURITY CHECK to avoid allowing access to another forbidden item by inheritance
    if (!isset($existItem[0]['typeId'])) {
       trigger_error("you try to modify an item that does not exist",FATAL);
    }

    $createdBy = $existItem[0]['createdBy'];
    $lastUpdateBy = $existItem[0]['lastUpdateBy'];
    $typeId = $existItem[0]['typeId'];
    $reqstate = ($state > $existItem[0]['pubState']) ? $state : $existItem[0]['pubState'];
    if ($state == -1) {
      // Default to current state
      $state = $existItem[0]['pubState'];
    }
  } else {
    // Create new item
    $createdBy = $clearance['userName'];
    $lastUpdateBy = $clearance['userName'];
    $reqstate = $state;
    if ($state == -1) {
      // Default to draft
      $state = 1;
    }
  }

  if ($itemId>0) {
    // Existing item: get it
    $currentItem = getItem($itemId, false);
  }

  // Obtain empty type object
  $typeArray = genTypeArray($typeId);

  //
  //Build sql query for inherante
  //

  //Build the sql table query to be compliant with inheritance
  $strItemTable = '';
  //Build the sqm where query to be compliant with inheritance
  $strItemCondition = '';
  //Build the delete query field selection
  $strDeleteQueryField = '';

  foreach($typeArray['parentTree'] as $strParentType) {
    $strItemTable .= '`'.addslashes($pcConfig['dbPrefix']).addslashes($strParentType).'`,';
    $strItemCondition .= '`'.addslashes($pcConfig['dbPrefix']).addslashes($strParentType).'`.itemId='.$itemId. ' AND ';
    $strDeleteQueryField .= '`'.addslashes($pcConfig['dbPrefix']).addslashes($strParentType).'`.*,';
      
    //define variable used below:
    // insert string use array because you have to do an insert per inherited type
    $insertStringCols[$strParentType] = '';
    $insertStringVals[$strParentType] = '';
  }

  //replace the last ',' by ' '
  $strItemTable = substr($strItemTable, 0, -1).' ';

  //replace the last ' AND ' by ''
  $strItemCondition = substr($strItemCondition, 0, -4).' ';

  //replace the last ',' by ' '
  $strDeleteQueryField = substr($strDeleteQueryField, 0, -1).' ';

  //
  //sql query for inherante Build
  //

  if ($state == 0) {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Errasing an Item',1,\"ItemId: \$itemId, PubState: \$state,typeid: \$typeId \")");

    // Trying to delete the item
    if (getWriteAuth($typeId, $createdBy, $lastUpdateBy)) {
      $deleteItemSuccess = true;
      // Deleting all files linked to item
      $filesToDelete = array();
      $delFilesQueryString = '';

      foreach ($typeArray['chars'] as $oneChar) {
        if ($oneChar['format'] == 'f') {
          //build the sql query for
          $delFilesQueryString .= ', `'.addslashes($pcConfig['dbPrefix']).addslashes($oneChar['type']).'`.`'.addslashes($oneChar['key']).'`';
          $filesToDelete[] = $oneChar['key'];    
        }
      }
      if (count($filesToDelete)) {
        assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Deleting files',1,\"Number of files:\".count(\$filesToDelete))");
        $rsFiles = pcdb_select('SELECT `'.addslashes($pcConfig['dbPrefix']).$typeId.'`.itemId'.$delFilesQueryString.' FROM '.$strItemTable.' WHERE '.$strItemCondition);

        foreach ($filesToDelete as $one) {
          if ($rsFiles[0][$one]) {
            $deleteItemSuccess = pcDeleteFile($rsFiles[0][$one]) && $deleteItemSuccess;
          }
        }
      }
      //delete all inherited item
      $deleteItemSuccess = pcdb_query('DELETE FROM '.$strItemTable.' WHERE '.$strItemCondition) && $deleteItemSuccess;
      $deleteItemSuccess = pcdb_query('DELETE FROM `'.addslashes($pcConfig['dbPrefix']).'items` WHERE itemId='.$itemId) && $deleteItemSuccess;
      setGlobal('lastContentUpdateOn', time());
      setGlobal('lastContentUpdateBy', $clearance['userName']);

      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Item Deleted',2,\"ItemId: \$itemId\")");
      return $deleteItemSuccess;
    } else {
      // Can't delete the item, user isn't allowed to do so
      trigger_error('User isnt\'t allowed to delete the item',ERROR);
      return false;
    }
  }

  // We're trying to create or update, so let's test, and write if we can
  $allowedState = getWriteAuth($typeId, $createdBy, $lastUpdateBy);
  if (!$allowedState) {
    // Writing isn't authorized
    trigger_error('You do not have a sufficient security clearance level to perform this action.',ERROR);
    return false;
  }

  // Limit state
  $state = ($state > $allowedState) ? $allowedState : $state;

  // Get itemId if new item
  if ($itemId > 0) {
    $isNewItem = false;
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'It is an update of an existing Item',1,\"ItemId:\".\$itemId)");
  } else {
    //update the item TOC table and get the new itemId 
    $itemId = pcdb_insert('INSERT INTO `'.addslashes($pcConfig['dbPrefix'])."items` (typeId, pubState, createdBy, createdOn) VALUES ('$typeId', 0, '".addslashes($clearance["userName"])."', '".date("Y-m-d H:i:s")."')");
    $isNewItem = true; 
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'It is a new item',1,\"ItemId will be:\".\$itemId)");  
  }

  // Chars Management
  $writeXvalsSuccess = true;
	
	$updateString = '';
	
	// insert string use array because you have to do an insert per inherited type 
  //array for insertion define at the beginning of the function 
	
	//the temp handle
	$strHandle = '';
	
  foreach($typeArray['chars'] as $key => $charDesc) {		
		//get the values
		$charValue = array();
		
		//sanitize value
		$charValue['delete'] = isset($chars[$key]['delete'])?(bool)$chars[$key]['delete']:false;
		$charValue['charsPrefix'] = isset($chars[$key]['charsPrefix'] )?$chars[$key]['charsPrefix'] : NULL;
		$charValue['value'] = isset($chars[$key]['value'])?$chars[$key]['value']:false;
		$charValue['filename']= isset($chars[$key]['filename'])?$chars[$key]['filename']: NULL;
		
		//Merge the data and the description
		$char = $charDesc + $charValue;

    if (!$char['delete']) {
     
			//action depend on the char format
      switch ($char['format']) {

        case 'd':
          // Deal with dates
          if ($char['value']) {
						if (is_array($char['value'])) {
							$tempDateArray = array(
								'year' => (isset($char['value']['year'])?(int)$char['value']['year']:'0000'),
								'month' => '-'.(isset($char['value']['month'])?(int)$char['value']['month']:'00'),
								'day' => '-'.(isset($char['value']['day'])?(int)$char['value']['day']:'00'),
								'hour' => ' '.(isset($char['value']['hour'])?(int)$char['value']['hour']:'00'),
								'minute' => ':'.(isset($char['value']['minute'])?(int)$char['value']['minute']:'00'),
								'second' => ':'.(isset($char['value']['second'])?(int)$char['value']['second']:'00')
							);
							switch ($char['limitTo']) {
								case 1:
									// Year and month (10/2002)
									$char['value']['day'] = '01';
									$tempDateArray['day'] = '';

              	case 2:
									// Date (21/10/2002)
									$char['value']['hour'] = '00';
									$char['value']['minute'] = '00';
									$tempDateArray['hour'] = '';
									$tempDateArray['minute'] = '';

              	case 3:
									// Date and time (21/10/2002 17:24)
									$char['value']['second'] = '00';
									$tempDateArray['second'] = '';

              	case 4:
									// Date and full time (21/10/2002 17:24:33)
								break;

              	case 5:
									// Time (17:24)
									$char['value']['second'] = '00';
									$tempDateArray['second'] = '';

              	case 6:
									// Full time (17:24:33)
									$char['value']['day'] = '01';
									$char['value']['month'] = '01';
									$char['value']['year'] = '2001';
									$tempDateArray['day'] = '';
									$tempDateArray['month'] = '';
									$tempDateArray['year'] = '';
								break;
							}
							//check if the day of the month is valid
							if (!(is_numeric($char['value']['month']) and checkdate($char['value']['month'],$char['value']['day'],$char['value']['year']))) {								
								trigger_error("you provide a day/month/year that does not exist, invalid date saved",ERROR);
								$char['value'] = date('Y-m-d H:i:s',0);
								$char['handleVal']= 'invalid date';
							} // check if the hours are ok
							elseif (!(is_numeric($char['value']['hour']) and (($char['value']['hour']) >-1) and ($char['value']['hour'] <24))) {
								trigger_error("you provide an incorrect hour number, must be a number between 0 and 23, invalid date saved",ERROR);
								$char['value'] = date('Y-m-d H:i:s',0);
								$char['handleVal']= 'invalid date';					
							} // check if the minutes are ok
							elseif(!(is_numeric($char['value']['minute']) and (($char['value']['hour']) >-1) and ($char['value']['hour'] <60))) {
								trigger_error("you provide an incorrect minute number, must be a number between 0 and 59, invalid date saved",ERROR);
								$char['value'] = date('Y-m-d H:i:s',0);
								$char['handleVal']= 'invalid date';
							} // check if the seconds are ok
							elseif (!(is_numeric($char['value']['second']) and (($char['value']['hour']) >-1) and ($char['value']['hour'] <60))) {
								trigger_error("you provide an incorrect second number, must be a number between 0 and 59, invalid date saved",ERROR);
								$char['value'] = date('Y-m-d H:i:s',0);
								$char['handleVal']= 'invalid date';
							}
							else {
									$char['value'] = $char['value']['year'].'-'.$char['value']['month'].'-'.$char['value']['day'].' '.$char['value']['hour'].':'.$char['value']['minute'].':'.$char['value']['second'];
									$char['handleVal']= $char['value'];
							}
						}						
						break;
          } else {
							$char['value'] = pcSanitizeStr($chars[$key]['value'],'/[^a-z0-9:\- ]/',18,'');
							$char['handleVal']= $char['value'];
					}

        case 'f':
					//file management
            //get the filepath+filename if it exists
          if ($rsPcEl = pcdb_select('SELECT `'.addslashes($key).'` AS previous FROM `'.addslashes($pcConfig['dbPrefix']).addslashes($char['type']).'` WHERE itemId=\''.$itemId.'\'')) {
            $tempPrevfile = $rsPcEl[0]['previous'];
          } else {
            $tempPrevfile = '';
          }
            //upload the newfile on the old file
          $uploadedResult = uploadFile($_FILES[$char['charsPrefix']]['tmp_name'][$key]['value'], $_FILES[$char['charsPrefix']]['size'][$key]['value'], $_FILES[$char['charsPrefix']]['name'][$key]['value'], 'items', $tempPrevfile, $char['filename'] , $char['valuesList']);
					
          if ($uploadedResult === false) {
            $char['value'] = '';
          } else {
            $char['value'] = $uploadedResult;
          }
					$chars[$key]['value'] = $char['value'];
					$char['handleVal'] = $char['value'];
        break;

        case 'i':
					// Item element
          if ($rsPcEl = pcdb_select('SELECT handle FROM `'.addslashes($pcConfig['dbPrefix'])."items` WHERE itemId='".addslashes($char['value'])."'")) {
            $char['handleVal'] = $rsPcEl[0]['handle'];
          } else {
            $char['handleVal'] = '';
          }
        break;

        case 'l':
					// the select box is manage as a bunch of defined numeric values
          $tempHandleArr = split(';', $char['valuesList']);
					$char['handleVal'] = $tempHandleArr[$char['value']];
        break;

      }
			
      if (isset($chars[$key]['value'])) {
        $updateString .= ', `'.addslashes($pcConfig['dbPrefix']).addslashes($char['type']).'`.`'.addslashes($key).'` = \''.addslashes($char['value'])."'";
        $insertStringCols[$char['type']] .= ', `'.addslashes($key).'`';
        $insertStringVals[$char['type']] .= ", '".addslashes($char['value'])."'";
      }
			
			//Define the Handle for type who do not require particular treatment
			if (!isset($char['handleVal'])) {
        $char['handleVal'] = $char['value'];
      }
			
			// Define back-end handle
		
			// Concatenates the proper value if the value is not empty and the char is defining
				$handleValue = ($char['handleVal'] != '' && $char['defining']) ? $char['handleVal'] : '';
				if ($handleValue != '') {
						$strHandle .= ($strHandle == '') ? $handleValue : ': '.$handleValue;
				}
				
    } else {
      // There is a deletion request on the char (used for files only)
      if ($rsPcEl = pcdb_select('SELECT `'.addslashes($key).'` AS previous FROM `'.addslashes($pcConfig['dbPrefix']).$typeId.'` WHERE itemId=\''.$itemId.'\'')) {
        $tempPrevfile = $rsPcEl[0]['previous'];
      } else {
        $tempPrevfile = '';
      }
      @unlink($pcConfig['upload']['dir'].$tempPrevfile);
      $updateString .= ', `'.addslashes($pcConfig['dbPrefix']).addslashes($char['type']).'`.`'.addslashes($key).'` = \'\'';
    }
  }
  if ($isNewItem) {
    //process an insert query for each inherited type:
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Insertion of the new items',1,\"ItemId:\".\$itemId)");
    foreach ($typeArray['parentTree'] as $intParentType) {
      $writeXvalsSuccess = pcdb_query('INSERT INTO `'.addslashes($pcConfig['dbPrefix']).addslashes($intParentType).'` (itemId '.$insertStringCols[$intParentType].') VALUES ('.$itemId.$insertStringVals[$intParentType].')') && $writeXvalsSuccess;
    }
  } else {
    //process a single update query for all inherited type:
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Update of the new items',1,\"ItemId:\".\$itemId)");
    $writeXvalsSuccess = pcdb_query('UPDATE '.$strItemTable.' SET `'.addslashes($pcConfig['dbPrefix']).$typeId.'`.itemId='.$itemId.$updateString." WHERE ".$strItemCondition) && $writeXvalsSuccess; 
  }


//---------------------------------------------------------------------------------
// Back to item management (now the handle is defined better)

  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Trying to save new item',1,\"Handle:\".\$strHandle)");
  // Now write the data
  // First, the item
  if (!$writeItemSuccess = pcdb_query('UPDATE `'.addslashes($pcConfig['dbPrefix'])."items` SET handle='".addslashes($strHandle)."', lastUpdateOn='".date("Y-m-d H:i:s")."', pubState=".addslashes($state).", lastUpdateBy='".addslashes($clearance["userName"])."' WHERE itemId=".addslashes($itemId))) {
    trigger_error('The item could not be properly saved in the database.',ERROR);
    return false;
  }

// Final end of item management
//--------------------------------------------------------------------------------

  if ($writeItemSuccess && $writeXvalsSuccess) {
    // It worked fine
    setGlobal('lastContentUpdateOn', time());
    setGlobal('lastContentUpdateBy', $clearance['userName']);
    return $itemId;
  } else {
    // Something didn't work
    trigger_error("The item could not be properly saved",ERROR);
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement',\"The item could not be properly saved\",6,\" Something didn't work (Item: \$writeItemSuccess, Xvals: \$writeXvalsSuccess)\")");
    return false;
  }
}

?>
