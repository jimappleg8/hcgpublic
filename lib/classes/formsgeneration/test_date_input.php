<?php
/*
 *
 * @(#) $Id: test_date_input.php,v 1.3 2004/06/20 07:57:26 mlemos Exp $
 *
 */

Function OutputDebug($error)
{
	echo "$error\n";
}

	require("forms.php");
	require("form_date.php");

	$day_seconds=60*60*24;
	$start_date=strftime("%Y-%m-%d",time()+1*$day_seconds);
	$end_date=strftime("%Y-%m-%d",time()+7*$day_seconds);
	$form=new form_class;
	$form->NAME="date_form";
	$form->METHOD="GET";
	$form->ACTION="";
	$form->debug="OutputDebug";
	$form->AddInput(array(
		"TYPE"=>"custom",
		"ID"=>"date",
		"LABEL"=>"<U>D</U>ate",
		"ACCESSKEY"=>"D",
		"CustomClass"=>"form_date_class",
		"VALUE"=>strftime("%Y-%m-%d"),
		"Format"=>"{day}/{month}/{year}",
		"Months"=>array(
			"01"=>"January",
			"02"=>"February",
			"03"=>"March",
			"04"=>"April",
			"05"=>"May",
			"06"=>"June",
			"07"=>"July",
			"08"=>"August",
			"09"=>"September",
			"10"=>"October",
			"11"=>"November",
			"12"=>"December"
		),
		"ValidationStartDate"=>$start_date,
		"ValidationStartDateErrorMessage"=>"It was specified a schedule date before the start date.",
		"ValidationEndDate"=>$end_date,
		"ValidationEndDateErrorMessage"=>"It was specified a schedule date after the end date.",
	));
	$form->AddInput(array(
		"TYPE"=>"submit",
		"VALUE"=>"Schedule",
		"NAME"=>"doit"
	));
	$form->LoadInputValues($form->WasSubmitted("doit"));
	$verify=array();
	if($form->WasSubmitted("doit"))
	{
		if(($error_message=$form->Validate($verify))=="")
			$doit=1;
		else
		{
			$doit=0;
			$error_message=HtmlEntities($error_message);
		}
	}
	else
	{
		$error_message="";
		$doit=0;
	}
?><html>
<head>
<title>Test for Manuel Lemos's PHP form class using the date plug-in input</title>
</head>
<body <?php if(!$doit) echo "onload=\"PageLoad()\""; ?> bgcolor="#cccccc">
<h1><center>Test for Manuel Lemos's PHP form class using the date plug-in input</center></h1>
<hr>
<?php
  if($doit)
	{
?>
<center><h2>The task is scheduled to be started on <?php echo $form->GetInputValue("date"); ?></h2></center>
<?php
	}
	else
	{
		$form->StartLayoutCapture();
		$title="Form Date plug-in test";
		$body_template="form_date_body.html.php";
		include("templates/form_frame.html.php");
		$form->EndLayoutCapture();
		$form->AddFunction(array(
			"Function"=>"PageLoad",
			"Type"=>"focus",
			"Element"=>"date"
		));
		$form->DisplayOutput();
		$form->RemoveFunction("PageLoad");
	}
?>
<hr>
</body>
</html>
