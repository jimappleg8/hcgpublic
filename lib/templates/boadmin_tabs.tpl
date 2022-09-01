{ if $data.tabs }
<table border="0" cellspacing="0" cellpadding="0"><tr>
{ foreach key=name item=url from=$data.tabs }
<td class="navtab{ if $name == $data.activetab }_on{ /if }">
  &nbsp;<a href="{$url}" class="navtablink{ if $name == $data.activetab }_on{ /if }">{$name}</a>&nbsp;
</td>
<td>&nbsp;&nbsp;</td>
{ /foreach }
</tr></table>
{ /if }
