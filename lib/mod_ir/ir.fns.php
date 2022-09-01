<?php

// ---------------------------------------------------------------------------
// ir.fns.php
//   written by Jim Applegate
//
// ---------------------------------------------------------------------------

require_once 'ir.inc.php';
require_once 'template.class.php';
require_once 'dbi_adodb.inc.php';

// ------------------------------------------------------------------------
// TAG: irPage()
//
// ------------------------------------------------------------------------
function irPage($page_name, $params = array())
{
   global $_HCG_GLOBAL;

   $url_base = "http://xml.corporate-ir.net/irxmlclient.asp?compid=87078";
   
   // creates extra parameter strings for some URLs
   $url_add = "";
   if (!empty($params)) {
      foreach($params as $key => $value) {
         $url_add .= "&".urlencode($key)."=".urlencode($value);
      }
   }
   
   switch ($page_name) {
   case "advanced_fun": 
      $source[0]['XML_LIST_ELEMENTS'][] = "StandardizedFinancials";
      $source[0]['XML_LIST_ELEMENTS'][] = "lineItem";
      $source[0]['url'] = $url_base."&reqtype=annualbalancesheet";
      $source[0]['lifeTime'] = 3600;
      $source[1]['XML_LIST_ELEMENTS'][] = "Fundamentals";
      $source[1]['XML_LIST_ELEMENTS'][] = "PriceChange";
      $source[1]['url'] = $url_base."&reqtype=fundamentals";
      $source[1]['lifeTime'] = 3600;
      break;
   case "alerts": 
      $source[0]['XML_LIST_ELEMENTS'][] = "Member";
      $source[0]['XML_LIST_ELEMENTS'][] = "Alert";
      $source[0]['url'] = $url_base."&reqtype=alerts";
      $source[0]['lifeTime'] = 3600;
      break;
   case "annual_reports":
      $source[0]['XML_LIST_ELEMENTS'][] = "Item";
      $source[0]['XML_LIST_ELEMENTS'][] = "Index";
      $source[0]['url'] = $url_base."&reqtype=items";
      $source[0]['lifeTime'] = 3600;
      break;
   case "audio_archives":
      $source[0]['XML_LIST_ELEMENTS'][] = "Item";
      $source[0]['XML_LIST_ELEMENTS'][] = "Index";
      $source[0]['url'] = $url_base."&reqtype=items";
      $source[0]['lifeTime'] = 3600;
      break;
   case "company":
      $source[0]['XML_LIST_ELEMENTS'][] = "Company";
      $source[0]['url'] = $url_base."&reqtype=company";
      $source[0]['lifeTime'] = 3600;
      $source[1]['XML_LIST_ELEMENTS'][] = "Stock_Quote";
      $source[1]['url'] = $url_base."&reqtype=quotes";
      $source[1]['lifeTime'] = 320;
      break;
   case "directors":
      $source[0]['XML_LIST_ELEMENTS'][] = "Person";
      $source[0]['XML_LIST_ELEMENTS'][] = "Analyst";
      $source[0]['url'] = $url_base."&reqtype=people2";
      $source[0]['lifeTime'] = 3600;
      break;
   case "email_alerts": 
      $source[0]['XML_LIST_ELEMENTS'][] = "Member";
      $source[0]['XML_LIST_ELEMENTS'][] = "Alert";
      $source[0]['url'] = $url_base."&reqtype=alerts";
      $source[0]['lifeTime'] = 3600;
      break;
   case "event_calendar": 
      $source[0]['XML_LIST_ELEMENTS'][] = "Event";
      $source[0]['XML_LIST_ELEMENTS'][] = "HeaderLink";
      $source[0]['XML_LIST_ELEMENTS'][] = "Webcast";
      $source[0]['XML_LIST_ELEMENTS'][] = "Stream";
      $source[0]['XML_LIST_ELEMENTS'][] = "Encoding";
      $source[0]['url'] = $url_base."&reqtype=events2";
      $source[0]['lifeTime'] = 3600;
      break;
   case "event_detail": 
      $source[0]['XML_LIST_ELEMENTS'][] = "Event";
      $source[0]['XML_LIST_ELEMENTS'][] = "HeaderLink";
      $source[0]['XML_LIST_ELEMENTS'][] = "Webcast";
      $source[0]['XML_LIST_ELEMENTS'][] = "Stream";
      $source[0]['XML_LIST_ELEMENTS'][] = "Encoding";
      $source[0]['url'] = $url_base."&reqtype=events2";
      $source[0]['lifeTime'] = 3600;
      break;
   case "governance":
      $source[0]['XML_LIST_ELEMENTS'][] = "Person";
      $source[0]['XML_LIST_ELEMENTS'][] = "Analyst";
      $source[0]['url'] = $url_base."&reqtype=people2";
      $source[0]['lifeTime'] = 3600;
      break;
   case "info_request":
      $source[0]['XML_LIST_ELEMENTS'][] = "";
      $source[0]['url'] = $url_base."&reqtype=informationrequest";
      $source[0]['lifeTime'] = 3600;
      $source[1]['XML_LIST_ELEMENTS'][] = "InfoRequestFormField";
      $source[1]['XML_LIST_ELEMENTS'][] = "FormFieldValue";
      $source[1]['url'] = $url_base."&reqtype=informationrequestconfig";
      $source[1]['lifeTime'] = 3600;
      break;
   case "management":
      $source[0]['XML_LIST_ELEMENTS'][] = "Person";
      $source[0]['XML_LIST_ELEMENTS'][] = "Analyst";
      $source[0]['url'] = $url_base."&reqtype=people2";
      $source[0]['lifeTime'] = 3600;
      break;
   case "press_releases":
      $source[0]['XML_LIST_ELEMENTS'][] = "NewsRelease";
      $source[0]['url'] = $url_base."&reqtype=newsreleases";
      $source[0]['lifeTime'] = 3600;
      break;
   case "price_lookup": 
      $source[0]['XML_LIST_ELEMENTS'][] = "HistoricalQuotes";
      if ($params['reqdate'] == "") { 
         $url_add = "&reqdate=07/14/2005";
      }
      $source[0]['url'] = $url_base."&reqtype=histquote&symb=HAIN".$url_add;
      $source[0]['lifeTime'] = 3600;
      break;
   case "release_text":
      $source[0]['XML_LIST_ELEMENTS'][] = "";
      $source[0]['url'] = $url_base."&reqtype=releasetxt".$url_add;
      $source[0]['lifeTime'] = 3600;
      break;
   case "sec_filings":
      $source[0]['XML_LIST_ELEMENTS'][] = "IRXML";
      $source[0]['url'] = $url_base."&reqtype=secfilings";
      $source[0]['lifeTime'] = 3600;
      break;
   case "stock_chart": 
      $source[0]['XML_LIST_ELEMENTS'][] = "ValuePair_attribute_value";
      $source[0]['XML_LIST_ELEMENTS'][] = "ValuePair_attribute_description";
      $source[0]['XML_LIST_ELEMENTS'][] = "ControlSet";
      $source[0]['url'] = $url_base."&reqtype=chart".$url_add;
      $source[0]['lifeTime'] = 3600;
      break;
   case "stock_quote": 
      $source[0]['XML_LIST_ELEMENTS'][] = "Stock_Quote";
      $source[0]['url'] = $url_base."&reqtype=quotes";
      $source[0]['lifeTime'] = 320;
      break;
   case "webcast_header": 
      $source[0]['XML_LIST_ELEMENTS'][] = "Event";
      $source[0]['XML_LIST_ELEMENTS'][] = "HeaderLink";
      $source[0]['XML_LIST_ELEMENTS'][] = "Webcast";
      $source[0]['XML_LIST_ELEMENTS'][] = "Stream";
      $source[0]['XML_LIST_ELEMENTS'][] = "Encoding";
      $source[0]['url'] = $url_base."&reqtype=events2";
      $source[0]['lifeTime'] = 3600;
      break;
   }

   // for each source, get the raw XML & convert it to a PHP array
   for($i=0; $i<count($source); $i++) {
      
      unset($_HCG_GLOBAL['XML_LIST_ELEMENTS']);
      for($j=0; $j<count($source[$i]['XML_LIST_ELEMENTS']); $j++) {
         $_HCG_GLOBAL['XML_LIST_ELEMENTS'][$j] = $source[$i]['XML_LIST_ELEMENTS'][$j];
      }

      $rawXML = getXmlFeed($source[$i]['url'], $source[$i]['lifeTime']);
   
      // display link to XML if desired
      if ($_HCG_GLOBAL['ir_display_xml'] == true) {
         echo "<p><a href=\"".$source[$i]['url']."\" target=\"_blank\">View XML Feed $i</a></p>";
      }
      
      // fix special characters at this stage
      // this is not an ideal solution, but it works.
      $rawXML = preg_replace("/®/", "&reg;", $rawXML);
      $rawXML = preg_replace("/’/", "'", $rawXML);
      $rawXML = preg_replace("/–/", "-", $rawXML);

      $pageData[$i] = makeXMLTree($rawXML);
   }
   
   $tpl = "ir_".$page_name.".tpl";
   
   $t = new HCG_Smarty;
   
   // post processing
   switch ($page_name) {
   case "alerts":
      $newData = alerts_signup($pageData);
      $t->assign("ir", $newData);
      break;
   case "company":
      $newData = irCompany($pageData);
      for ($i=0; $i<count($newData); $i++) {
         $t->assign("ir".$i, $newData[$i]['IRXML']);
      }
      $t->assign("params", $params);
      break;
   case "email_alerts":
      $newData = irAlerts($pageData, $t);
      $t->assign("ir", $newData);
      break;
   case "event_calendar":
      $newData = irEvents($pageData);
      $t->assign("ir", $newData);
      break;
   case "event_detail":
      $newData = irEventDetail($pageData);
      $t->assign("ir", $newData);
      break;
   case "webcast_header":
      $newData = irEventHeaders($pageData);
      $t->assign("ir", $newData);
      break;
   case "info_request":
      $newData = irInfoRequest($pageData, $t);
      $t->assign("ir", $newData);
      break;
   case "release_text":
      $newData = irReleaseText($pageData);
      $t->assign("ir", $newData);
      $t->assign("params", $params);
      break;
   default:
      for ($i=0; $i<count($pageData); $i++) {
         $t->assign("ir".$i, $pageData[$i]['IRXML']);
      }
      $t->assign("params", $params);
      break;
   }

   // display converted XML data if desired
   if ($_HCG_GLOBAL['ir_display_data'] == true) {
      echo "<pre>XML Data:\n";
      for ($i=0; $i<count($pageData); $i++) {
         print_r($pageData[$i]['IRXML']);
      }
      echo "Params:\n";
      print_r($params);
      if (!empty($newData)) {
         echo "New Data:\n";
         print_r($newData);
      }
      echo "</pre>";
   }

   $t->setTplPath($tpl);
   $t->display($tpl);
}

