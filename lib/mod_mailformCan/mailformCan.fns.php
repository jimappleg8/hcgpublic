<?php

// =========================================================================
// mailform.fns.php
// Sunil Gurung
//
// =========================================================================

define("DEBUG", 0);

require_once 'dbi_adodb.inc.php';
require_once 'formsprocessing.class.php';
require_once 'formsgeneration.inc.php';
require_once 'template.class.php';
require_once 'HTML/QuickForm.php';

//-------------------------------------------------------------------------
// TAG: promo_form
//   used to process the form data from 'Promotion' forms ;
//   includes: sending internal
//-------------------------------------------------------------------------

function promo_form($mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';

  $hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
  $siteid = $_HCG_GLOBAL['site_id'];
  $form_html = "";
  $firstName=""; $lastName=""; $birthDate=""; $gender=""; $address=""; $city=""; $province=""; $postCode="";
  $phone=""; $email="";
  $display_response = false;

//******** Retrieving Values from POST method **********/

if(isset($_HCG_GLOBAL['passed_vars']['income'])){

$firstName = $_HCG_GLOBAL['passed_vars']['firstName'];
$lastName = $_HCG_GLOBAL['passed_vars']['lastName'];
$birthDate = $_HCG_GLOBAL['passed_vars']['birthDate'];
$gender = $_HCG_GLOBAL['passed_vars']['gender'];
$address = $_HCG_GLOBAL['passed_vars']['address'];
$city = $_HCG_GLOBAL['passed_vars']['city'];
$province = $_HCG_GLOBAL['passed_vars']['province'];
$postCode = $_HCG_GLOBAL['passed_vars']['postCode'];
$phone = $_HCG_GLOBAL['passed_vars']['phone'];
$email = $_HCG_GLOBAL['passed_vars']['email'];
$income =  $_HCG_GLOBAL['passed_vars']['income'];
$education =  $_HCG_GLOBAL['passed_vars']['education'];
$ages = $_HCG_GLOBAL['passed_vars']['ages'];
$source = $_HCG_GLOBAL['passed_vars']['source'];

$imaginesoup = $_HCG_GLOBAL['passed_vars']['imaginesoup'];
$ricedream = $_HCG_GLOBAL['passed_vars']['ricedream'];
$soydream = $_HCG_GLOBAL['passed_vars']['soydream'];
$garden = $_HCG_GLOBAL['passed_vars']['garden'];
$terrachips = $_HCG_GLOBAL['passed_vars']['terrachips'];
$nilespice = $_HCG_GLOBAL['passed_vars']['nilespice'];
$casbah = $_HCG_GLOBAL['passed_vars']['casbah'];
$yves = $_HCG_GLOBAL['passed_vars']['yves'];
$seasoning = $_HCG_GLOBAL['passed_vars']['seasoning'];
$spectrum = $_HCG_GLOBAL['passed_vars']['spectrum'];
$emailnotification = $_HCG_GLOBAL['passed_vars']['emailnotification'];
$emailpromotion = $_HCG_GLOBAL['passed_vars']['emailpromotion'];

//******************************************************/
 $error_msg='';
//*********** User Input Validation *********************/

        //1. First Name
      	if((trim ($firstName))=='') {
      		$error_msg.="* Please enter your First Name<br />";
      	}
      	//2. Second Name
      	if((trim($lastName))=='') {
      		$error_msg.="* Please enter your Last Name<br />";
      	}
        //3. birthDate
        $strdate = $birthDate;
        if((strlen( $strdate)<10)OR(strlen( $strdate)>10)){
          $error_msg.="* Please enter the date in 'dd/mm/yyyy' format <br />";
        }
        else{
        $pos=strpos($strdate,"/");
        $date=substr($strdate,0,($pos));
        $result=ereg("^[0-9]+$",$date,$trashed);
          if(!($result)){$error_msg.="* Please enter a Valid Date<br />";}
            else{
              if(($date<=0)OR($date>31)){$error_msg.="* Please enter a Valid Date <br />";}
          }
        $month=substr($strdate,($pos+1),($pos));
          if(($month<=0)OR($month>12)){$error_msg.="* Please enter a Valid Month <br />";}
          else{
            $result=ereg("^[0-9]+$",$month,$trashed);
            if(!($result)){$error_msg.="* Please enter a Valid Month<br />";}
          }
        $year=substr($strdate,($pos+4),strlen($strdate));
        $result=ereg("^[0-9]+$",$year,$trashed);
          if(!($result)){$error_msg.="* Please enter a Valid year <br />";}
          else{
            if(($year<1900)OR($year>2200)){$error_msg.="* Please enter a year between 1900-2200 <br />";}
            }
      }
        //4.Gender
        	if((trim($gender))=='') {
      		$error_msg.="* Please select your Gender<br />";
      	}
        switch ($gender)
          {
            case "male":
            $gender1="selected";
            break;
            case "female":
            $gender2="selected";
            break;
          }
      	 //5.Address
        	if((trim($address))=='') {
      		$error_msg.="* Please enter your Address<br />";
      	}
      	 //6.City
        	if((trim($city))=='') {
      		$error_msg.="* Please enter your City<br />";
      	}
        //7. Province
      	if((trim($province))=='') {
      		$error_msg.="* Please select your Province<br />";
      	}
        switch ($province)
          {
            case "AB":
            $prov1="selected";
            break;
            case "BC":
            $prov2="selected";
            break;
            case "MB":
            $prov3="selected";
            break;
            case "NB":
            $prov4="selected";
            break;
            case "NL":
            $prov5="selected";
            break;
            case "NS":
            $prov6="selected";
            break;
            case "ON":
            $prov7="selected";
            break;
            case "PE":
            $prov8="selected";
            break;
            case "QC":
            $prov9="selected";
            break;
            case "SK":
            $prov10="selected";
            break;
            case "YT":
            $prov11="selected";
            break;
          }

        //8. Postal Code
        $postCode = strtoupper($postCode);
        $len = strlen($postCode);
      	if((trim($postCode))==''&& ($len<6 || $len>7) ) {
          $error_msg.="* Please enter a valide Postal Code [A9B 1C3]<br />";
          $postCode="";      	}else{
          if ($len==6){
          if (!ereg("[A-Z][0-9][A-Z][0-9][A-Z][0-9]", $postCode)){
          $error_msg.="* Please enter a valide Postal Code [A9B 1C3]<br />";
          }
          $temp1 = substr($postCode, 0, 3);
          $temp2 = substr($postCode, 3, 3);
          $postCode = $temp1." ".$temp2;

          }
          if ($len==7){
          if (!ereg("[A-Z][0-9][A-Z][[:space:]][0-9][A-Z][0-9]", $postCode)){
          $error_msg.="* Please enter a valide Postal Code [A9B 1C3]<br />";
          }}
        }

        //9. Phone Number
        if((trim($phone))==''){
        	$error_msg.="* Please enter a telephone number<br>";
        }else{
        $regex = '^[(]?[2-9]{1}[0-9]{2}[) -]{0,2}' . '[0-9]{3}[- ]?' . '[0-9]{4}[ ]?' . '((x|ext)[.]?[ ]?[0-9]{1,5})?$^';
        	if(!preg_match($regex, $phone)){
      			$error_msg.="* Please enter a valid Phone Number<br>";}
        }

        //9. Email Address
      	if((trim($email))=='') {
      		$error_msg.="* Please enter an email<br>";
      	} else {
      		// check if email is a valid address in this format username@domain.com
      		if(!ereg("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]", $email))
      			$error_msg.="* Please enter a valid email address<br>";
      	}

      	//10.income
      	if((trim($income))=='') {
      		$error_msg.="* Please select your average household income<br />";
      	}else {
        switch (trim($income))
          {
            case "<25,000":
            $income1="selected";
            break;
            case "25,000-50,000":
            $income2="selected";
            break;
            case "50,000-75,000":
            $income3="selected";
            break;
            case "75,000+":
            $income4="selected";
            break;
          }}

      //11. education
       	if((trim($education))=='') {
      		$error_msg.="* Please select your  level of your education<br />";
      	}else {
        switch (trim($education))
          {
            case "High School":
            $edu1="selected";
            break;
            case "College":
            $edu2="selected";
            break;
            case "Undergraduate(University)":
            $edu3="selected";
            break;
            case "Post Graduate":
            $edu4="selected";
            break;}}

       //12. Ages
        	if((trim($ages))=='') {
      		$error_msg.="* Please select ages of the children in your household<br />";
      	}else {
        switch (trim($ages))
          {
            case "0":
            $age1="selected";
            break;
            case "1-4":
            $age2="selected";
            break;
            case "5-8":
            $age3="selected";
            break;
            case "9-12":
            $age4="selected";
            break;
            case "12+":
            $age5="selected";
            break;
          }}

         //13. Sources
        if((trim($source))=='') {
      		$error_msg.="* Please select the source<br />";
      	}else {
        switch (trim($source))
          {
            case "Magazine advertising":
            $source1="selected";
            break;
            case "Insert in newspaper":
            $source2="selected";
            break;
            case "In grocery store":
            $source3="selected";
            break;
            case "Online advertising":
            $source4="selected";
            break;
          }}

          //14. Imagine Soup
        if((trim($imaginesoup))=='') {
      		$error_msg.="* Please make selection for Imagine Soup<br />";
      	}else {
        switch (trim($imaginesoup))
          {
            case "Y":
            $imaginesoup1="selected";
            break;
            case "N":
            $imaginesoup2="selected";
            break;
          }}

        //15. Rice Dream
        if((trim($ricedream))=='') {
      		$error_msg.="* Please make selection for Rice Dream<br />";
      	}else {
        switch (trim($ricedream))
          {
            case "Y":
            $ricedream1="selected";
            break;
            case "N":
            $ricedream2="selected";
            break;
          }}

        //16. Soy Dream
        if((trim($soydream))=='') {
      		$error_msg.="* Please make selection for Soy Dream<br />";
      	}else {
        switch (trim($soydream))
          {
            case "Y":
            $soydream1="selected";
            break;
            case "N":
            $soydream2="selected";
            break;
          }}

        //17. Garden of Eatin Tortilla Chips
        if((trim($garden))=='') {
      		$error_msg.="* Please make selection for Garden of Eatin Tortilla Chips<br />";
      	}else {
        switch (trim($garden))
          {
            case "Y":
            $garden1="selected";
            break;
            case "N":
            $garden2="selected";
            break;
          }}

         //18. Garden of Terra Chips
        if((trim($terrachips))=='') {
      		$error_msg.="* Please make selection for Terra Chips<br />";
      	}else {
        switch (trim($terrachips))
          {
            case "Y":
            $terrachips1="selected";
            break;
            case "N":
            $terrachips2="selected";
            break;
          }}

          //19. Garden of Nile Spice
        if((trim($nilespice))=='') {
      		$error_msg.="* Please make selection for Nile Spice<br />";
      	}else {
        switch (trim($nilespice))
          {
            case "Y":
            $nilespice1="selected";
            break;
            case "N":
            $nilespice2="selected";
            break;
          }}

          //19. Casbah
        if((trim($casbah))=='') {
      		$error_msg.="* Please make selection for Casbah<br />";
      	}else {
        switch (trim($casbah))
          {
            case "Y":
            $casbah1="selected";
            break;
            case "N":
            $casbah2="selected";
            break;
          }}

          //20. Yves Veggie Cuisine
        if((trim($yves))=='') {
      		$error_msg.="* Please make selection for Yves Veggie Cuisine<br />";
      	}else {
        switch (trim($yves))
          {
            case "Y":
            $yves1="selected";
            break;
            case "N":
            $yves2="selected";
            break;
          }}

          //21. Celestial seasoning
        if((trim($seasoning))=='') {
      		$error_msg.="* Please make selection for Celestial Seasonings<br />";
      	}else {
        switch (trim($seasoning))
          {
            case "Y":
            $seasoning1="selected";
            break;
            case "N":
            $seasoning2="selected";
            break;
          }}

          //23. Spectrum
        if((trim($spectrum))=='') {
      		$error_msg.="* Please make selection for Spectrum<br />";
      	}else {
        switch (trim($spectrum))
          {
            case "Y":
            $spectrum1="selected";
            break;
            case "N":
            $spectrum2="selected";
            break;
          }}

        //24. Email Notification
        if ($emailnotification == 'Y'){
          $notification = "checked";
        }

        //25. Email Promotion
        if ($emailpromotion == 'Y'){
          $promotion = "checked";
        }
       // display error message if any, if not, proceed to other processing
      	if($error_msg==''){
      	   process_promo_form($mailtpl1, $mailtpl2);
      		 $display_response = true;
      	} else {
      		$form_html="<div id=\"errormsg\"><b>Invalid information entered.</b><br/>".$error_msg."<b>Please correct these fields.</b></div>";
      	}

}

//********Building a Promotion Form *****************

$form_html = $form_html . "<form name=\"Promo\" method=\"POST\" action=\"promocontest.php\">
       	<!-- firstName -->
        <div class=\"leftColumn\">
        <span class=\"contenttitle\">First name:*<br /></span>
          <input value=\"".$firstName."\" name=\"firstName\" type=\"text\" class=\"width200px\"/>
        </div>

        <!-- lastName -->
        <div class=\"rightColumn\"><span class=\"contenttitle\"><nobr>Last name:*</nobr><br /></span>
          <input value=\"".$lastName."\" name=\"lastName\" type=\"text\" class=\"width200px\"/>
        </div>

        <!-- birthDate -->
        <div class=\"leftColumn\"><span class=\"contenttitle\">Birth Date-'dd/mm/yyyy':*<br /></span>
              <input value=\"".$birthDate."\" name=\"birthDate\" type=\"text\" class=\"width200px\"/>
        </div>

        <!-- gender -->
        <div class=\"rightColumn\"><span class=\"contenttitle\">Gender:*<br /></span>
               <select name=\"gender\">
                <option value=\"\"> Select</option>
                <option value=\"male\"".$gender1.">Male</option>
                 <option value=\"female\"".$gender2.">Female</option>
              </select>
        </div>

        <!-- address -->
        <div class=\"clear\"><span class=\"contenttitle\">Street Address:*<br /></span>
              <input value=\"".$address."\" name=\"address\" type=\"text\" class=\"longWidth\"/>
        </div>

        <!-- city -->
       <div class=\"leftColumn\"><span class=\"contenttitle\">City/Town:*<br /></span>
              <input value=\"".$city."\" name=\"city\" type=\"text\" class=\"width200px\"/>
       </div>

        <!-- province -->
        <div class=\"middileColumn\"><span class=\"contenttitle\">Province:*<br /></span>
               <select name=\"province\">
                <option value=\"\"> Select</option>
                <option value=\"AB\"".$prov1.">AB</option>
                 <option value=\"BC\"".$prov2.">BC</option>
                 <option value=\"MB\"".$prov3.">MB</option>
                 <option value=\"NB\"".$prov4.">NB</option>
                 <option value=\"NL\"".$prov5.">NL</option>
                 <option value=\"NS\"".$prov6.">NS</option>
                 <option value=\"ON\"".$prov7.">ON</option>
                 <option value=\"PE\"".$prov8.">PE</option>
                 <option value=\"QC\"".$prov9.">QC</option>
                 <option value=\"SK\"".$prov10.">SK</option>
                 <option value=\"YT\"".$prov11.">YT</option>
              </select>
        </div>

        <!--postCode -->
        <div class=\"rightColumn\"><span class=\"contenttitle\">Postal Code:*<br /></span>
              <input value=\"".$postCode."\" name=\"postCode\" type=\"text\" class=\"postcodewidth\"/>
        </div>

        <!-- phone -->
        <div class=\"leftColumn\"><span class=\"contenttitle\">Day Time Phone Number*<br /></span>
              <input value=\"".$phone."\" name=\"phone\" type=\"text\" class=\"width200px\"/>
        </div>

        <!-- email -->
        <div class=\"rightColumn\"><span class=\"contenttitle\"><nobr>Email Address*</nobr><br /></span>
			        <input value=\"".$email."\" name=\"email\" type=\"text\" class=\"width200px\"/>
        </div>

			  <!-- Average Income/Education/Household -->
        <div class=\"clear\">
        <div class=\"leftColumn incomewidth\"><span class=\"contenttitle\">What is your <br />
              average household <br />
              income?*<br />
              </span>
              <select name=\"income\">
                <option value=\"\">Select</option>
                <option value=\"<25,000\"".$income1."><25,000</option>
                <option value=\"25,000-50,000\"".$income2.">25,000-50,000</option>
                <option value=\"50,000-75,000\"".$income3.">50,000-75,000</option>
                <option value=\"75,000+\"".$income4."> 75,000+</option>
              </select>
        </div>

        <div class=\"middileColumn incomewidth\"><span class=\"contenttitle\">What is the <br />
              level of your education?*<br />
              </span>
              <select name=\"education\">
                <option value=\"\">Select</option>
                <option value=\"High School\"".$edu1.">High School</option>
                <option value=\"College\"".$edu2.">College</option>
                <option value=\"Undergraduate(University)\"".$edu3.">Undergraduate-University</option>
                <option value=\"Post Graduate\"".$edu4.">Post Graduate</option>
              </select>

          </div>
        <div class=\"rightColumn incomewidth\"><span class=\"contenttitle\">What are the ages <br />
              of the children in <br />
              your household?*<br />
              </span>
              <select name=\"ages\">
                <option value=\"\">Select</option>
                <option value=\"0\"".$age1.">0</option>
                <option value=\"1-4\"".$age2.">1-4</option>
                <option value=\"5-8\"".$age3.">5-8</option>
                <option value=\"9-12\"".$age4.">9-12</option>
                <option value=\"12+\"".$age5.">12+</option>
              </select>
        </div>

        <!-- Kitchen content -->
        <div class=\"clear\"><span class=\"contenttitle\">How did you find out about the imagine a Kitchen Content?<br />
              </span>
              <select name=\"source\">
                <option value=\"\">Select</option>
                <option value=\"Magazine advertising\"".$source1.">Magazine advertising</option>
                <option value=\"Insert in newspaper\"".$source2.">Insert in newspaper</option>
                <option value=\"In grocery store\"".$source3.">In grocery store</option>
                <option value=\"Online advertising\"".$source4.">Online advertising</option>
              </select>
        </div>

        <!-- Purchase of Imagine product -->
        <div class=\"clear\"><span class=\"contenttitle\">Which of these brands have you purchased?</span> </div>

        <table>
          <tr>
            <td>Imagine Soup</td>
            <td><select name=\"imaginesoup\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$imaginesoup1.">Yes</option>
                <option value=\"N\"".$imaginesoup2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Rice Dream</td>
            <td><select name=\"ricedream\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$ricedream1.">Yes</option>
                <option value=\"N\"".$ricedream2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Soy Dream</td>
            <td><select name=\"soydream\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$soydream1.">Yes</option>
                <option value=\"N\"".$soydream2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Garden of Eatin Tortilla Chips</td>
            <td><select name=\"garden\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$garden1.">Yes</option>
                <option value=\"N\"".$garden2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Terra Chips</td>
            <td><select name=\"terrachips\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$terrachips1.">Yes</option>
                <option value=\"N\"".$terrachips2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Nile Spice</td>
            <td><select name=\"nilespice\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$nilespice1.">Yes</option>
                <option value=\"N\"".$nilespice2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Casbah</td>
            <td><select name=\"casbah\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$casbah1.">Yes</option>
                <option value=\"N\"".$casbah2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Yves Veggie Cuisine</td>
            <td><select name=\"yves\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$yves1.">Yes</option>
                <option value=\"N\"".$yves2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Celestial Seasonings</td>
            <td><select name=\"seasoning\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$seasoning1.">Yes</option>
                <option value=\"N\"".$seasoning2.">No</option>
              </select></td>
          </tr>
          <tr>
            <td>Spectrum</td>
            <td><select name=\"spectrum\">
                <option value=\"\">Select</option>
                <option value=\"Y\"".$spectrum1.">Yes</option>
                <option value=\"N\"".$spectrum2.">No</option>
              </select></td>
          </tr>
        </table>

        <div class=\"clear longWidth\">
        <p class=\"contenttitle\" id=\"checkboxtitle\">
        <input name=\"emailnotification\" type=\"checkbox\" value=\"Y\" ".$notification." />Yes,I would like to receive an email notifying me of any future Hain Celestial Contest news ,updates and special offers.
        </p>
        </div>

        <div class=\"clear longWidth\"><p class=\"contenttitle\" id=\"checkboxtitle\">
        <input name=\"emailpromotion\" type=\"checkbox\" value=\"Y\" ".$promotion." />Yes,I would like to receive occasional announcements from canadiannetstacks the contest hosting comany.
        </p>
        </div>

        <!-- send button -->
        <input  type=\"image\" src=\"/images/promo/send.gif\" name=\"submit_button\" alt=\"SEND\" class=\"sendImage\" />

      </form>
      <p> &nbsp; </p>";

// end form code

   $results[0] = $display_response;
   $results[1] = $form_html;
   return $results;
}


