<?php

function getGlobal($key) {
  global $pcConfig;
  $globalQ = pcdb_select('SELECT value FROM `'.addslashes($pcConfig['dbPrefix'])."global` WHERE setting='".addslashes($key)."'");
  return $globalQ[0]['value'];
}

function genTypeArray($typeId) {
  global $pcConfig;
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'Generate Type Array',1,\"for type \$typeId\")");
  
	//test the asked type:
	$arrayInheritedType = _pcGetParentTree($typeId); 

	if ($arrayInheritedType===false) {
	//bad typeId
		assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'You provide a bad type identifier',7,\"type asked: \$typeId\")");
		return false;
	}
	
	// Returns an array describing the requested type and its characteristics
  $typeId = pcSanitizeStr($typeId,'/[^a-z0-9_]/', 18);
  $typeQ = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'types` WHERE typeId=\''.$typeId.'\'');
  $returnArray['typeId'] = $typeId;
  $returnArray['label'] = $typeQ[0]['label'];
  $returnArray['moduleId'] = $typeQ[0]['moduleId'];
  $returnArray['parentTree']= $arrayInheritedType;

  //we get the chars of the different type of the parent tree
  $i=0;
  foreach($returnArray['parentTree'] as $intParentType) {
    $charsQ = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'characteristics` WHERE typeId=\''.addslashes($intParentType).'\' ORDER BY sequence');
    for($j=0;$j<count($charsQ);$j++) {
      $returnArray['chars'][$charsQ[$j]['columnId']]['charId'] = $charsQ[$j]['charId'];
      $returnArray['chars'][$charsQ[$j]['columnId']]['format'] = $charsQ[$j]['format'];
      $returnArray['chars'][$charsQ[$j]['columnId']]['type'] = $intParentType;
      $returnArray['chars'][$charsQ[$j]['columnId']]['label'] = $charsQ[$j]['label'];
      $returnArray['chars'][$charsQ[$j]['columnId']]['key'] = $charsQ[$j]['columnId']; 
      $returnArray['chars'][$charsQ[$j]['columnId']]['defining'] = ($charsQ[$j]['defining'] == 1) ? true : false;
      $returnArray['chars'][$charsQ[$j]['columnId']]['limitTo'] = $charsQ[$j]['limitTo'];
      $returnArray['chars'][$charsQ[$j]['columnId']]['valuesList'] = $charsQ[$j]['valuesList'];
      $i++;
    }
  }
  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'Generated Type Array',2,\"for type \$typeId\")");
  return $returnArray;
}

/*////////////////////////////////////
//
// Admin functions
//
////////////////////////////////////*/

function getUser($login, $adminRequest=false) {
  global $pcConfig;
  $login = substr(addslashes($login), 0, 12);
  $adminModifier = '';
  if ($adminRequest) {
    $clearance = unserialize(CLEARANCE);
    $canSeeUsers = $clearance['isSupervisor'];
    foreach ($clearance['isModuleSupervisor'] as $oneModuleSup) {
      if ($oneModuleSup) {
        $canSeeUsers = true;
      }
    }
    if ($canSeeUsers) {
      $adminModifier = ', status, activeSince, createdBy, createdOn, newEMail';
    } else if ($clearance['userName'] == $login) {
      // User looking himself up
      $adminModifier = ', newEMail';
    }
  }
  $loginQuery = pcdb_select('SELECT firstName, lastName, email'.$adminModifier.' FROM `'.addslashes($pcConfig['dbPrefix'])."webusers` WHERE userName='".addslashes($login)."'");
  if ($loginQuery) {
    return $loginQuery[0];
  } else {
    return false;
  }
}


/**
 * Function to get an item data from its id
 *
 * the common way to use this function is : getItem(Id,
 * pubstate), it will get every inherited item type through the database and
 * return the complete Item referenced in the item TOC table.
 *
 * If the second argument (pubstate) is equal to zero the result would be the
 * item whatever its current pubstate
 *
 * This function support inherited item
 *
 * Here are the information you get as a result array in the normal use:
 *
 * <code>
 * $resultArray["itemId"]=ItemId asked
 * $resultArray["typeID"]=typeId referenced in the item TOC for this item
 * $resultArray["handle"]=Handle of the Item (display named in the
 * interface)
 * $resultArray ["createdOn"]= Date of its creation
 * $resultArray ["lastUpdateOn"]= Date of its last update
 * $resultArray["lastUpdateBy"]= userName of the last administrator
 * that update the item
 * $resultArray ["createdBy"]= userName if the admin that
 * create the item
 * $resultArray["pubState"]= publication state of the item
 *
 * $resultArray["characteristic name1"] = 'stored data of char1 type';
 * $resultArray ["characteristic name2"] = 'stored data of char2 type';
 *   ...
 * $resultArray ["characteristic nameN"] = 'stored data of charN type';
 * </code>
 *
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @author Raphael Mazoyer <rm@splandigo.nl>
 * @since 3 mar 2005
 * @version 0.2
 * @package pcItemManagement
 * @access public
 * @var integer the primary key of the item you want to get
 * @var integer the publication state of the item you want to get
 * @return array The result in a associative array
 */