//-------------------------------------------------------------------------
// irAlerts
//   Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function irAlerts($pageData, &$t)
{
   global $_HCG_GLOBAL;

   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

   $form = new HTML_QuickForm('email_alerts', null, null, null, null, true);
   
   $alertList = $pageData[0]['IRXML']['Alerts']['Alert'];
   
   for ($i=0; $i<count($alertList); $i++) {
      $form->addElement('checkbox', $alertList[$i]['AlertClass'], '', ' <b>'.$alertList[$i]['AlertListDescr'].'</b>', null);
   }
   $form->addElement('text', 'subscriber_email', 'Enter your e-mail address: ', array('size' => 25));
   $form->addElement('submit', 'Submit', 'Subscribe');

   $form->applyFilter('subscriber_email', 'trim');

   $form->addRule('subscriber_email', 'You must enter your email address.', 'required', null, 'client');

   if ($form->validate()) {
      $form_data = process_alerts($form, $alertList);
      $display_response = true;
   } else {
      $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($t);
      $renderer->setRequiredTemplate('{if $required}<span class="red">*</span>{/if}{$label}');
      $renderer->setErrorTemplate('{$label}{if $error}<br><span class="red">{$error}</span>{/if}');    
      $form->accept($renderer);
      $form_data = $renderer->toArray();
      $display_response = false;
      
      // display converted XML data if desired
      if ($_HCG_GLOBAL['ir_display_data'] == true) {
         echo "<pre>Form Data:\n"; print_r($form_data); echo "</pre>";
      }
   }

   $results[0] = $display_response;
   $results[1] = $form_data;

   return $results;
}

