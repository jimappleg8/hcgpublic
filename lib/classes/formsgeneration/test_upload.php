<?php
/*
 * test_upload.html
 *
 * @(#) $Header: /home/mlemos/cvsroot/forms/test_upload.php,v 1.5 2004/06/20 07:57:26 mlemos Exp $
 *
 */

?><html>
<head>
<title>Test for Manuel Lemos' PHP form class to upload a file</title>
</head>
<body onload="PageLoad()" bgcolor="#cccccc">
<h1><center>Test for Manuel Lemos' PHP form class to upload a file</center></h1>
<hr />
<?php

Function Output($text)
{
	echo $text;
}

Function OutputDebug($error)
{
	echo "$error\n";
}

	require("forms.php");
	$form=new form_class;
	$form->NAME="subscription_form";
	$form->METHOD="POST";
	$form->ACTION="";
	$form->ENCTYPE="multipart/form-data";
	$form->debug="OutputDebug";
	$form->ResubmitConfirmMessage="Are you sure you want to submit this form again?";
	$form->AddInput(array(
		"TYPE"=>"file",
		"NAME"=>"userfile",
		"ACCEPT"=>"image/gif",
		"ValidateAsNotEmpty"=>1,
		"ValidationErrorMessage"=>"It was not specified a valid file to upload"
	));
	$form->AddInput(array(
		"TYPE"=>"submit",
		"VALUE"=>"Upload",
		"NAME"=>"doit"
	));
	$form->AddInput(array(
		"TYPE"=>"hidden",
		"NAME"=>"MAX_FILE_SIZE",
		"VALUE"=>1000000
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
  if($doit)
	{
		$form->GetFileValues("userfile",$userfile_values);
?>
<script type="text/javascript">
<!--
function PageLoad()
{
}
//-->
</script>
<noscript>
<!-- dummy comment for user agents without Javascript support enabled -->
</noscript>
<h2><center>The file was uploaded.</center></h2>
<center><table>

<tr>
<th align="right">Uploaded file path:</th>
<td><tt><?php echo $userfile_values["tmp_name"]; ?></tt></td>
</tr>

<tr>
<th align="right">Client file name:</th>
<td><tt><?php echo HtmlEntities($userfile_values["name"]); ?></tt></td>
</tr>

<tr>
<th align="right">File type:</th>
<td><tt><?php echo $userfile_values["type"]; ?></tt></td>
</tr>

<tr>
<th align="right">File size:</th>
<td><tt><?php echo $userfile_values["size"]; ?></tt></td>
</tr>

</table></center>
<?php
	}
  else
  {
		$form->StartLayoutCapture();
?>
<script type="text/javascript">
<!--
	loaded_MD5=false
//-->
</script>
<script type="text/javascript" src="md5.js">
</script>
<?php
		$title="Form upload file test";
		$body_template="form_upload_body.html.php";
		include("templates/form_frame.html.php");
		$form->EndLayoutCapture();

		if($error_message!="")
		{
			Reset($verify);
			$focus=Key($verify);
		}
		else
			$focus="userfile";
		$form->AddFunction(array(
			"Function"=>"PageLoad",
			"Type"=>"focus",
			"Element"=>$focus
		));

		$form->Output(array(
			"Function"=>"Output",
			"EndOfLine"=>"\n"
		));
	}
?>
<hr />
</body>
</html>
