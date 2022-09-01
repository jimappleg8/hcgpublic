
<table width="150" cellpadding="0" cellspacing="0" border="0">
<tr><td class="leftMenu"><img src="/images/dot_clear.gif" width="1" height="8" alt=""></td></tr>

{section name=menu loop=$menu_table}

{if ($menu_table[menu].sec_name == "spacer") }
   <tr><td class="leftMenu"><img src="/images/dot_clear.gif" width="1" height="8" alt=""></td></tr>
{elseif ($menu_table[menu].hilite == 1) }
   <tr><td class="leftMenu"><a href="{$menu_table[menu].link}" class="Menu{$menu_table[menu].level}y">{$menu_table[menu].sec_name}</a></td></tr>
{else}
   <tr><td class="leftMenu"><a href="{$menu_table[menu].link}" class="Menu{$menu_table[menu].level}">{$menu_table[menu].sec_name}</a></td></tr>
{/if}

{/section}

</table>