//-------------------------------------------------------------------------
// irCompany
//   This is perhaps a temporary solution to the odd encoding that seems
//   to affect only the Company Profile. This search and replaces all
//   problems.
//
//-------------------------------------------------------------------------

function irCompany($pageData)
{
   $profile = $pageData[0]['IRXML']['Companies']['Company'][0]['CompanyProfile'];
   
   $profile = preg_replace("/Biomarche/", "Biomarch&eacute;", $profile);
   $profile = preg_replace("/Westoy/", "WestSoy", $profile);
   
   $pageData[0]['IRXML']['Companies']['Company'][0]['CompanyProfile'] = $profile;
   return $pageData;
}

//-------------------------------------------------------------------------
// process_alerts
//   Process alerts sign up form. This entails formatting an email and
//   sending to the person signing up to confirm their email address.
//
//-------------------------------------------------------------------------

function process_alerts(&$form, $alertList)
{
   $tpl = "ir_email_alerts_mail.tpl";
   
   $values = $form->exportValues();

   foreach ($values as $key=>$value) {
      $slash_values[$key] = addslashes($value);
   }

   $form_data['email'] = $values['subscriber_email'];
   $form_data['timestamp'] = time();

   $listCnt = 0;
   $alertStr = "";
   for ($i=0; $i<count($alertList); $i++) {
      $alertClass = $alertList[$i]['AlertClass'];
      if ($values[$alertClass] == 1) {
         $form_data['list'][$listCnt] = $alertList[$i];
         if ($listCnt != 0) {
            $alertStr .= "|";
         }
         $alertStr .= $alertClass;
         $listCnt++;
      }
   }

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO ir_alert ".
            "(email, alerts, datesent, submit_ts) ".
            "VALUES ".
            "(\"".$slash_values['subscriber_email']."\", ".
            "\"".$alertStr."\", ".
            "\"".date("Y-m-d")."\", ".
            "\"".$form_data['timestamp']."\")";
   $db->Execute($query);
   
   // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   $m = new HCG_Smarty;
   $m->assign("mail", $form_data);
   $m->setTplPath($tpl);
   $mail_content = $m->fetch($tpl);
   
   $fd = popen($sendmail,"w");
   fputs($fd, $mail_content."\n");
   pclose($fd);
   
   return $form_data;
}


