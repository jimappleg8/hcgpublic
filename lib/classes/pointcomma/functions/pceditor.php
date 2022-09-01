<?php

//----------------------------------------------------------------
// makeItemEditForm()
//
//----------------------------------------------------------------
function makeItemEditForm($itemId, $typeId=false, $charsPrefix='chars', $modifiedItem=false, $pubState=5, $inputClass=false, $inputExtras=true)
{
   if ($typeId && $itemId=='new') {
      $type = genTypeArray($typeId);
      $item = array();
   } else {
      $type = genTypeArray($item['typeId']);
      $item = getItem($itemId, $pubState);
   }

   // check if the typeId does exist
   if (!$type) {
      assert("\$pcConfig['debug']['errorLevel'] > pcDebugInfo(get_defined_vars(),'pcTypeManagement', 'Try to edit a type that does not exist',7,\"for type \$typeId\")");
      return false;
   }

   $returnArray = array();

   // initialize RTE check
   is_RTE($type['chars']);

   foreach ($type['chars'] as $oneChar) {
      $key = $oneChar['key'];
      $returnArray[$key]['name'] = $charsPrefix.'['.$key.'][value]';
      if (isset($modifiedItem[$key])) {
         $returnArray[$key]['value'] = $modifiedItem[$key];
      } elseif (isset($item[$key])) {
         $returnArray[$key]['value'] = $item[$key];
      } else {
         $returnArray[$key]['value'] = NULL;
      }
      $returnArray[$key]['label'] = $oneChar['label'];
      $returnArray[$key]['hidden'] = makeInputField(0, $returnArray[$key]['name'], 'hidden', $returnArray[$key]['value']);
      $returnArray[$key]['html'] = makeCharEditor($oneChar, $returnArray[$key]['value'], $charsPrefix, false, $inputClass, $inputExtras);
   }
   return $returnArray;
}