function getItem($itemId, $pubState=5) {
  global $pcConfig;

  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Getting an Item',1,\"ItemId: \$itemId, PubState: \$pubState\")");

  // sanitize
  $itemId = (int)$itemId;
  $pubState= (int)$pubState;

  //pubstate where case modifier
  $pubStateQueryModifyer = '';
  if ($pubState) {
    $pubStateQueryModifyer = " && pubState=$pubState";
  }

  //Find the officialy referenced item in the item table
  $rsItemQuery = pcdb_select('SELECT * FROM `'.addslashes($pcConfig['dbPrefix']).'items` WHERE itemId='.$itemId.$pubStateQueryModifyer);
  
  //no item found in the TOC table
  if (!$rsItemQuery) {
    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement','Item Requested does not exist: incorrect ItemId',4,\"ItemId: \".\$itemId)");
    return false;
  }

  //Basic info about the item
  $itemArray = $rsItemQuery[0];

  //obtain extended info about the type
  $typeArray = genTypeArray($itemArray['typeId']);
  $itemArray['moduleId'] = $typeArray['moduleId'];

  //Build the sql table query to be compliant with inheritance
  $strItemTable = '';
  //Build the sqm where query to be compliant with inheritance
  $strItemCondition = '';

  foreach($typeArray['parentTree'] as $intParentType) {
    $strItemTable .= '`'.addslashes($pcConfig['dbPrefix']).addslashes($intParentType).'`,';
    $strItemCondition .= '`'.addslashes($pcConfig['dbPrefix']).addslashes($intParentType).'`.itemId='.$itemId. ' AND ';
  }

  //replace the last ',' by ' '
  $strItemTable = substr($strItemTable, 0, -1).' ';

  //replace the last ' AND ' by ''
  $strItemCondition = substr($strItemCondition, 0, -4).' ';

  // Get the information about all the item of the different type
  $rsItemContent = pcdb_select('SELECT * FROM '.$strItemTable.' WHERE '.$strItemCondition);
  $itemContent = $rsItemContent[0];

  foreach ($typeArray['chars'] as $oneChar) {
    $itemArray[$oneChar['key']] = $itemContent[$oneChar['key']];
		
		//TODO: Flag for review dirty code, nearly useless mention of the characteristics
		// Only used to get the selection menu option directly from the item
    $itemArray[$oneChar['key'].'_char'] = $oneChar;
		
    // That was the general case

    // Now on to dealing with specific formats
		
		//number
    if ($oneChar['format'] == 'n') {
      $itemArray[$oneChar['key']] = (int)$itemArray[$oneChar['key']];
    }
		
		//text
		if ($oneChar['format'] == 't') {
			$itemArray[$oneChar['key'].'_raw'] = $itemArray[$oneChar['key']];
			//process spip text if it is needed
			if ($oneChar['valuesList'] == 'SPIP') {
				require_once($pcConfig['includePath'].$pcConfig['functionFolder'].'pcspip_text.php');
				$itemArray[$oneChar['key'].'_toc'] = '';			
				$itemArray[$oneChar['key']] = propre($itemArray[$oneChar['key']],$itemArray[$oneChar['key'].'_toc']);
			}
    }
    // For now, no other specific formats

  }
  // end of foreach chars

  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcItemManagement', 'Item got',2,\"ItemId: \$itemId, PubState: \$pubState, Number of item Parent treated: \".count(\$typeArray['parentTree']))");
  return $itemArray;
}
// End function getItem()