//-------------------------------------------------------------------------
// alerts_signup
//   This is the function run when the person confirms his or her email
//   by clicking on the link. All information required for this function
//   comes from the URL of that clicked link. We format an XML file and
//   submit it to CCBN so they can add the person to their database.
//
//-------------------------------------------------------------------------

function alerts_signup($pageData)
{
   global $_HCG_GLOBAL;

   $alertList = $pageData[0]['IRXML']['Alerts']['Alert'];
   $timestamp = $_HCG_GLOBAL['passed_vars']['item'];
   
   // pull data for this "item" number
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "SELECT * FROM ir_alert ".
            "WHERE submit_ts = ".$timestamp;
   $results = $db->GetAll($query);
   
   // make sure the record exists
   if (count($results) == 0) {
      $returnData[0] = "error";
      $returnData[1][0] = "The item number was not found.";
      return $returnData;
   }
   
   // make sure they haven't already confirmed the request
   if ($results[0]['completed'] == 1) {
      $returnData[0] = "error";
      $returnData[1][0] = "You have already successfully subscribed to these alerts.";
      return $returnData;
   }
   
   // turn the string of requested alerts into an array
   $a_list = explode("|", $results[0]['alerts']);
   for ($i=0; $i<count($a_list); $i++) {
      $alerts[$a_list[$i]] = 1;
   }

   // build a list of alert data, including the needed XML
   $listCnt = 0;
   $xml_insert = "";
   for ($i=0; $i<count($alertList); $i++) {
      $alertClass = $alertList[$i]['AlertClass'];
      if ($alerts[$alertClass] == 1) {
         $form_data['list'][$listCnt] = $alertList[$i];
         $xml_insert .= "<ALERT SUBSCRIBE=\"YES\">".strtoupper($alertClass)."</ALERT>\n";
         $listCnt++;
      }
   }

   $xml_text = "";
   $xml_text .= "<ALERT_SUBSCRIPTION>\n";
   $xml_text .= "<COMPANY CORPORATE_MASTER_ID=\"87078\">\n";
   $xml_text .= "<MEMBERS>\n";
   $xml_text .= "<MEMBER>\n";
   $xml_text .= "<EMAIL_ADDRESS>".$results[0]['email']."</EMAIL_ADDRESS>\n";
   $xml_text .= "<ALERTS>\n";
   $xml_text .= $xml_insert;
   $xml_text .= "</ALERTS>\n";
   $xml_text .= "</MEMBER>\n";
   $xml_text .= "</MEMBERS>\n";
   $xml_text .= "</COMPANY>\n";
   $xml_text .= "</ALERT_SUBSCRIPTION>";
   
   
   $url = "http://www.corporate-ir.net/ireye/xmlsub.asp";
   
   require_once "HTTP/Request.php";

   $req =& new HTTP_Request($url);
   $req->setMethod(HTTP_REQUEST_METHOD_POST);
   if ($_HCG_GLOBAL['proxy'] != "") {
      $req->setProxy($_HCG_GLOBAL['proxy'], $_HCG_GLOBAL['proxy_port']);
   }
   $req->addHeader("Content-Type", "application/xml");
   $req->addRawPostData($xml_text);

   $response = $req->sendRequest();

   if (PEAR::isError($response)) {
      $resultData['Response']['return_code_attribute_code'] = 1;
      $resultData['Response']['errors']['error'][0] = $response->getMessage();
   } else {
      $result = $req->getResponseBody();
      unset($_HCG_GLOBAL['XML_LIST_ELEMENTS']);
      $_HCG_GLOBAL['XML_LIST_ELEMENTS'][] = "error";
      $resultData = makeXMLTree($result);
   }
   
   if ($resultData['ALERT_SUBSCRIPTION']['Response']['return_code_attribute_code'] != 0) {
      $returnData[0] = "error";
      $returnData[1] = $resultData['ALERT_SUBSCRIPTION']['Response']['errors']['error'];
   } elseif ($resultData['ALERT_SUBSCRIPTION']['COMPANY']['MEMBERS']['MEMBER']['Response']['return_code_attribute_code'] != 0) {
      $returnData[0] = "error";
      $returnData[1] = $resultData['ALERT_SUBSCRIPTION']['COMPANY']['MEMBERS']['MEMBER']['Response']['errors']['error'];
   } else {
      $query2 = "UPDATE ir_alert ".
               "SET completed=1 ".
               "WHERE id = ".$results[0]['id']." ".
               "LIMIT 1";
      $db->Execute($query2);
      $returnData[0] = "OK";
      $returnData[1] = $form_data;
   }   
   return $returnData;
}


