<?php
	if($error_message!="")
	{

/*
 * There was a validation error.  Display the error message associated with
 * the first field in error.
 */
?>
<center><table summary="Validation error table" border="1" bgcolor="#c0c0c0" cellpadding="2" cellspacing="1">
<tr>
<td bgcolor="#808080" style="border-style: none"><table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td><b><font color="#c0c0c0">Validation error</font></b></td>
</tr>
</table></td>
</tr>
<tr>
<td style="border-style: none"><table cellpadding="0" cellspacing="0">
<tr>
<td><table frame="box" bgcolor="#FF8000">
<tr>
<td><b>!</b></td>
</tr>
</table></td>
<td><table>
<tr>
<td><b><?php echo $error_message; ?></b></td>
</tr>
</table></td>
</tr>
<?php
/*
 * If there was more than on field in error let the user know.
 */
		if(count($verify)>1)
		{
?>
<tr>
<td><table frame="box" bgcolor="#FF8000">
<tr>
<td><b>!</b></td>
</tr>
</table></td>
<td><table>
<tr>
<td><b>Please verify also the remaining fields marked with [Verify]</b></td>
</tr>
</table></td>
</tr>
<?php
		}
?>
</table></td>
</tr>
</table></center>
<br />
<?php
	}
?>
<center><table summary="Form table" border="1" bgcolor="#c0c0c0" cellpadding="2" cellspacing="1">
<tr>
<td bgcolor="#000080" style="border-style: none;"><font color="#ffffff"><b><?php echo $title; ?></b></font></td>
</tr>

<tr>
<td style="border-style: none;"><?php

	/*
	 * Include the form body template
	 */
	include($body_template);

?></td>
</tr>
</table></center>