/**
 * Function to get an array of the parent's type of the parent
 *
 * This function is used to recusively determine the inherited type of the
 * current type.
 * This function now use a local cache, each time a query is done, the result is stored in a static var
 * So that if in the same page the same query is done, no sql query will be done and the result is provided quickly without calculation
 * 
 * The function return array(typeId) is there is the type is a root type
 *
 * This function should normally not be used outside the framework
 *
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 4 mar 2005
 * @version 0.2
 * @package pcTypeManagement
 * @access private
 * @var integer the typeId you want to analyse
 * @return array an array containing the parent's type of the type ordered from
 * the root parent to the nearest in the family tree (return array(typeId) if
 * there is no inherited type)
 * Return false if there is a mistake in the typeId
 *
 */

function _pcGetParentTree($typeId) {
  global $pcConfig;
  static $intRecursion;
  static $arrayCache;  
	
  //Typeid is supposed to be Sanitized because it is an internal function !!!
  
	//look in the cache:
	if (isset($arrayCache[$typeId])) {
			return ($arrayCache[$typeId]);
	}
	
  //Check it there is there is not too much recursion
  $intRecursion++;

  if ($intRecursion > 5) {
    trigger_error("Too many recursion in the parent search process, check if you do not have mistaken your type construction",FATAL);
		exit();
  }

  assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'Start the Parent search for a type',1,\"type asked: \$typeId\")");

  //try to get the direct Parent
  $result = pcdb_select('SELECT `inherit_from` FROM `'.addslashes($pcConfig['dbPrefix'])."types` WHERE `typeId` LIKE '".addslashes($typeId)."'");
	
	
  if ((isset($result[0]) and ($result[0]['inherit_from'] != ''))) {
    //try to find a parent from that parent
    //and return the result

    $arrayTemp= array_merge(_pcGetParentTree($result[0]['inherit_from']),array($typeId));

    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'Found a new Parent',0,\"type asked: \$typeId, Parent type found: \".\$result[0]['inherit_from'])");
		
		//fill the cache
		$arrayCache[$typeId]=$arrayTemp;
    return $arrayTemp;

  } elseif ((isset($result[0]) and ($result[0]['inherit_from'] == ''))){
    //no more parent found

    assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'No parent found',1,\"type asked: \$typeId\")");

    $intRecursion = 0;
		
		//fill the cache
		$arrayCache[$typeId]=array($typeId);
		
    return array($typeId);
  } else {
			assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'You provide a bad type identifier',7,\"type asked: \$typeId\")");
			$intRecursion = 0;
			return false;
	}
}

function getValue($itemId, $charLabel) {
  // This is an ugly function, which has no point with the multiple database format
  // It remains here for compatibility with existing installs
  $item = getItem($itemId);
  return $item[strtolower(str_replace(' ', '_', $charLabel))];
}

/**
 * Function to sanitize array before putting their data in the database
 * 
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 18 avril 2005
 * @version 0.2
 * @package pcDataManagement
 * @access private
 * @param $array the array of integer/string/...
 * @return array with escaped value
 */
function _sanitizeArray($value) {
	$value = is_array($value) ?
                   array_map('_sanitizeArray', $value) :
                   stripslashes($value);

       return $value;
}

/**
 * Function to query data from the database
 *
 *This function is the more useful and thus used function in your pointcomma implementation.
 *It is the function that needs to be used for getting itemId (and data) from a bunch of condition
 *
 *You can only get data from a particular type at a time
 *
 *So the first argument will be the choosen type define and displayed in the interface
 *The type id has the form: {moduleIdentifier}__{typeidentifier} it is also the name of the table that stored the data 
 *
 *This function allow not only to get itemId from the database that could be used with getItem 
 * it can also directly extract value of chracteristics to optimize your site. The chacteristics to be extract 
 * are choosen with the second parameter which could be a string (if only one char) or an array of chars 
 *
 *The result will be a associative array of array the key of the array is the itemId of the currentItem and 
 *the value are stored in an associative array whose keys are the name of the chracteristics required
 *
 *Example:
 *<code>
 *$arrayItemList = getList('mymodule__mytype',array('char1','char2');
 *
 *foreach ($arrayItemList as $itemId => $itemSelectedValue) {
 *		echo 'ItemId: ' . $itemId . 'char1 value: '. $itemSelectedValue['char1']. ' char2 value: '. $itemSelectedValue['char2']
 * }
 *</code>
 *
 *Then you choose the parameter to refine the selected item:
 *	$arrayWhereCase is an array of array containing the condition on the value of the item
 *	
 * IE:
 * <code>
 * --array authorized if there is only one condition
 * $arrayWhereCase = array('char1', '=','10');
 * --Otherwise array of array of condition
 * $arrayWhereCase = array(array('char1', '=','10'),array('char2', '<','2'));
 *</code>
 *
 *The following parameter is an array or an array of array of charname and boolean to sort the result array
 *The boolean indicate if the sorting is ascending (true) or descending (false)
 *
 *IE:
 *<code>
 * --array authorized if there is only one char to sort on
 * $arrayOrderCase = array('char1', true);
 * --Otherwise array of array of condition
 * $arrayOrderCase = array(array('char1', true),array('char2', false));
 * -- will be order by ascending char1 and THEN descending char2
 *</code>
 *
 *The next argument is an array of 2 integers containing the limit of the sql query
 *<code>
 *$arrayLimitCase = array(0,30);
 *</code>
 *
 *Then if the $boolStrict argument is true, it will extract data ONLY if there are in the item Table of Content
 *registered with that type. That means that inherited items of an other type will not be selected in this case 
 *
 *$pubState is an integer of the publication state requested (5 = published, 1= submit, 8= archive)
 *
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 18 avril 2005
 * @version 0.2
 * @package pcDataManagement
 * @access public
 * @var string the typeId
 * @var string or array of characteristic name
 * @var array or array of array of characteristic name, operator, filtervalue to filter the result
 * @var array or array of array of characteristic name,boolean ascending to order the result
 * @var array of integer corresponding to the limit in the sql query
 * @var boolean should the list be limited to item natively of this type
 * @var interger the pubstate of the item queried (1,5 or 8) 
 * @return array The result array itemId => array of selected char value
 */