//----------------------------------------------------------------
// makeCharEditor()
//
//----------------------------------------------------------------
function makeCharEditor($char, $value=false, $formName='chars', $labeling=true, $inputClass=false, $inputExtras=true)
{
  global $pcConfig;
  $returnEditor=''; // init missing vars

  $formId = $formName.'['.$char['key'].']';
  if (!$labeling) {
    $char['label'] = false;
  }

  switch ($char['format']) {

    case 'b':
      $returnEditor .= makeInputField($char['label'], $formId."[value]", 'bool', $value, $char['valuesList'], false, $inputClass, $inputExtras);
    break;

    case 'd':
     // Date and time
			//Convert mysql date into timestamp
			if (isset($value) and ($value != '')) {
				$value= strtotime($value);
			}
				$paddedVal = array(
        'year' => (isset($value)) ? date("Y",$value) : true,
        'month' => (isset($value)) ? date("m",$value) : true,
        'day' => (isset($value)) ? date("d",$value) : true,
        'hour' => (isset($value)) ? date("G",$value) : true,
        'minute' => (isset($value)) ? date("i",$value) : true,
        'second' => (isset($value)) ? date("s",$value) : true
      );
      switch ($char['limitTo']) {
        // Displays the correct form according to the type's settings
        case 1:
          // Year and month (10/2002)
          $paddedVal['day'] = false;

        case 2:
          // Date (21/10/2002)
          $paddedVal['hour'] = false;
          $paddedVal['minute'] = false;

        case 3:
          // Date and time (21/10/2002 17:24)
          $paddedVal['second'] = false;

        case 4:
          // Date and full time (21/10/2002 17:24:33)
        break;

        case 5:
          // Time (17:24)
          $paddedVal['second'] = false;

        case 6:
          // Full time (17:24:33)
          $paddedVal['day'] = false;
          $paddedVal['month'] = false;
          $paddedVal['year'] = false;
        break;
      }

      $returnEditor .= makeInputField($char['label'], $formId."[value]", 'datetime', $paddedVal, false, false, $inputClass, $inputExtras);
    break;

    case 'f':
      //get the true filename from $value
      $filename = basename($value,substr($value, strrpos($value, '.'), strlen($value)));

      //display the form elements
      $returnEditor .= makeInputField('', $char['label'], 'fieldsetStart');
      $returnEditor .= makeInputField('File name', $formId.'[filename]', 'string',$filename);
      $returnEditor .= makeInputField('Upload file', $formId.'[value]', 'file', false, false, false, $inputClass, $inputExtras);
      $returnEditor .= makeInputField(0, $formId.'[charsPrefix]', 'hidden', $formName); // Need to provide this for upload fn
      if ($value != '') {
        $returnEditor .= makeInputField('Delete <a href="'.$pcConfig['productionServer'].$pcConfig['upload']['path'].$value.'" target="_blank">existing file</a>?', $formId.'[delete]', 'confirm');
      }
      $returnEditor .= makeInputField('', '', 'fieldsetEnd');
    break;

    case 'i':
      if ($char['valuesList'] > 0) {
        //to be compatible with inheritance
        $strQueryTOCTable = '`'.addslashes($pcConfig['dbPrefix']).'items`';
        $strQueryItemTable = '`'.addslashes($pcConfig['dbPrefix'].$char['valuesList']).'`';
        $selectItems = pcdb_select("SELECT ".$strQueryItemTable.".itemId, ".$strQueryTOCTable.".handle FROM ".addslashes($strQueryTOCTable)." , ".addslashes($strQueryItemTable)." WHERE ".addslashes($strQueryItemTable).".itemId = ".addslashes($strQueryTOCTable).".itemId");
      } else {
        $selectItems = pcdb_select('SELECT itemId, handle FROM `'.addslashes($pcConfig['dbPrefix']).'items`');
      }

      if ($selectItems) {
        $selectMenu = array(
          array(
            'value' => 0,
            'label' => 'Select an item'
          ),
          array(
            'value' => 0,
            'label' => ''
          )
        );
        foreach ($selectItems as $oneSelectItem) {
          $selectMenu[] = array(
            'value' => $oneSelectItem['itemId'],
            'label' => ($oneSelectItem['handle'] != '') ? stripslashes($oneSelectItem['handle']) : 'no title',
            'isSelected' => ($oneSelectItem['itemId'] == $value)
          );
        }
      }
      else {
        $selectMenu = array(
          array(
            'value' => 0,
            'label' => 'No items available'
          )
        );
      }
      $returnEditor .= makeInputField($char['label'], $formId.'[value]', 'menu', $selectMenu, false, false, $inputClass, $inputExtras);
    break;

    case 'l':
      $explArray = explode(';', $char['valuesList']);
      if (is_array($explArray)) {
        foreach ($explArray as $oneListOrder => $oneListItem) {
          $allSelItems[] = array(
            'value'=> $oneListOrder,
            'label' => $oneListItem,
            'isSelected' => ($oneListOrder == $value)
          );
        }
      }
      $returnEditor .= makeInputField($char['label'], $formId.'[value]', 'menu', $allSelItems, false, false, $inputClass, $inputExtras);
    break;

    case 'n':
      $returnEditor .= makeInputField($char['label'], $formId.'[value]', 'string', $value, false, false, $inputClass, $inputExtras);
    break;

    case 's':
      $returnEditor .= makeInputField($char['label'], $formId.'[value]', 'string', htmlspecialchars($value), false, false, $inputClass, $inputExtras);
    break;

    case 't':
      $returnEditor .= makeHTMLeditor($value, $formId.'[value]', $char['valuesList'], $char['label']);
    break;

    case 'u':
      $usersList = pcdb_select('SELECT firstName, lastName, userName FROM `'.addslashes($pcConfig['dbPrefix']).'webusers` ORDER BY lastName, firstName');
      if (is_array($usersList)) {
        foreach ($usersList as $oneUser) {
          $allSelItems[] = array(
            'value'=> $oneUser['userName'],
            'label' => $oneUser['firstName'].' '.$oneUser['lastName'],
            'isSelected' => ($oneUser['userName'] == $value)
          );
        }
      }
      $returnEditor .= makeInputField($char['label'], $formId.'[value]', 'menu', $allSelItems, false, false, $inputClass, $inputExtras);
    break;

  }
  // End switch

  return $returnEditor;
}