function process_promo_form($mailtpl1, $mailtpl2){

global $_HCG_GLOBAL;
  // Values retrieved from the POST method
$value['firstName'] = $_HCG_GLOBAL['passed_vars']['firstName'];
$value['lastName'] = $_HCG_GLOBAL['passed_vars']['lastName'];
$value['birthDate'] = $_HCG_GLOBAL['passed_vars']['birthDate'];
$value['gender'] = $_HCG_GLOBAL['passed_vars']['gender'];
$value['address'] = $_HCG_GLOBAL['passed_vars']['address'];
$value['city'] = $_HCG_GLOBAL['passed_vars']['city'];
$value['province'] = $_HCG_GLOBAL['passed_vars']['province'];
$value['postCode'] = $_HCG_GLOBAL['passed_vars']['postCode'];
$value['phone'] = $_HCG_GLOBAL['passed_vars']['phone'];
$value['email'] = $_HCG_GLOBAL['passed_vars']['email'];
$value['income'] =  $_HCG_GLOBAL['passed_vars']['income'];
$value['education'] =  $_HCG_GLOBAL['passed_vars']['education'];
$value['ages'] = $_HCG_GLOBAL['passed_vars']['ages'];
$value['source'] = $_HCG_GLOBAL['passed_vars']['source'];
$value['imaginesoup'] = $_HCG_GLOBAL['passed_vars']['imaginesoup'];
$value['ricedream'] = $_HCG_GLOBAL['passed_vars']['ricedream'];
$value['soydream'] = $_HCG_GLOBAL['passed_vars']['soydream'];
$value['garden'] = $_HCG_GLOBAL['passed_vars']['garden'];
$value['terrachips'] = $_HCG_GLOBAL['passed_vars']['terrachips'];
$value['nilespice'] = $_HCG_GLOBAL['passed_vars']['nilespice'];
$value['casbah'] = $_HCG_GLOBAL['passed_vars']['casbah'];
$value['yves'] = $_HCG_GLOBAL['passed_vars']['yves'];
$value['seasoning'] = $_HCG_GLOBAL['passed_vars']['seasoning'];
$value['spectrum'] = $_HCG_GLOBAL['passed_vars']['spectrum'];
$value['emailnotification'] = $_HCG_GLOBAL['passed_vars']['emailnotification'];
$value['emailpromotion'] = $_HCG_GLOBAL['passed_vars']['emailpromotion'];

if ($value['emailnotification'] == ''){
  $value['emailnotification'] = 'N';
}

if ($value['emailpromotion'] == ''){
  $value['emailpromotion'] = 'N';
}
$value['DateSent'] = date("Y-m-d");

   // insert data into database table
   $db = HCGNewConnection('hcg_public_master');
   $db->SetFetchMode(ADODB_FETCH_ASSOC);
   $query = "INSERT INTO wf_contest ".
            "(firstname, lastname, dob, gender, address, city, province, postalcode, phone, email, income, education, ages, source, imaginesoup, ricedream, soydream, garden, terrachips, nilespice, casbah, yves, seasoning, spectrum, emailnotification, emailpromotion, datesent) ".
            "VALUES ".
            "(\"".addslashes($value['firstName'])."\", ".
            "\"".addslashes($value['lastName'])."\", ".
            "\"".addslashes($value['birthDate'])."\", ".
            "\"".addslashes($value['gender'])."\", ".
            "\"".addslashes($value['address'])."\", ".
            "\"".addslashes($value['city'])."\", ".
            "\"".addslashes($value['province'])."\", ".
            "\"".addslashes($value['postCode'])."\", ".
            "\"".addslashes($value['phone'])."\", ".
            "\"".addslashes($value['email'])."\", ".
            "\"".addslashes($value['income'])."\", ".
            "\"".addslashes($value['education'])."\", ".
            "\"".addslashes($value['ages'])."\", ".
            "\"".addslashes($value['source'])."\", ".
            "\"".addslashes($value['imaginesoup'])."\", ".
            "\"".addslashes($value['ricedream'])."\", ".
            "\"".addslashes($value['soydream'])."\", ".
            "\"".addslashes($value['garden'])."\", ".
            "\"".addslashes($value['terrachips'])."\", ".
            "\"".addslashes($value['nilespice'])."\", ".
            "\"".addslashes($value['casbah'])."\", ".
            "\"".addslashes($value['yves'])."\", ".
            "\"".addslashes($value['seasoning'])."\", ".
            "\"".addslashes($value['spectrum'])."\", ".
            "\"".addslashes($value['emailnotification'])."\", ".
            "\"".addslashes($value['emailpromotion'])."\", ".
            "\"".addslashes($value['DateSent'])."\")";
    // if Required to store the form info to db execute the query
    // table: wf_contest;
   $db->Execute($query);

 // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $value);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }

}


