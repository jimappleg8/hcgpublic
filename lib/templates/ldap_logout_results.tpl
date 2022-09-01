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

{if $logout_result == 0 }

<p align="center"><b>Sorry, I could not log you out.</b></p>

{elseif $logout_result == 1 }

<p align="center"><b>You have been successfully logged out.</b></p>

{elseif $logout_result == 2 }

<p align="center"><b>You were not logged in, so you have not been logged out.</b></p>

{/if}

<p align="center"><a href="{$return_url}">Continue</a></p>

</td>
</tr>

</table>

</td></tr></table>

</td></tr></table>

</center>

</form>
