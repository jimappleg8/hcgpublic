<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
   <title>patUser - Login Template</title>
   <style type="text/css">
      TD {font-family: Verdana, sans-serif; font-size: 11px; }
      INPUT {font-family: Verdana, sans-serif; font-size: 11px; }
   </style>
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#cc0000" alink="#cc0000" vink="#cc0000">

<form action="{$PATUSER_SELF}" method="post">
   <input type="Hidden" name="{$PATUSER_ACTION}" value="login">

   <table border="0" cellpadding="0" cellspacing="2">

   <tr>
   <td colspan="2">
      <h4>{$PATUSER_REALM}</h4>
      <b>Please Login to continue</b>
   </td>
   </tr>
		
{if $iserror == "yes"}

   <tr>
   <td colspan="2">
      Please correct the following errors:<br><br>
				
      {section name="error" loop=$errors}
         &middot;&nbsp;Error: {$errors[error].MESSAGE} (#{$errors[error].CODE})<br>
      {/section}
      <br>
   </td>
   </tr>
{/if}

   <tr>
   <td>Username:</td>
   <td><input type="Text" name="{$USERNAME}" value="{$USERNAME_VALUE}"></td>
   </tr>

   <tr>
   <td>Password: </td>
   <td><input type="Password" name="{$PASSWD}" value="{$PASSWD_VALUE}"></td>
   </tr>

   <tr>
   <td colspan="2" align="center"><input type="Submit" value="LOGIN"></td>
   </tr>
   
   </table>

</form>

</body>
</html>
