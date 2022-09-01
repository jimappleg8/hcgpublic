{if $error == ""}

<div align="center">
<table cellspacing="0" cellpadding="0" border="0" >

<tr>
<td valign="top"><div align="center"><img src="/justforfun/e-postcards/images/grn_from.gif" width="298" height="112" alt="Your Postcard from a Friend"></div></td>
</tr>

<tr>
<td valign="top"><div align="center"><img src="/justforfun/e-postcards/images/{$artwork.LargeFile}" width="{$artwork.LargeWidth}" height="{$artwork.LargeHeight}" alt="{$artwork.LargeAlt}"></div></td>
</tr>

<tr>
<td>
   <table width="453" cellspacing="0" cellpadding="0" border="0">

   <tr>
   <td width="2" rowspan="5" bgcolor="#000000"><img src="/images/dot_black.gif" width="2" height="300" alt=""></td>
   <td width="12" bgcolor="#000000"><img src="/images/dot_black.gif" width="12" height="2" alt=""></td>
   <td width="200" bgcolor="#000000"><img src="/images/dot_black.gif" width="200" height="2" alt=""></td>
   <td width="12" bgcolor="#000000"><img src="/images/dot_black.gif" width="12" height="2" alt=""></td>
   <td width="1" bgcolor="#000000"><img src="/images/dot_black.gif" width="1" height="2" alt=""></td>
   <td width="12" bgcolor="#000000"><img src="/images/dot_black.gif" width="12" height="2" alt=""></td>
   <td width="200" bgcolor="#000000"><img src="/images/dot_black.gif" width="200" height="2" alt=""></td>
   <td width="12" bgcolor="#000000"><img src="/images/dot_black.gif" width="12" height="2" alt=""></td>
   <td width="2" rowspan="5" bgcolor="#000000"><img src="/images/dot_black.gif" width="2" height="300" alt=""></td>
   </tr>
   
   <tr>
   <td><img src="/images/dot_clear.gif" width="1" height="12" alt=""></td>
   <td colspan="5">&nbsp;</td>
   <td>&nbsp;</td>
   </tr>

   <tr>
   <td><img src="/images/dot_clear.gif" width="1" height="272" alt=""></td>
   <td valign="top"><div class="postcardQuote">{$quote.Quotation}</div>
   <div class="postcardAuthor">&mdash; {$quote.Author}</div>
   <p class="postcardText">{$postcard.Message}</p>
   <p class="postcardText">From {$postcard.FromName}
   <br><a href="mailto:{$postcard.FromEmail}">{$postcard.FromEmail}</p></td>
   <td>&nbsp;</td>
   <td bgcolor="#000000"><img src="/images/dot_black.gif" width="1" height="272" alt=""></td>
   <td>&nbsp;</td>
   <td valign="top"><div align="right"><img src="/justforfun/e-postcards/images/bear1.gif" width="130" height="96" alt="Stamp"></div>
   <div align="center">
   <p class="postcardText">To: <b>{$postcard.ToName}</b> 
   <br>{$postcard.ToEmail}</p></div></td>
   <td>&nbsp;</td>
   </tr>
   
   <tr>
   <td><img src="/images/dot_clear.gif" width="1" height="12" alt=""></td>
   <td colspan="5">&nbsp;</td>
   <td>&nbsp;</td>
   </tr>
   
   <tr>
   <td width="2" colspan="7" bgcolor="#000000"><img src="/images/dot_black.gif" width="449" height="2" alt=""></td>

   </table>	
</td>
</tr>

<tr>
<td valign="top"><a href="http://www.celestialseasonings.com" target="top"><img src="/justforfun/e-postcards/images/bearclic.gif" align="bottom" border="0"></a></td>
</tr>

</table>
</div>

{else}

<div align="center">
<p>&nbsp;</p>

<p><span class="pageHd">Error</span>
<br>{$error}</p>

<p><a href="/index.php">Return to Home Page</a></p>
</div>

{/if}