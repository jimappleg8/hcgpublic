{if $postcard.display_response != true}

<center>

<table width="600" border="0" cellpadding="0" cellspacing="0">

<tr> 
<td colspan="2"><img src="/justforfun/e-postcards/images/mark.gif" width="146" height="109" alt="postmark Boulder, Colorado"><img src="/justforfun/e-postcards/images/grn_send.gif" width="181" height="92" alt="send a Postcard to a Friend" border="0"></td>
</tr>

<tr>
<td width="200" valign="top">

   <table border="0" cellspacing="0" cellpadding="4">
   {section name="quote" loop=$quotes}

   <tr>
   <td><b>{$quotes[quote].letter}</b> "{$quotes[quote].Quotation}"
   <br><b>&mdash;{$quotes[quote].Author}</b></td>

   {/section}
   </table>

</td>
<td valign="top">&nbsp; &nbsp; &nbsp; &nbsp;<img src="/justforfun/e-postcards/images/{$artwork.SmallFile}" width="{$artwork.SmallWidth}" height="{$artwork.SmallHeight}" alt="{$artwork.SmallAlt}" border="0"></td>
</tr>

<tr>
<td colspan="2">{$form_html}</td>
</tr>

</table>

</center>

{else}

<center>

<p>&nbsp;</p>

<h1 class="pageHd">Thank you!
<br>Your postcard will be mailed immediately</h1>

<p><a href="/justforfun/e-postcards/index.php">Send another postcard?</a>
<br>
<br><a href="/index.php">Return to Home Page</a></p>

</center>

{/if}