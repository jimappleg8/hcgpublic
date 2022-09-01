<?php
/*
 * test_form.php
 *
 * @(#) $Header: /home/mlemos/cvsroot/forms/test_form.php,v 1.22 2004/07/14 08:32:45 mlemos Exp $
 *
 */

?><html>
<head>
<title>Test for Manuel Lemos' PHP form class</title>
</head>
<body onload="PageLoad()" bgcolor="#cccccc">
<center><h1>Test for Manuel Lemos' PHP form class</h1></center>
<hr>
<?php

/*
 * Callback function for debugging form programming errors
 */
Function OutputDebug($error)
{
	echo "$error\n";
}

/*
 * Include form class code.
 */
	require("forms.php");

/*
 * Create a form object.
 */
	$form=new form_class;

/*
 * Define the name of the form to be used for example in Javascript validation
 * code generated by the class.
 */
	$form->NAME="subscription_form";

/*
 * Use the GET method if you want to see the submitted values in the form
 * processing URL, or POST otherwise.
 */
	$form->METHOD="GET";

/*
 * Make the form be displayed and also processed by this script.
 */
	$form->ACTION="";

/*
 * Specify a debug output function you really want to output any programming errors.
 */
	$form->debug="OutputDebug";

/*
 * Define a warning message to display by Javascript code when the user
 * attempts to submit the this form again from the same page.
 */
	$form->ResubmitConfirmMessage="Are you sure you want to submit this form again?";

/*
 * Output previously set password values
 */
	$form->OutputPasswordValues=1;