//----------------------------------------------------------------
// makeInputField()
//   returns a formatted input field. Accepts the following arguments:
//      label: the text that is displayed to describe the field
//      name: the form name of the input field
//      format: string, text, hidden, menu, option, password, bool, 
//              file, confirm, keep, datetime, list. Determines the 
//              output of the function, and its behavior.
//      value: the existing value of the field. If format is menu or 
//             option, the it must be an array of arrays. Each of the 
//             elements of the array is an array with the following keys
//               > ['value'] (string)
//               > ['label'] (string)
//               > ['isSelected'] (bool)
//             If the format is bool, value must be set to '1' for the 
//             box to be checked.
//      extraInfo: message displayed to the right of a checkbox
//      checkedVal: for checkboxes only, the value; defaults to '1'
//      inputClass:
//      inputExtras:
//
//----------------------------------------------------------------
function makeInputField($label, $name, $format, $value='', $extraInfo='', $checkedVal='1', $inputClass='formtextinput', $inputExtras=true)
{
   $returnString=''; // init missing var
   $classModif=''; // init missing var
   $htmlId = str_replace(array('[',']'), '_', $name);

   if ($label != '') {
      $returnString .= '<div class="formline"><label for="'.$htmlId.'">'.$label."</label>\n".'<div class="formelement">';
   }
   if (!empty($inputClass)) {
      $classModif = ' class="'.$inputClass.'"';
   }

  switch ($format) {

		case 'fieldsetStart':
			return '<fieldset><legend>'.$name.'</legend>';

		case 'fieldsetEnd':
			return '</fieldset>';

    case 'string':
      $returnString .= '<input type="text" name="'.$name.'" id="'.$htmlId.'" value="'.$value.'" size="40"'.$classModif.(($checkedVal>1)?' maxlength="'.$checkedVal.'"':'').' />';
    break;

    case 'num':
      $returnString .= '<input type="text" name="'.$name.'" id="'.$htmlId.'" value="'.$value.'" size="5"'.$classModif.' />';
    break;

    case 'text':
      $returnString .= '<textarea name="'.$name.'" id="'.$htmlId.'" style="width: 100%; " rows="20"'.$classModif.'>'.$value.'</textarea>';
    break;

    case 'hidden':
      // Warning: special case
      return '<input type="hidden" name="'.$name.'" id="'.$htmlId.'" value="'.$value.'" />';

    case 'menu':
      if (!is_array($value)) {
        $value = array();

      }
      if ($inputExtras) {
        $returnString .= '<select id="'.$htmlId.'" name="'.$name.'"'.$classModif.'>';
        foreach ($value as $oneValue) {
          $returnString .= '    <option value="'.$oneValue['value'].'"';
          if (isset($oneValue['isSelected']) && $oneValue['isSelected']) {
            $returnString .= ' selected="selected"';
          }
          $returnString .= ' class="formtextinput">'.$oneValue['label']."</option>\n";
        }
        $returnString .= '  </select>';
      } else {
        foreach ($value as $oneValue) {
          if ($oneValue['isSelected']) {
            $returnString .= '<input type="hidden" id="'.$htmlId.'" name="'.$name.'" value="'.$oneValue['value'].'" />';
          }
        }
      }
    break;

    case 'option':
      $returnString .= '';
    break;

    case 'password':
      $returnString .= '<input type="password" id="'.$htmlId.'" name="'.$name.'"'.$classModif.' />';
    break;

    case 'bool':
      static $numChecks = 0;
      $returnString .= '<input type="checkbox" id="check_'.$numChecks.'" name="check_'.$numChecks.'" value="1" ';
      $numChecks++;
      if ($value==1) {
        $returnString .= ' checked="checked"';
      }
      $returnString .= ' onClick="this.form.elements[\''.$name.'\'].value=(this.checked)?1:0"'.$classModif.'/><input type="hidden" value="'.$value.'" name="'.$name.'" class="checkbox" /> ';
    break;

    case 'file':
      global $pcConfig;
      $returnString .= '<input type="file" id="'.$htmlId.'" name="'.$name.'" /><input type="hidden" name="MAX_FILE_SIZE" value="'.$pcConfig['upload']['maxSize'].'" />';
    break;

    case 'confirm':
      // Warning: special case
      if ($label != '') {
        $returnString = '<div class="formline">
  <div class="formlabel"><input type="checkbox" id="'.$htmlId.'" name="'.$name.'" value="'.$checkedVal.'" class="checkbox" /></div>
      <div class="formelement">'.$label;
      } else {
        $returnString = '<input type="checkbox" id="'.$htmlId.'" name="'.$name.'" value="'.$checkedVal.'" class="checkbox" /> &nbsp; '.$label;
      }
    break;

    case 'keep':
      // Warning: special case
      if ($label != '') {
        $returnString = '<div class="formkeepline">
  <div class="formkeepcheckbox"><input type="checkbox" id="'.$htmlId.'" name="'.$name.'" value="'.$checkedVal.'" checked="checked" class="checkbox" /></div>
  <div class="formkeeplabel">'.$label;
      } else {
        $returnString = '<input type="checkbox" id="'.$htmlId.'" name="'.$name.'" value="'.$checkedVal.'" checked="checked" class="checkbox" /> &nbsp; '.$label;
      }
    break;

    case 'datetime':
      if ($value['day'] !== false) {
        $returnString .= '<select id="'.$htmlId.'day" name="'.$name.'[day]"'.$classModif.'>'."\n";
        $returnString .= '      <option value="">--</option>';
        for($d=1;$d<=31;$d++) {
          $val = ($d<10) ? '0'.$d : $d;
          $selectedtoken = (((int)$value['day'] === $d) || (($value['day'] === true) && (date("d") == $val))) ? ' selected' : '';
					$returnString .= '      <option value="'.$val.'"'.$selectedtoken.">$d</option>\n";
        }
        $returnString .= "\n</select>\n&nbsp;";
      }
      if ($value['month'] !== false) {
        $returnString .= '<select id="'.$htmlId.'month" name="'.$name.'[month]"'.$classModif.'>'."\n";
        $returnString .= '      <option value="">--</option>';
        for($d=1;$d<=12;$d++) {
          $val = ($d<10) ? '0'.$d : $d;
          $selectedtoken = (($value['month'] === $val) || (($value['month'] === true) && (date("m") == $val))) ? ' selected' : '';
          $returnString .= '      <option value="'.$val.'"'.$selectedtoken.">".strftime('%B', strtotime("2000-$val-25"))."</option>\n";
        }
        $returnString .= "\n</select>\n&nbsp;";
      }
      if ($value['year'] !== false) {
        $returnString .= '<input type="text" id="'.$htmlId.'year" name="'.$name.'[year]" size="6" maxlength="4" value="';
        $returnString .= ($value['year'] === true) ? date("Y") : $value['year'];
        $returnString .= '"'.$classModif.'> &nbsp;&nbsp; ';
      }
      if ($value['hour'] !== false) {
        $returnString .= '<input type="text" id="'.$htmlId.'hour" name="'.$name.'[hour]" size="4" maxlength="2" value="';
        $returnString .= ($value['year'] === true) ? date("G") : $value['hour'];
        $returnString .= '"'.$classModif.'>';
      }
      if ($value['minute'] !== false) {
        $returnString .= ' : <input type="text" id="'.$htmlId.'minute" name="'.$name.'[minute]" size="4" maxlength="2" value="';
        $returnString .= ($value['minute'] === true) ? date("i") : $value['minute'];
        $returnString .= '"'.$classModif.'>';
      }
      if ($value['second'] !== false) {
        $returnString .= ' : <input type="text" id="'.$htmlId.'second" name="'.$name.'[second]" size="4" maxlength="2" value="';
        $returnString .= ($value['second'] === true) ? date("s") : $value['second'];
        $returnString .= '"'.$classModif.'>';
      }
      if ($inputExtras) {
        $returnString .= ' <input type="button" value="now" onClick="rightNow = new Date(); if(this.form.elements[\''.$name.'[hour]\']) { this.form.elements[\''.$name.'[hour]\'].value = rightNow.getHours();} if (this.form.elements[\''.$name.'[minute]\']) {this.form.elements[\''.$name.'[minute]\'].value = rightNow.getMinutes();} if (this.form.elements[\''.$name.'[second]\']) {this.form.elements[\''.$name.'[second]\'].value = rightNow.getSeconds();} if (this.form.elements[\''.$name.'[year]\']) {this.form.elements[\''.$name.'[year]\'].value = (rightNow.getYear()>1900)?rightNow.getYear():rightNow.getFullYear();} if (this.form.elements[\''.$name.'[month]\']) {this.form.elements[\''.$name.'[month]\'].selectedIndex = (rightNow.getMonth()+1);} if (this.form.elements[\''.$name.'[day]\']) {this.form.elements[\''.$name.'[day]\'].selectedIndex = rightNow.getDate();}"> <input type="button" value="no time" onClick="if(this.form.elements[\''.$name.'[hour]\']) { this.form.elements[\''.$name.'[hour]\'].value = \'\';} if (this.form.elements[\''.$name.'[minute]\']) {this.form.elements[\''.$name.'[minute]\'].value = \'\';} if (this.form.elements[\''.$name.'[second]\']) {this.form.elements[\''.$name.'[second]\'].value = \'\';} if (this.form.elements[\''.$name.'[year]\']) {this.form.elements[\''.$name.'[year]\'].value = \'\';} if (this.form.elements[\''.$name.'[month]\']) {this.form.elements[\''.$name.'[month]\'].selectedIndex = 0;} if (this.form.elements[\''.$name.'[day]\']) {this.form.elements[\''.$name.'[day]\'].selectedIndex = 0;}"> ';
      }

    break;

    case 'list':
      static $numMenus = 0;
      $returnString .= makeInputField('', $name, 'hidden', $value);
      if ($inputExtras) {
        $returnString .= '<table border="0" cellpadding="3" cellspacing="0" class="manageList">
  <tr valign="top">
    <th rowspan="2">
    <table border="0" cellpadding="2" cellspacing="0" class="tbbox">
      <tr>
        <th>List elements</th>
      </tr>
      <tr>
        <td>
          <select size="9" name="menulist['.$numMenus.']" id="menulist['.$numMenus.']" class="managelist">
          </select>
        </td>
      </tr>
    </table>
    </th>
    <td>
      <table border="0" cellpadding="2" cellspacing="0" class="tbbox">
        <tr>
          <th>Add element</th>
        </tr>
        <tr>
          <td>
            <input type="text" name="menuadditem['.$numMenus.']" onKeyPress="if (event.keyCode == 13) { addListElement('.$numMenus.'); return false; }">
            <input type="button" value="add" onClick="addListElement('.$numMenus.')">
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" cellpadding="2" cellspacing="0" class="tbbox">
        <tr>
          <th colspan="2">Selected element</th>
        </tr>
        <tr>
          <td>move <input type="button" value="up" onClick="moveListElementUp('.$numMenus.')"> <input type="button" value="down" onClick="moveListElementDown('.$numMenus.')"></td>
          <td>
            <input type="button" value="delete" onClick="deleteListElement('.$numMenus.')">
          </td>
        </tr>
      </table>
    ';
        if (!empty($extraInfo)) {
          $returnString .= '<span class="manageextex"><br />'.$extraInfo.'</span>';
          $extraInfo = '';
        }
        $returnString .= '</td>
  </tr>
</table>
<script type="text/javascript"><!--
populateList('.$numMenus.', \''.$value.'\', \''.$name.'\');
// --></script>
';
        $numMenus ++;
      }
    break;

  }
  // End switch format

  $returnString .= $extraInfo;
  if ($label != '') {
    $returnString .= "</div>\n</div>\n";
  }
  return $returnString;
}


//----------------------------------------------------------------
// makeEditPageJS()
//
//----------------------------------------------------------------
function makeEditPageJS($rteSkinDir=false) {
  global $pcConfig, $pcCanEditHTML;
  if (!$rteSkinDir) {
    $rteSkinDir = $pcConfig['adminServer'];
  }

  if (($pcCanEditHTML) && is_RTE()) {
    $returnStr = '<script type="text/javascript" src="'.$rteSkinDir.'img/richtext.js"></script>
<script type="text/javascript">
<!--
initRTE("'.$rteSkinDir.'img/buttons/", "'.$rteSkinDir.'", "'.$rteSkinDir.'img/style.css");
//-->
</script>';
    $returnStr .= '<script type="text/javascript" src="'.$pcConfig['adminServer'].'img/script.js"></script>';
    $returnStr .= "\n".'<script type="text/javascript"><!--'."\n".'pcJSroot = "'.$pcConfig['adminServer'].'";'."\n// --></script>\n";
    return $returnStr;
  } else {
    return '<script type="text/javascript" src="'.$pcConfig['adminServer'].'img/script.js"></script>';
  }
}


//----------------------------------------------------------------
// makeHTMLeditor()
//
//----------------------------------------------------------------
function makeHTMLeditor($text, $field, $editorType='NONE', $label=false)
{
   global $pcCanEditHTML;

  if ($label) {
    $label = '<label for="'.$field.'">'.$label.'</label>';
  } else {
    $label = '';
  }
	// display a Rich Text Editor if the characteristic is configured to and the browser support it
  if (($editorType == 'RTE') and $pcCanEditHTML) {
    $returnStr = '<div class="formline">'.$label.'
<div class="formelement"><div class="haedit"><script type="text/javascript">
<!--
';
    if ($text != "") {
      $returnStr .= "writeRichText('".$field."', '".RTESafe($text)."', 500, 200, true, false);
";
    } else {
      $returnStr .= "writeRichText('".$field."', '<p></p>', 500, 200, true, false);
";
    }
    $returnStr .= '
//-->
</script></div>
</div></div>
';
  } else {
    // No SPIP or NONE text style type
    $returnStr = makeInputField($label, $field, 'text', htmlspecialchars($text));
  }
  return $returnStr;
}


//----------------------------------------------------------------
// makeEditPageFooter()
//
//----------------------------------------------------------------
function makeEditPageFooter()
{
   global $pcCanEditHTML;

   // init missing vars
   if (!isset($returnFooter)) {
      $returnFooter=false;
   }

   if (($pcCanEditHTML)&& is_RTE()) {
      $returnFooter .= '
         <script type="text/javascript" id="editorend">
         <!--
         activateRTEs(allRTEs);
         document.canedithtml = true;
         // -->
         </script>
         ';
   }
   $returnFooter .= makeInputField(0, 'action', 'hidden', 'save').makeInputField(0, 'issaved', 'hidden', '0').'<a name="pageBottom"></a>';

   return $returnFooter;
}


//----------------------------------------------------------------
// is_RTE()
//   Determine if a Rich Text Editor is a characteristic of the 
//   current type.
//
//   var $char => array of the characteristics of the current type
//
//----------------------------------------------------------------
function is_RTE($char = array())
{
   static $BoolRteExist;
   if (!isset($BoolRteExist)) {
      $BoolRteExist = false;
   }
   if (is_array($char)) {
      foreach ($char as $oneChar) {
         if ($oneChar['format'] == 't') {
            $BoolRteExist = true;
         }
      }
   }
   return $BoolRteExist;
}


//----------------------------------------------------------------
// makeEditPageFooter()
//   Returns safe code for preloading in the RTE
//
//----------------------------------------------------------------
function RTESafe($strText)
{
   $tmpString = trim($strText);

   //convert all types of single quotes
   $tmpString = str_replace(chr(145), chr(39), $tmpString);
   $tmpString = str_replace(chr(146), chr(39), $tmpString);
   $tmpString = str_replace("'", "&#39;", $tmpString);

   //convert all types of double quotes
   $tmpString = str_replace(chr(147), chr(34), $tmpString);
   $tmpString = str_replace(chr(148), chr(34), $tmpString);
// $tmpString = str_replace("\"", "\"", $tmpString);

   //replace carriage returns & line feeds
   $tmpString = str_replace(chr(10), " ", $tmpString);
   $tmpString = str_replace(chr(13), " ", $tmpString);

   return $tmpString;
}


$pcCanEditHTML = (
  // That's if the browser's able to deal with MSIE's integrated edit mode. IE/MacOS doesn't support it at the moment
  // TODO: add Mozilla's midas rich-text interface
  // TODO: add per-browser-type control
    (
      substr(stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE'), 5, 3) > 5.5
      &&
      stristr($_SERVER['HTTP_USER_AGENT'], 'Windows')
    )
    ||
    substr(stristr($_SERVER['HTTP_USER_AGENT'], 'gecko'), 6, 6) > 200306
   );

?>
