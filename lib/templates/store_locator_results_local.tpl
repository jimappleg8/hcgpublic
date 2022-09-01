<font face="arial,helvetica,sans-serif">

<div align="center">
<center>

   <table width="70%" cellspacing="0" cellpadding="7" border="0">

   <tr>
   <td>

   <p>Results for <b>{$brand_name} products</b> within {$iri_query.SEARCHRADIUS} miles of <b>{$iri_query.ZIP}</b>
   <br>(&nbsp;{$brand_count} stores found.&nbsp;)</p>

   <p>These are stores where we know {$brand_name} products are sold. Please call the store to find out if they carry the specific product you're looking for.</p>

{if $brand_count != 0}
   
   {section name="store" loop=$brand_store}
   
   <p class="FieldCell"><b>{$brand_store[store].StoreName}</b>
   <br>{$brand_store[store].Address1}{if $brand_store[store].Address2 != ""},{$brand_store[store].Address2}{/if}, {$brand_store[store].City}, {$brand_store[store].State} {$brand_store[store].Zip} 
   <br>{$brand_store[store].Phone}
   <br>Distance: {$brand_store[store].distance}{if $brand_store[store].distance != "unknown"} mi.{/if}</p>

   {/section}

{/if}

   <p>&nbsp;</p>

   <p>-------------------------------------------</p>

   <p>Results for <b>All Hain Celestial Group products</b> within {$iri_query.SEARCHRADIUS} miles of <b>{$iri_query.ZIP}</b>
   <br>(&nbsp;{$company_count} stores found.&nbsp;)</p>

   <p>These are stores where we know some Hain Celestial Group products are sold. Please call the store to find out if they carry the specific brand and product you're looking for.</p>

{if $company_count != 0}
   
   {section name="store" loop=$company_store}
   
   <p class="FieldCell"><b>{$company_store[store].StoreName}</b>
   <br>{$company_store[store].Address1}{if $company_store[store].Address2 != ""},{$company_store[store].Address2}{/if}, {$company_store[store].City}, {$company_store[store].State} {$company_store[store].Zip} 
   <br>{$company_store[store].Phone}
   <br>Distance: {$company_store[store].distance}{if $company_store[store].distance != "unknown"} mi.{/if}</p>

   {/section}

{/if}

   </td></tr></table>

</center>
</div>

</font>