/*
 * Define the form field properties even if they may not be displayed.
 */
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"email",
		"ID"=>"email",
		"MAXLENGTH"=>100,
		"Capitalization"=>"lowercase",
		"ValidateAsEmail"=>1,
		"ValidationErrorMessage"=>"It was not specified a valid e-mail address",
		"LABEL"=>"<u>E</u>-mail address",
		"ACCESSKEY"=>"E"
	));
	$form->AddInput(array(
		"TYPE"=>"select",
		"NAME"=>"credit_card_type",
		"ID"=>"credit_card_type",
		"VALUE"=>"unknown",
		"SIZE"=>2,
		"OPTIONS"=>array(
			"unknown"=>"Unknown",
			"mastercard"=>"Master Card",
			"visa"=>"Visa",
			"amex"=>"American Express",
			"dinersclub"=>"Diners Club",
			"carteblanche"=>"Carte Blanche",
			"discover"=>"Discover",
			"enroute"=>"enRoute",
			"jcb"=>"JCB"
		),
		"ValidationErrorMessage"=>"It was not specified a valid credit card type",
		"LABEL"=>"Credit card t<u>y</u>pe",
		"ACCESSKEY"=>"y"
	));
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"credit_card_number",
		"ID"=>"credit_card_number",
		"SIZE"=>20,
		"ValidateOptionalValue"=>"",
		"ValidateAsCreditCard"=>"field",
		"ValidationCreditCardTypeField"=>"credit_card_type",
		"ValidationErrorMessage"=>"It wasn't specified a valid credit card number",
		"LABEL"=>"Credit card <U>n</U>umber",
		"ACCESSKEY"=>"n"
	));
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"user_name",
		"ID"=>"user_name",
		"MAXLENGTH"=>60,
		"ValidateAsNotEmpty"=>1,
		"ValidationErrorMessage"=>"It was not specified a valid name",
		"LABEL"=>"<u>P</u>ersonal name",
		"ACCESSKEY"=>"P"
	));
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"age",
		"ID"=>"age",
		"ValidateAsInteger"=>1,
		"ValidationLowerLimit"=>18,
		"ValidationUpperLimit"=>65,
		"ValidationErrorMessage"=>"It was not specified a valid age",
		"LABEL"=>"<u>A</u>ge",
		"ACCESSKEY"=>"A"
	));
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"weight",
		"ID"=>"weight",
		"ValidateAsFloat"=>1,
		"ValidationLowerLimit"=>10,
		"ValidationErrorMessage"=>"It was not specified a valid weight",
		"LABEL"=>"<u>W</u>eight",
		"ACCESSKEY"=>"W"
	));
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"home_page",
		"ID"=>"home_page",
		"ReplacePatterns"=>array(
			"^\\s+"=>"",  /* trim whitespace at the beginning of the text value */
			"\\s+\$"=>"",  /* trim whitespace at the end of the text value */
			"^([wW]{3}\\.)"=>"http://\\1", /* Assume that URLs starting with www. start with http://www. */
			"^([^:]+)\$"=>"http://\\1", /* Assume that URLs that do not have a : in them are http:// */
			"^(http|https)://(([-!#\$%&'*+.0-9=?A-Z^_`a-z{|}~]+\.)+[A-Za-z]{2,6}(:[0-9]+)?)\$"=>"\\1://\\2/" /* Assume at least / as URI . */
		),
		"ValidateRegularExpression"=>'^(http|https)\://(([-!#\$%&\'*+.0-9=?A-Z^_`a-z{|}~]+\.)+[A-Za-z]{2,6})(\:[0-9]+)?(/)?/',
		"ValidationErrorMessage"=>"It was not specified a valid home page URL",
		"LABEL"=>"H<u>o</u>me page",
		"ACCESSKEY"=>"o"
	));
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"alias",
		"ID"=>"alias",
		"MAXLENGTH"=>20,
		"Capitalization"=>"uppercase",
		"ValidateRegularExpression"=>"^[a-zA-Z0-9]+$",
		"ValidateRegularExpressionErrorMessage"=>"The alias may only contain letters and digits",
		"ValidateAsNotEmpty"=>1,
		"ValidateAsNotEmptyErrorMessage"=>"It was not specified the alias",
		"ValidateMinimumLength"=>5,
		"ValidateMinimumLengthErrorMessage"=>"It was not specified an alias shorter than 5 characters",
		"LABEL"=>"Acce<u>s</u>s name",
		"ACCESSKEY"=>"s"
	));
	$form->AddInput(array(
		"TYPE"=>"password",
		"NAME"=>"password",
		"ID"=>"password",
		"ONCHANGE"=>"if(value.toLowerCase) value=value.toLowerCase()",
		"ValidateAsNotEmpty"=>1,
		"ValidationErrorMessage"=>"It was not specified a valid password",
		"LABEL"=>"Passwor<u>d</u>",
		"ACCESSKEY"=>"d",
		"ReadOnlyMark"=>"********"
	));
	$form->AddInput(array(
		"TYPE"=>"password",
		"NAME"=>"confirm_password",
		"ID"=>"confirm_password",
		"ONCHANGE"=>"if(value.toLowerCase) value=value.toLowerCase()",
		"ValidateAsEqualTo"=>"password",
		"ValidationErrorMessage"=>"The password is not equal to the confirmation",
		"LABEL"=>"<u>C</u>onfirm password",
		"ACCESSKEY"=>"C",
		"ReadOnlyMark"=>"********"
	));
	$form->AddInput(array(
		"TYPE"=>"text",
		"NAME"=>"reminder",
		"ID"=>"reminder",
		"ValidateAsNotEmpty"=>1,
		"ValidateAsNotEmptyErrorMessage"=>"It was not specified a reminder phrase",
		"ValidateAsDifferentFrom"=>"password",
		"ValidateAsDifferentFromErrorMessage"=>"The reminder phrase may not be equal to the password",
		"LABEL"=>"Password <u>r</u>eminder",
		"ACCESSKEY"=>"r"
	));
	$form->AddInput(array(
		"TYPE"=>"checkbox",
		"NAME"=>"notification",
		"ID"=>"email_notification",
		"VALUE"=>"email",
		"CHECKED"=>0,
		"MULTIPLE"=>1,
		"ValidateAsSet"=>1,
		"ValidateAsSetErrorMessage"=>"It were not specified any types of notification",
		"LABEL"=>"E-<u>m</u>ail",
		"ACCESSKEY"=>"m",
		"ReadOnlyMark"=>"[X]"
	));
	$form->AddInput(array(
		"TYPE"=>"checkbox",
		"NAME"=>"notification",
		"ID"=>"phone_notification",
		"VALUE"=>"phone",
		"CHECKED"=>0,
		"MULTIPLE"=>1,
		"LABEL"=>"P<u>h</u>one",
		"ACCESSKEY"=>"h",
		"ReadOnlyMark"=>"[X]"
	));
	$form->AddInput(array(
		"TYPE"=>"radio",
		"NAME"=>"subscription_type",
		"VALUE"=>"administrator",
		"ID"=>"administrator_subscription",
		"ValidateAsSet"=>1,
		"ValidateAsSetErrorMessage"=>"It was not specified the subscription type",
		"LABEL"=>"Adm<u>i</u>nistrator",
		"ACCESSKEY"=>"i",
		"ReadOnlyMark"=>"[X]"
	));
	$form->AddInput(array(
		"TYPE"=>"radio",
		"NAME"=>"subscription_type",
		"VALUE"=>"user",
		"ID"=>"user_subscription",
		"LABEL"=>"<u>U</u>ser",
		"ACCESSKEY"=>"U",
		"ReadOnlyMark"=>"[X]"
	));
	$form->AddInput(array(
		"TYPE"=>"radio",
		"NAME"=>"subscription_type",
		"VALUE"=>"guest",
		"ID"=>"guest_subscription",
		"LABEL"=>"<u>G</u>uest",
		"ACCESSKEY"=>"G",
		"ReadOnlyMark"=>"[X]"
	));
	$form->AddInput(array(
		"TYPE"=>"button",
		"NAME"=>"toggle",
		"ID"=>"toggle",
		"VALUE"=>"On",
		"ONCLICK"=>"this.value=(this.value=='On' ? 'Off' : 'On'); alert('The button is '+this.value);",
		"LABEL"=>"Toggle <U>b</U>utton",
		"ACCESSKEY"=>"b"
	));
	$form->AddInput(array(
		"TYPE"=>"checkbox",
		"NAME"=>"agree",
		"ID"=>"agree",
		"VALUE"=>"Yes",
		"ValidateAsSet"=>1,
		"ValidateAsSetErrorMessage"=>"You have not agreed with the subscription terms.",
		"LABEL"=>"Agree with the <u>t</u>erms",
		"ACCESSKEY"=>"t"
	));

	$form->AddInput(array(
		"TYPE"=>"submit",
		"ID"=>"button_subscribe",
		"VALUE"=>"Submit subscription",
		"ACCESSKEY"=>"u"
	));
	$form->AddInput(array(
		"TYPE"=>"image",
		"ID"=>"image_subscribe",
		"SRC"=>"http://www.phpclasses.org/graphics/add.gif",
		"ALT"=>"Submit subscription",
		"ExtraAttributes"=>array(
			"border"=>"0"
		)
	));