//-------------------------------------------------------------------------
// irEvents
//	This is to process the events calendar xml.  It will loop through listed
//	events and find only events that are today or later so we don't show past
//	events
//
//-------------------------------------------------------------------------
function irEvents($pageData)
{
   $eventList = $pageData[0]['IRXML']['Events']['Event'];
   //echo "<pre>Events Data:\n"; print_r($eventList); echo "</pre>";
   
   $today = date('Ymd');
   $keepCnt = 0;
   
   for ($i=0; $i<count($eventList); $i++) {
      if ($eventList[$i][EventStartDateTime_attribute_Date] >= $today ) {
           $newEventList[$keepCnt] = $eventList[$i];
           $keepCnt++;
      }
   }
   return $newEventList;
}


//-------------------------------------------------------------------------
// irEventDetail
//	This is to process the events calendar xml.  It will loop through listed
//	events and find only events that are today or later so we don't show past
//	events
//
//-------------------------------------------------------------------------
function irEventDetail($pageData)
{
   global $_HCG_GLOBAL;
   
   $eventList = $pageData[0]['IRXML']['Events']['Event'];
   
   for ($i=0; $i<count($eventList); $i++) {
      if ($eventList[$i][EventID] == $_HCG_GLOBAL['passed_vars']['item'] ) {
           $newEventList[0] = $eventList[$i];
           break;
      }
   }
   // get the parent record if applicable
   if ($newEventList[0]['ParentEventID'] > 0) {
      for ($i=0; $i<count($eventList); $i++) {
         if ($eventList[$i][EventID] == $newEventList[0]['ParentEventID'] ) {
              $newEventList[1] = $eventList[$i];
              break;
         }
      }
   }
   return $newEventList;
}


