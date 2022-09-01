<table width="100%" cellpadding="2" cellspacing="0" border="0">

{section name=site_map loop=$site_data}

{if $site_data[site_map].level == 1}

<tr>
<td colspan="5">&nbsp;</td>
</tr>

<tr>
<td colspan="5">&gt; <a href="{$site_data[site_map].link}">{$site_data[site_map].sec_name|replace:"&gt; ":""}</a></td>
</tr>

{else}

<tr>
<td colspan='{math equation="x - 1" x=$site_data[site_map].level}'>&nbsp;</td>
<td colspan='{math equation="6 - x" x=$site_data[site_map].level}'>&gt; <a href="{$site_data[site_map].link}">{$site_data[site_map].sec_name|replace:"&gt; ":""}</a></td>
</tr>

{/if}
   
{/section}

<tr>
<td width="18"><img src="/images/dot_clear.gif" width="18" height="1" alt=""></td>
<td width="18"><img src="/images/dot_clear.gif" width="18" height="1" alt=""></td>
<td width="18"><img src="/images/dot_clear.gif" width="18" height="1" alt=""></td>
<td width="18"><img src="/images/dot_clear.gif" width="18" height="1" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td>
</tr>

</table>