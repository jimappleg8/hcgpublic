{$form_data.javascript}

<form {$form_data.attributes}>

<div>

{$form_data.hidden}

<table border="0">

<tr>
<td style="text-align:right; vertical-align:top"><b>{$form_data.FName.label}</b></td>
<td style="text-align:left; vertical-align:top">{$form_data.FName.html}</td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><b>{$form_data.LName.label}</b></td>
<td style="text-align:left; vertical-align:top">{$form_data.LName.html}</td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><b>{$form_data.Email.label}</b></td>
<td style="text-align:left; vertical-align:top">{$form_data.Email.html}</td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><b>{$form_data.Email2.label}</b></td>
<td style="text-align:left; vertical-align:top">{$form_data.Email2.html}</td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><b>{$form_data.Comment.label}</b></td>
<td style="text-align:left; vertical-align:top">{$form_data.Comment.html}</td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><b></b></td>
<td style="text-align:left; vertical-align:top">{$form_data.Submit.html}</td>
</tr>

<tr>
<td width="120"><img src="/images/dot_clear.gif" width="120" height="1" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td>
</tr>

<tr>
<td></td>
<td style="text-align:right; vertical-align:top"><span style="font-size:80%; color:#F00;">*</span><span style="font-size:80%;"> denotes required field</span></td>
</tr>

</table>

</div>

</form>