//-------------------------------------------------------------------------
// irEventHeaders
//	This is to process the events calendar xml.  It will loop through listed
//	events and find only events where
//     1. $headerLink['Display'] == "yes"
//     2. AdjustedDisplayTimestamp <= $now
//
//  Need to add sorting the list by $eventList[$i][HeaderLink][DisplayOrder]
//
//-------------------------------------------------------------------------
function irEventHeaders($pageData)
{
   $eventList = $pageData[0]['IRXML']['Events']['Event'];
   //echo "<pre>Events Data:\n"; print_r($eventList); echo "</pre>";
   
   $now = time();
   $keepCnt = 0;
   $keepThis = false;
   
   for ($i=0; $i<count($eventList); $i++) {

      $headerList = $eventList[$i]['HeaderLinks']['HeaderLink'];
      for ($j=0; $j<count($headerList); $j++) {

         $headerLink = $eventList[$i]['HeaderLinks']['HeaderLink'][$j];

         list($hr, $min, $sec) = explode(":", $headerLink['EndDisplayTime']);
         $yr = substr($headerLink['EndDisplayDate'], 0, 4);
         $mon = substr($headerLink['EndDisplayDate'], 4, 2);
         $day = substr($headerLink['EndDisplayDate'], 6, 2);
         $display_ts = mktime($hr, $min, $sec, $mon, $day, $yr);
         $displayto = irAdjustTime($display_ts, $headerLink['TimeZone']);

         if (strtolower($headerLink['Display']) == "yes" && $now <= $displayto) {
            $keepThis = true;
         } else {
            // delete any HeaderLinks that shouldn't be displayed
            $eventList[$i]['HeaderLinks']['HeaderLink'][$j] = array();
         }
      }
      if ($keepThis == true) {
         $newEventList[$keepCnt] = $eventList[$i];
         $keepCnt++;
         $keepThis = false;
      }
   }
   return $newEventList;
}


//-------------------------------------------------------------------------
// irInfoRequest
//   Uses the PEAR class QuickForm.
//
//-------------------------------------------------------------------------