/*
 * Give a name to hidden input field so you can tell whether the form is to
 * be outputted for the first or otherwise it was submitted by the user.
 */
	$form->AddInput(array(
		"TYPE"=>"hidden",
		"NAME"=>"doit",
		"VALUE"=>1
	));

/*
 * Hidden fields can be used to pass context values between form pages,
 * like for instance database record identifiers or other information
 * that may help your application form processing scripts determine
 * the context of the information being submitted with this form.
 *
 * You are encouraged to use the DiscardInvalidValues argument to help
 * preventing security exploits performed by attackers that may spoof
 * invalid values that could be used for instance in SQL injection attacks.
 *
 * In this example, any value that is not an integer is discarded. If the
 * value was meant to be used in a SQL query, with this attack prevention
 * measure an attacker cannot submit SQL code that could be used to make
 * your SQL query retrieve unauthorized information to abuse your system.
 */
	$form->AddInput(array(
		"TYPE"=>"hidden",
		"NAME"=>"user_track",
		"VALUE"=>"0",
		"ValidateAsInteger"=>1,
		"DiscardInvalidValues"=>1
	));

/*
 * Load form input values eventually from the submitted form.
 */
	$form->LoadInputValues($form->WasSubmitted("doit"));

/*
 * Empty the array that will list the values with invalid field after validation.
 */
	$verify=array();


/*
 * Check if the global array variable corresponding to hidden input field is
 * defined, meaning that the form was submitted as opposed to being displayed
 * for the first time.
 */
	if($form->WasSubmitted("doit"))
	{


/*
 * Therefore we need to validate the submitted form values.
 */
		if(($error_message=$form->Validate($verify))=="")
		{

/*
 * It's valid, set the $doit flag variable to 1 to tell the form is ready to
 * processed.
 */
			$doit=1;

		}
		else
		{

/*
 * It's invalid, set the $doit flag to 0 and encode the returned error message
 * to escape any non-ASCII ISO-latin 1 characters and HTML special characters.
 */
			$doit=0;
			$error_message=HtmlEntities($error_message);
		}
	}
	else
	{

/*
 * The form is being displayed for the first time, so it is not ready to be processed
 * and there is no error message to display.
 */
		$error_message="";
		$doit=0;
	}
  if($doit)
  {

/*
 * The form is ready to be processed, just output it again as read only to
 * display the submitted values.  A real form processing script usually may
 * do something else like storing the form values in a database.
 */
  	$form->ReadOnly=1;
  }

/*
 * Compose the form output by including a HTML form template with PHP code
 * interleaaved with calls to insert form input field
 * parts in the layout HTML.
 */

	$form->StartLayoutCapture();
	$title="Form class test";
	$body_template="form_body.html.php";
	require("templates/form_frame.html.php");
 	$form->EndLayoutCapture();

	if($doit)
	{

/*
 * If the form was submitted and was valid, make a Javascript function named
 * PageLoad be generated to be used by the page ONLOAD event to do nothing.
 */
		$form->AddFunction(array(
			"Function"=>"PageLoad",
			"Type"=>"void"
		));
	}
	else
	{

/*
 * Otherwise make the PageLoad function give the input focus to the first form
 * field or the first invalid field.
 */
		if(strlen($error_message))
		{
/*
 * If there is at least one field with invalid values,
 * get the name of the first field in error to make it get the input focus
 * when the page is loaded.
 */
			Reset($verify);
			$focus=Key($verify);
		}
		else
		{

/*
 * Make the email field get the input focus when the page is loaded
 * if there was no previous validation error.
 */
			$focus="email";
		}

		$form->AddFunction(array(
			"Function"=>"PageLoad",
			"Type"=>"focus",
			"Element"=>$focus
		));
	}

/*
 * Output the form using the function named Output.
 */
	$form->DisplayOutput();
?>
</body>
</html>