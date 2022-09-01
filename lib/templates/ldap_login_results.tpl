<form method="post" action="{$form_action}">

<center>

<table width="300" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
<tr><td align="center">

<table width="100%" cellpadding="4" cellspacing="0" border="0" bgcolor="#99CC33">
<tr><td align="center">

<table width="100%" cellpadding="12" cellspacing="0" border="0" bgcolor="#FFFFFF">

<tr>
<td><span class="PageHd">Intranet Log In</span>
<br>&nbsp;
<br><img src="/images/dot_black.gif" width="376" height="2" alt=""></td>
</tr>

<tr>
<td align="center">

{if $login_result == 0 }

<p align="center"><b>Sorry, your login failed.</b></p>

{elseif $login_result == 1 }

<p align="center"><b>You have successfully logged in.</b></p>

{elseif $login_result == 2 }

<p align="center"><b>You are already logged in.</b></p>

{/if}

<p align="center">[<a href="{$return_url}">Continue</a>]</p>

</td>
</tr>

</table>

</td></tr></table>

</td></tr></table>

</center>

</form>