function getList($typeId, $arraySelectCase = false, $arrayWhereCase = false, $arrayOrderCase = false, $arrayLimitCase = false, $boolStrict = false, $pubState=5) {
global $pcConfig;

//sanitize
$typeId = pcSanitizeStr($typeId,'/[^a-z0-9_]/', 18);

$arrayInheritedType = _pcGetParentTree($typeId);

if ($arrayInheritedType===false) {
	//bad typeId
	assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'You provide a bad type identifier',7,\"type asked: \$typeId\")");
	return false;
}
	
  //initialize variables
  $filterQuery='';	
	$filterQuerySupp = '';
  $sortQuery = '';
  $pubStateQuery = '';
  $limitQuery = '';
  $fromQuery = '';
	$strictQuery = '';
  $selectQuery = '';
	
	if (!$arraySelectCase) {
		//do not do anything		
	} else {
		if (!is_array($arraySelectCase)) {
			$arraySelectCase = array($arraySelectCase);
	  }	
			//sanitize the array
			$arraySelectCase = _sanitizeArray($arraySelectCase);
		if (count($arraySelectCase) > 0) {		
			// Build the select query
			$selectQuery = ', `'.implode('`, `',$arraySelectCase).'`';
		}
	}	 
	
	//$arrayWhereCase is an array of array
		// first array containing the field
		// second array the op
		// third array contain the value	
		//or is false
	if (!$arrayWhereCase) {
		//do not do anything
	}
	elseif (!is_array($arrayWhereCase)) {
			// bad argument in the function
			assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your where case arguments in getList are wrong: not enough operand,value or char to filter',7)");
			return false;
 } 
 elseif (count($arrayWhereCase) > 0) {
	
	 //manage the fact that arrayWhereCase could not be an array of array and have only one arg array(char,op,value)
	 if (!is_array($arrayWhereCase[0])) {
			$arrayWhereCase = array($arrayWhereCase);
	 }
		//sanitize the data and check them
	  while (list($key, $value) = each($arrayWhereCase)) {
				$arrayWhereCase[$key]	= _sanitizeArray($value);
				// test if the number of arg is k
				if (count($arrayWhereCase[$key]) !== 3) {
					// bad argument in the function
					assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your where case array in getList are wrong: not enough operand,value or char to filter',7)");
					return false;
				}				
				// Build the filter query
				$filterQuery .= ' AND '.$arrayWhereCase[$key][0].$arrayWhereCase[$key][1]."'".$arrayWhereCase[$key][2]."'";
		}
  }

  // Order case
  
  if (!$arrayOrderCase) {
		//do not do anithing
  }	elseif (!is_array($arrayOrderCase)) {
			// bad argument in the function
			assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your order case arguments in getList are wrong: not an array',7)");
			return false;
 } elseif (count($arrayOrderCase) > 0) {
	
	 //manage the fact that arrayWhereCase could not be an array of array and have only one arg array(char,op,value)
	 if (!is_array($arrayOrderCase[0])) {
			$arrayOrderCase = array($arrayOrderCase);
	 }
		//sanitize the data and build the query
		$sortQuery = ' ORDER BY';
		
	  while (list($key, $value) = each($arrayOrderCase)) {
				$arrayOrder[$key]	= _sanitizeArray($value);
				// test if the number of arg is k
				if (count($arrayOrderCase[$key]) !== 2) {
					// bad argument in the function
					assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your order case array in getList are wrong: not enough argument',7)");
					return false;
				}				
				// Build the sort query
				$sortQuery .= ($arrayOrderCase[$key][1])?' `'.$arrayOrderCase[$key][0]. '` DESC ,':' `'.$arrayOrderCase[$key][0].'`,';
		}
  }
	
	//remove the last , from order query
	$sortQuery = substr($sortQuery, 0, -1);

  // Handle FROM and WHERE clauses in case of inheritance recursion
  $fromQuery = '`'.addslashes($pcConfig['dbPrefix']).'items`, '.addslashes($pcConfig['dbPrefix']).implode(', '.addslashes($pcConfig['dbPrefix']), $arrayInheritedType);
	
	//add the constraint in the where query due to inheritence
	foreach ($arrayInheritedType as $typeId) {
		$filterQuerySupp .= '`'.addslashes($pcConfig['dbPrefix']).'items`.itemId=`'.addslashes($pcConfig['dbPrefix']).$typeId.'`.itemId'.' AND ';
	}
	
	$filterQuery = substr($filterQuerySupp, 0, -4).$filterQuery;
	
  // Limite case
  
  if ($arrayLimitCase) {
		if (is_array($arrayLimitCase) and count($arrayLimitCase) == 2) {
			$limitQuery = ' LIMIT '.$arrayLimitCase[0].', '.$arrayLimitCase[1];
		} else {
			assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your limite case array in getList are wrong: not enough argument or bad type',7)");
			return false;
		}
  }
	
	//get item only registred in the toc or not
	
	if($boolStrict) {
		$strictQuery = ' AND `'.addslashes($pcConfig['dbPrefix']).'items`.typeId = \''.$typeId.'\' ';
	}
	
	//pubstate case
	
	if ($pubState) {
    $pubStateQuery = ' AND pubState='.$pubState;
  }
	
	$query = 'SELECT `'.addslashes($pcConfig['dbPrefix']).'items`.itemId'. $selectQuery .' FROM '.$fromQuery.' WHERE '.$filterQuery.$pubStateQuery.$strictQuery.$sortQuery.$limitQuery;
  
	$rsListQuery = pcdb_select($query);

	$returnList = array();
	
  if ($rsListQuery) {
    foreach ($rsListQuery as $oneItem) {
			$tempItemId = $oneItem['itemId'];
			unset($oneItem['itemId']);
      $returnList += array($tempItemId => $oneItem);
    }
		assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your getList query has succeeded',3,\"Query : \$query\")");
    return $returnList;
  } 
	elseif ($rsListQuery === 0) {
		assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your getList query return no result',5,\"Query : \$query\")");
		return 0;
  }
	else {		
		assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcDataOutput', 'Your getList query has failed, your arguments are probably wrong',7,\"Query : \$query\")");
		return false;
  }
}