//-------------------------------------------------------------------------
// TAG: contact_form
//   used to process the form data from 'contactus' forms ;
//   includes: sending internal
//-------------------------------------------------------------------------

function contact_form($mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';

  $hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
  $siteid = $_HCG_GLOBAL['site_id'];
  $form_html = "";
  $firstname ="";
  $lastname ="";
  $email ="";
  $message="";

  $display_response = false;

  //Validation
if(isset($_HCG_GLOBAL['passed_vars']['inquirytype'])){

$firstname = $_HCG_GLOBAL['passed_vars']['firstname'];
$lastname =$_HCG_GLOBAL['passed_vars']['lastname'];
$email = $_HCG_GLOBAL['passed_vars']['email'];
$message = $_HCG_GLOBAL['passed_vars']['message'];

    switch ($_HCG_GLOBAL['passed_vars']['inquirytype'])
    {
      case "Product Review / Comment":
      $option1="selected";
      break;
      case "Media Inquiries":
      $option2="selected";
      break;
      case "Other":
      $option3="selected";
      break;
      }
  $error_msg='';

      	if((trim($firstname))=='') {
      		$error_msg.="* Please enter your first name<br>";
      	}
      	if((trim($lastname))=='') {
      		$error_msg.="* Please enter your last name<br>";
      	}

      	if((trim($email))=='') {
      		$error_msg.="* Please enter an email<br>";
      	} else {
      		// check if email is a valid address in this format username@domain.com
      		if(!ereg("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]", $email))
      			$error_msg.="* Please enter a valid email address<br>";
      	}
        if(($_HCG_GLOBAL['passed_vars']['inquirytype'])=='') {
      		$error_msg.="* Please select the inquiry type subject <br />";
      	}

      	if((trim($message))=='') {
      		$error_msg.="* Please enter the message <br />";
      	}

	     // display error message if any, if not, proceed to other processing
      	if($error_msg==''){

      	   process_contact_form($mailtpl1, $mailtpl2);
      		 $display_response = true;
      	} else {
      		$form_html="<div id=\"errormsg\"><b>Errors</b><br/>".$error_msg."</div>";
      	}

}
$form_html = $form_html . "<form name=\"ContactUS\" method=\"POST\" action=\"contactus.php\">
                <input type=\"hidden\" value=\"ContactUsForm\" name=\"formid\" />
                <table width=\"550\" cellspacing=\"5\" cellpadding=\"0\" border=\"0\">
                    <tbody>
                        <tr valign=\"middle\">
                            <td width=\"90\" align=\"left\">&nbsp;</td>
                            <td align=\"left\">
				                    <select name=\"inquirytype\" >
                            <option value=\"\">Subject is (please select one)</option>
                            <option value=\"Product Review / Comment\"".$option1.">Product Review / Comment</option>
                            <option value=\"Media Inquiries\"".$option2.">Media Inquiries</option>
                            <option value=\"Other\"".$option3.">Other</option>
                            </select> </td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/name_first.gif\" width=\"81\" height=\"19\" /></td>
                            <td align=\"left\"><input value=\"".$firstname."\" name=\"firstname\"  maxlength=\"50\"  class=\"fulwidth\"/></td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/name_last.gif\" width=\"81\" height=\"19\" /></td>
                            <td align=\"left\"><input value=\"".$lastname."\" name=\"lastname\" class=\"fulwidth\" maxlength=\"50\" /></td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/email.gif\" width=\"81\" height=\"19\" /></td>
                            <td align=\"left\"><input value=\"".$email."\" name=\"email\" class=\"fulwidth\" maxlength=\"50\" /></td>
                        </tr>
                        <tr valign=\"top\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/message.gif\" width=\"81\" height=\"19\" /></td>
                            <td align=\"left\"><textarea  name=\"message\" cols=\"42\" rows=\"10\" class=\"fulwidth\">".$message."</textarea></td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" align=\"left\">&nbsp;</td>
                            <td align=\"right\">
				<input  type=\"image\"  align=\"middle\" src=\"/images/contactus/button_send.gif\" name=\"submit_button\" alt=\"SEND\" value=\"submit\"/></td>
                        </tr>
                    </tbody>
                </table>
            </form>";

// end form code
//Validation

   $results[0] = $display_response;
   $results[1] = $form_html;

   return $results;
}