function irInfoRequest($pageData, &$t)
{
   global $_HCG_GLOBAL;

   require_once 'HTML/QuickForm.php';
   require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

   $form = new HTML_QuickForm('info_request', null, null, null, null, true);
   
   $countries = $pageData[1]['IRXML']['InfoReq']['InfoRequestFormFields']['InfoRequestFormField'][9]['FormFieldValues']['FormFieldValue'];
   for ($i=0; $i<count($countries); $i++) {
      $countryArray[$countries[$i]['ValueID']] = $countries[$i]['Value'];
   }

   $form->addElement('hidden', 'RequestSource', '0');
   $form->addElement('text', 'first_name', 'First Name:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'last_name', 'Last Name:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'title', 'Title:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'institution', 'Organization:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'addr1', 'Address 1:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'addr2', 'Address 2:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'city', 'City:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'state', 'State/Province:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'zip', 'Zip Code:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('select', 'country', 'Country:', $countryArray);
   $form->addElement('text', 'telephone', 'Phone #:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('text', 'fax', 'Fax #:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('radio', 'phone_type', 'Home ', '', 'HOME', array('onclick' => 'onclick_radio("HOME")'));
   $form->addElement('radio', 'phone_type', 'Work ', '', 'WORK', array('onclick' => 'onclick_radio("WORK")'));
   $form->addElement('text', 'email', 'E-mail:', array('size' => 20, 'maxlength' => 255));
   $form->addElement('textarea', 'comment', 'Question/Comment Box:', array('cols' => 20, 'rows' => 6, 'wrap' => 'soft', 'onkeyup' => 'textarealimit(this,255)'));
   $form->addElement('submit', 'submit', 'Submit Form');
   $form->addElement('reset', 'reset', 'Clear Form');

   $form->applyFilter('first_name', 'trim');
   $form->applyFilter('last_name', 'trim');
   $form->applyFilter('institution', 'trim');
   $form->applyFilter('email', 'trim');

   $form->addRule('first_name', 'You must enter your first name.', 'required', null, 'client');
   $form->addRule('last_name', 'You must enter your last name.', 'required', null, 'client');
   $form->addRule('institution', 'You must enter your organization name.', 'required', null, 'client');
   $form->addRule('email', 'You must enter your email.', 'required', null, 'client');

   if ($form->validate()) {
      $form_data = process_info_request($form);
      $display_response = true;
   } else {
      $renderer =& new HTML_QuickForm_Renderer_ArraySmarty($t);
      $renderer->setRequiredTemplate('{$label}{if $required}<span class="red">*</span>{/if}');
      $renderer->setErrorTemplate('{$label}{if $error}<br><span class="red">{$error}</span>{/if}');    
      $form->accept($renderer);
      $form_data = $renderer->toArray();
      $display_response = false;
      
      // display converted XML data if desired
      if ($_HCG_GLOBAL['ir_display_data'] == true) {
         echo "<pre>Form Data:\n"; print_r($form_data); echo "</pre>";
      }
   }

   $results[0] = $display_response;
   $results[1] = $form_data;

   return $results;
}

//-------------------------------------------------------------------------
// process_info_request
//   Process alerts sign up form. This entails formatting an email and
//   sending to the person signing up to confirm their email address.
//
//-------------------------------------------------------------------------

function process_info_request(&$form)
{
   global $_HCG_GLOBAL;

   $values = $form->exportValues();

   $xml_text = "<?xml version=\"1.0\" ?".">\n";
   $xml_text .= "<inforequest ID=\"87078\">\n";
   $xml_text .= "<UserData>\n";
   $xml_text .= "<fname>".$values['first_name']."</fname>\n";
   $xml_text .= "<lname>".$values['last_name']."</lname>\n";
   $xml_text .= "<title>".$values['title']."</title>\n";
   $xml_text .= "<institution>".$values['institution']."</institution>\n";
   $xml_text .= "<addr1>".$values['addr1']."</addr1>\n";
   $xml_text .= "<addr2>".$values['addr2']."</addr2>\n";
   $xml_text .= "<city>".$values['city']."</city>\n";
   $xml_text .= "<state>".$values['state']."</state>\n";
   $xml_text .= "<zip>".$values['zip']."</zip>\n";
   $xml_text .= "<country>".$values['country']."</country>\n";
   $xml_text .= "<telephone>".$values['telephone']."</telephone>\n";
   $xml_text .= "<fax>".$values['fax']."</fax>\n";
   $xml_text .= "<email>".$values['email']."</email>\n";
   $xml_text .= "<comment>".$values['comment']."</comment>\n";
   $xml_text .= "</UserData>\n";
   $xml_text .= "</inforequest>\n";
   
   $url = "http://www.corporate-ir.net/ireye/xmlreq.asp";
   
//   echo "<pre>".htmlentities($xml_text)."</pre>";
   
   require_once "HTTP/Request.php";

   $req =& new HTTP_Request($url);
   $req->setMethod(HTTP_REQUEST_METHOD_POST);
   if ($_HCG_GLOBAL['proxy'] != "") {
      $req->setProxy($_HCG_GLOBAL['proxy'], $_HCG_GLOBAL['proxy_port']);
   }

   $req->addHeader("Content-Type", "application/xml");

   $req->addRawPostData($xml_text);

   $response = $req->sendRequest();

   if (PEAR::isError($response)) {
      $resultData['Response']['return_code_attribute_code'] = 1;
      $resultData['Response']['errors']['error'][0] = $response->getMessage();
   } else {
      $result = $req->getResponseBody();
      unset($_HCG_GLOBAL['XML_LIST_ELEMENTS']);
      $_HCG_GLOBAL['XML_LIST_ELEMENTS'][] = "error";
      $resultData = makeXMLTree($result);
   }
   
//   echo "<pre>"; print_r($resultData); echo "</pre>";
   
   if ($resultData['Response']['return_code_attribute_code'] != 0) {
      return $resultData['Response']['errors']['error'];
   } else {
      return "OK";
   }
   
}


//-------------------------------------------------------------------------
// process_info_request
//   Process alerts sign up form. This entails formatting an email and
//   sending to the person signing up to confirm their email address.
//
//-------------------------------------------------------------------------

function irReleaseText($pageData)
{
   $results = $pageData[0]['IRXML'];
   
   // determine if the Comtex blurb is required
   if (strpos($results['NewsReleaseText']['ReleaseText'], "via COMTEX")) {
      $results['Comtex'] = true;
   } else {
      $results['Comtex'] = false;
   }
      
   return $results;
}

?>