function getRandom($type, $number=1, $column='', $value='', $operator='=', $pubState=5) {
  global $pcConfig;
  $pubState = (int)$pubState;
  $typeId = pcSanitizeStr($typeId,'/[^a-z0-9_]/', 18);
  $number = (int)$number;
  $column = pcSanitizeStr($column,'/[^a-z0-9_]/', 18);
  $value = addslashes($value);
  $operator = addslashes($operator);
  if ($column != '' && $value != '') {
    $modif = $column.$operator."'".$value."'";
    $modix = ', `'.addslashes($pcConfig['dbPrefix']).$type.'`';
  }
  $listAll = pcdb_select('SELECT `'.addslashes($pcConfig['dbPrefix']).'items`.itemId FROM `'.addslashes($pcConfig['dbPrefix']).'items`'.$modix.' WHERE `'.addslashes($pcConfig['dbPrefix']).'items`.itemId=`'.addslashes($pcConfig['dbPrefix']).$typeId.'`.itemId && typeId=\''.$type.'\' && pubState='.$pubState.$modif);
  if ($listAll==false) {
    return false;
  } else if (count($listAll) == 1) {
    if ($number == 1) {
      return $listAll[0]['itemId'];
    } else {
      for($i=0;$i<$number;$i++) {
        $returnArray[$i] = $listAll[0]['itemId'];
      }
      return $returnArray;
    }
  } else {
    mt_srand((double)microtime()*943426);
    if ($number == 1) {
      return $listAll[mt_rand(0, (count($listAll)-1))]['itemId'];
    } else {
      for ($i=0;$i<$number;$i++) {
        $returnArray[$i] = $listAll[mt_rand(0, (count($listAll)-1))]['itemId'];
      }
      return $returnArray;
    }
  }
}