function process_contact_form($mailtpl1, $mailtpl2){


   global $_HCG_GLOBAL;
  // Values retrieved from the POST method

   $values['Lname'] = $_HCG_GLOBAL['passed_vars']['lastname'];
   $values['Fname'] = $_HCG_GLOBAL['passed_vars']['firstname'];
   $values['Email'] = $_HCG_GLOBAL['passed_vars']['email'];
   $values['Message'] = $_HCG_GLOBAL['passed_vars']['message'];
   $values['Inquiry'] = $_HCG_GLOBAL['passed_vars']['inquirytype'];
   $values['fullname']= $values['Fname']." ".$values['Lname'];
   $values['DateSent'] = date("Y-m-d");
   $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

 // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $values);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }

}



//-------------------------------------------------------------------------
// TAG: contact_form_fr
//   used to process the form data from 'contactus' forms ;
//   includes: sending internal
//-------------------------------------------------------------------------

function contact_form_fr($mailtpl1, $mailtpl2)
{
   global $_HCG_GLOBAL;

   require_once $_HCG_GLOBAL['lib_dir'].'/mod_products/products.fns.php';
   require_once 'HTML/QuickForm.php';

  $hcg_site = get_brand_name($_HCG_GLOBAL['site_id']);
  $siteid = $_HCG_GLOBAL['site_id'];
  $form_html = "";
  $firstname ="";
  $lastname ="";
  $email ="";
  $message="";

  $display_response = false;

  //Validation
if(isset($_HCG_GLOBAL['passed_vars']['inquirytype'])){

$firstname = $_HCG_GLOBAL['passed_vars']['firstname'];
$lastname =$_HCG_GLOBAL['passed_vars']['lastname'];
$email = $_HCG_GLOBAL['passed_vars']['email'];
$message = $_HCG_GLOBAL['passed_vars']['message'];

    switch ($_HCG_GLOBAL['passed_vars']['inquirytype'])
    {
      case "Product Review / Comment":
      $option1="selected";
      break;
      case "Media Inquiries":
      $option2="selected";
      break;
      case "Other":
      $option3="selected";
      break;
      }
  $error_msg='';

      	if((trim($firstname))=='') {
      		$error_msg.="* Veuillez entrer votre pr&eacute;nom<br>";
      	}
      	if((trim($lastname))=='') {
      		$error_msg.="*  Veuillez entrer votre nom de famille<br>";
      	}

      	if((trim($email))=='') {
      		$error_msg.="* Veuillez entrer un courriel<br>";
      	} else {
      		// check if email is a valid address in this format username@domain.com
      		if(!ereg("[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]", $email))
      			$error_msg.="* Veuillez &eacute;crire un email address valide <br>";
      	}
        if(($_HCG_GLOBAL['passed_vars']['inquirytype'])=='') {
      		$error_msg.="* Veuillez choisir le type sujet d'enqu&ecirc;te <br />";
      	}

      	if((trim($message))=='') {
      		$error_msg.="* Veuillez entrer le message <br />";
      	}

	     // display error message if any, if not, proceed to other processing
      	if($error_msg==''){

      	   process_contact_form_fr($mailtpl1, $mailtpl2);
      		 $display_response = true;
      	} else {
      		$form_html="<div id=\"errormsg\"><b>Erreurs</b><br/>".$error_msg."</div>";
      	}

}
$form_html = $form_html . "<form name=\"ContactUS\" method=\"POST\" action=\"contactus.php\">
                <input type=\"hidden\" value=\"ContactUsForm\" name=\"formid\" />
                <table width=\"550\" cellspacing=\"5\" cellpadding=\"0\" border=\"0\">
                    <tbody>
                        <tr valign=\"middle\">
                            <td width=\"90\" align=\"left\">&nbsp;</td>
                            <td align=\"left\">
				                    <select name=\"inquirytype\" >
                            <option value=\"\">Le sujet est (choisir svp un)</option>
                            <option value=\"Product Review / Comment\"".$option1.">Evaluation des produit</option>
                            <option value=\"Media Inquiries\"".$option2.">Communiqu&eacute;s de presse</option>
                            <option value=\"Other\"".$option3.">Autres</option>
                            </select> </td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/name_first_fr.gif\" /></td>
                            <td align=\"left\"><input value=\"".$firstname."\" name=\"firstname\"  maxlength=\"50\"  class=\"fulwidth\"/></td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/name_last_fr.gif\" /></td>
                            <td align=\"left\"><input value=\"".$lastname."\" name=\"lastname\" class=\"fulwidth\" maxlength=\"50\" /></td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/email_fr.gif\" /></td>
                            <td align=\"left\"><input value=\"".$email."\" name=\"email\" class=\"fulwidth\" maxlength=\"50\" /></td>
                        </tr>
                        <tr valign=\"top\">
                            <td width=\"90\" class=\"form_text\"><img src=\"/images/contactus/message_fr.gif\"/></td>
                            <td align=\"left\"><textarea  name=\"message\" cols=\"42\" rows=\"10\" class=\"fulwidth\">".$message."</textarea></td>
                        </tr>
                        <tr valign=\"middle\">
                            <td width=\"90\" align=\"left\">&nbsp;</td>
                            <td align=\"right\">
				<input  type=image src=\"/images/contactus/button_send_fr.gif\" name=\"submit_button\" alt=\"SEND\"/></td>
                        </tr>
                    </tbody>
                </table>
            </form>";

// end form code
//Validation

   $results[0] = $display_response;
   $results[1] = $form_html;

   return $results;
}


