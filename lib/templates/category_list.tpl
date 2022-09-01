<p class="pageHd">{$category.title}</p>

<table width="100%" cellspacing="0" cellpadding="2" border="0">

{section name="catitem" loop=$items}

   {if file_exists("/var/opt/httpd/amdocs`$items[catitem].ThumbFile`") eq false }
      {assign var=thumb_file value="/images/products/w080/placeholder.jpg"}
   {else}
      {assign var=thumb_file value=$items[catitem].ThumbFile }
   {/if}

   <tr>
	
   <!-- left column of main area -->
   <td valign="top" align="left"><a href="/products/product.php?prod_id={$items[catitem].ProductID}&cat_name={$category.id}"><img src="{$thumb_file}" width="{$items[catitem].ThumbWidth}" height="{$items[catitem].ThumbHeight}" alt="{$items[catitem].ThumbAlt}" border="0"></a></td>
	
   <!-- Gutter -->
   <td valign="top">&nbsp;</td>
	
   <!-- right column of main area -->
   <td valign="top" align="left"><p><b><a href="/products/product.php?prod_id={$items[catitem].ProductID}&cat_name={$category.id}">{$items[catitem].ProductName}</a></b>
   <br>{$items[catitem].LongDescription}</p></td>

   </tr>
	
{/section}

   <tr>
<td width="165"><img height="1" width="80" src="/images/dot_clear.gif" alt=""></td>
<td width="15"><img height="1" width="15" src="/images/dot_clear.gif" alt=""></td>
<td width="100%"><img height="1" width="401" src="/images/dot_clear.gif" alt=""></td>
</tr>

</table>