/*
 * Function pcDefaultValue
 * param type string one of 'array', 'bool', 'int', 'numeric', 'float', 'string', 'pcId'
 * param defaultValue mixed defaults to false
 * param inputName string value is available via $_XXX[$inputName] with XXX appropriate method
 * param allowedInput string allowed input method one of A (already provided within the script), P (post), G (get), C (cookie), S (session)
 * returns normalized value
 */
function pcDefaultValue(
  $type,
  $defaultValue = false,
  $inputName = '',
  $allowedInput = ''
) {

  if (!in_array($type, array('array', 'bool', 'int', 'numeric', 'float', 'string', 'pcId','pcTypeId','pcStrId'))) {
    return false;
  }
  $typeDefaultValues = array(
    'array' => array(),
    'bool' => 0,
    'int' => 0,
    'numeric' => 0,
    'float' => 0,
    'string' => '',
    'pcId' => 0,
		'pcTypeId'=> '',
		'pcStrId'=> ''
  );
  $superArrays = array(
    'P' => &$_POST,
    'G' => &$_GET,
    'C' => &$_COOKIE,
    'S' => &$_SESSION
  );
  $allowedInput .= 'D';
  for($i=0;$i<strlen($allowedInput);$i++) {
    $inputMethod = substr(strtoupper($allowedInput), $i, 1);
    switch ($inputMethod) {
      case 'A':
        global $$inputName;
        if (isset($$inputName)) {
	        $returnValue = $$inputName;
				}
      break;

      case 'P':
      case 'G':
      case 'C':
      case 'S':
			 if (isset($superArrays[$inputMethod][$inputName])) {
          $returnValue = $superArrays[$inputMethod][$inputName];
        }
      break;

      case 'D':
        $returnValue = $defaultValue;
      break;
    }
    if (isset($returnValue)) {
      break;
    }
  }
  if ($type == 'pcId') {
    $test = ($returnValue == 'new' || is_numeric($returnValue));
  } elseif ($type == 'bool') {
			// Normalize/force to a boolean
			$returnValue = ($returnValue)?1:0;
			$test = true;
  } elseif ($type == 'pcStrId') {
		$test = ($returnValue == 'new' or $returnValue = pcSanitizeStr($returnValue,'/[^a-z0-9_]/',8));
  } elseif ($type == 'pcTypeId') {
		$test = ($returnValue == 'new' or $returnValue = pcSanitizeStr($returnValue,'/[^a-z0-9_]/',18));
	} else {
    $typefunction = "is_$type";
    $test = $typefunction($returnValue);
  }
  if ($test) {
    return $returnValue;
  } else {
    return $typeDefaultValues[$type];
  }
}

/**
 * Function to sanitize string on several criteria
 *
 * You can sanitize string by reducing it's size to avoir buffer overflow and
 * too complexe injection
 *
 * Put the string lower case (useful for file and directory)
 *
 * Determine which kind of character are allowed and the string that replaced
 * the forbidden char
 *
 * @author Renaud Morvan <renaud@splandigo.nl>
 * @since 21 feb 2005
 * @version 0.2
 * @package pcCommonLib
 * @var $strStringToSanitize The string to sanitize
 * @var $strAuthozizedPattern Regular expression of the allowed char [a-z0-9
 * _] alphanumeric by default and _
 * @var $intStringLength max size of the sanitized string 
 * @var $boolLowerTheCase Lower case the string or not
 * @return string return The sanitized string
 */

function pcSanitizeStr($strStringToSanitize,$strAuthozizedPattern = '/[^a-z0-9_]/', $intStringLength = 20, $strReplaceString = '_', $boolLowerTheCase = 'true') {
    
    //lower the case:
    if ($boolLowerTheCase) {
      $strStringToSanitize = strtolower($strStringToSanitize);
    }
    // sanitize the string
    $strStringToSanitize = substr(preg_replace($strAuthozizedPattern,$strReplaceString,$strStringToSanitize),0,$intStringLength);

    return $strStringToSanitize;
}
?>