function process_contact_form_fr($mailtpl1, $mailtpl2){


   global $_HCG_GLOBAL;
  // Values retrieved from the POST method

   $values['Lname'] = $_HCG_GLOBAL['passed_vars']['lastname'];
   $values['Fname'] = $_HCG_GLOBAL['passed_vars']['firstname'];
   $values['Email'] = $_HCG_GLOBAL['passed_vars']['email'];
   $values['Message'] = $_HCG_GLOBAL['passed_vars']['message'];
   $values['Inquiry'] = $_HCG_GLOBAL['passed_vars']['inquirytype'];
   $values['fullname']= $values['Fname']." ".$values['Lname'];
   $values['DateSent'] = date("Y-m-d");
   $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

 // send e-mail
   $sendmail = ini_get('sendmail_path');
   if (empty($sendmail)) {
      $sendmail = "/usr/sbin/sendmail -t ";
   }

   // send the email internally
   if ($mailtpl1 != "") {
      $m = new HCG_Smarty;
      $m->assign("mail", $values);
      $m->setTplPath($mailtpl1);
      $mail_content = $m->fetch($mailtpl1);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   }

   // send reply to user
   if ($mailtpl2 != "") {
      $n = new HCG_Smarty;
      $n->assign("mail", $values);
      $n->setTplPath($mailtpl2);
      $mail_content2 = $n->fetch($mailtpl2);

      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);
   }

}

?>