
<div align="center">
<center>

<hr width="90%" noshade align="center" size="2">

<p><span class="pageHd">Search Results</span>
<br><span class="pageSbhd"> for {$iri_query.PRODNAME} within {$iri_query.SEARCHRADIUS} miles of {$iri_query.ZIP}</span></p>

<hr width="90%" noshade align="center" size="2">

{if ($iri_count != 0) && ($iri_error == "ok")}

   <a name="grocery"></a>
   <table width="90%" cellspacing="0" cellpadding="7" border="0">

   <tr>
   <td colspan="3">
   <p align="center"><span class="pageHd">Grocery Stores</span>
   <br>(<b>{$iri_count} stores found</b> - page {$iri_query.STORESPAGENUM} of {$iri_pages})</p>
   <p align="center">We know these Grocery Stores carry {$iri_query.PRODNAME} based on scanner data provided by IRI. This information is for grocery stores only.<br><a href="#naturalfoods">For <b>Natural Foods Stores</b>, see below</a>.</p>
   </td>
   </tr>

   <tr>
   <td width="60%" class="HeadingCell"><b>GROCERY STORES</b></td>
   <td width="20%" class="HeadingCell"><b>DISTANCE</b></td>
   <td width="20%" class="HeadingCell"><b>MAP</b></td>
   </tr>

   {section name="store" loop=$iri_store}

   <tr>
   <td width="60%" valign="top" class="FieldCell"><b>{$iri_store[store].NAME}</b><br> 		{$iri_store[store].ADDRESS}, {$iri_store[store].CITY}, {$iri_store[store].STATE} {$iri_store[store].ZIP} <br>
      {$iri_store[store].PHONE}</td>
   <td width="20%" valign="top" class="FieldCell">{$iri_store[store].DISTANCE} mi.</td>
   <td width="20%" valign="top" class="FieldCell"><A HREF="http://www.infousa.com/cgi-bin/map/mqcustomconnect.cgi?link=map&icontitles=yes&level=9&POI1iconid=31&POI1name={$iri_store[store].NAME|escape:"url"}&POI1streetaddress={$iri_store[store].ADDRESS|escape:"url"}&POI1city={$iri_store[store].CITY}&POI1state={$iri_store[store].STATE}&POI1zip={$iri_store[store].ZIP}&POI1phone={$iri_store[store].PHONE}" target="InfoUSA" class="AnchorAction">InfoUSA</A></td>
   </tr>

      {assign var="last_store" value=$iri_store[store].NUMBER}

   {/section}

   </table>

   {if $iri_store[0].NUMBER != 1}

      {assign var="previous" value="yes"}
      {math equation="x - y" x=$iri_query.STORESPAGENUM y=1 assign="prevpage"}
      <p><a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$iri_query.SEARCHRADIUS}&storespagenum={$prevpage}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Previous page of grocery stores</a>

   {/if}

   {if $last_store < $iri_count}

      {if $previous == "yes"} | {else}<p>{/if}
      {math equation="x + y" x=$iri_query.STORESPAGENUM y=1 assign="nextpage"}
      <a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$iri_query.SEARCHRADIUS}&storespagenum={$nextpage}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Next page of grocery stores</a></p>

   {else}
      <p></p>
   {/if}

{elseif ($iri_count == 0) && ($iri_error == "ok")}

   {math equation="x + y" x=$iri_query.SEARCHRADIUS y=10 assign="plusten"}
   {math equation="x + y" x=$iri_query.SEARCHRADIUS y=20 assign="plustwenty"}

   <a name="grocery"></a>
   <table width="90%" cellspacing="0" cellpadding="7" border="0">

   <tr>
   <td>
   <p align="center"><span class="pageHd">Grocery Stores</span>
   <br>(<b>0 stores found</b>)</p>

   <p align="center">No grocery stores were found within {$iri_query.SEARCHRADIUS} miles radius.</p>

   <p align="center"><a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$plusten}&storespagenum={$iri_query.STORESPAGENUM}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Expand search radius to {$plusten} miles?</a>
   <br><a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$plustwenty}&storespagenum={$iri_query.STORESPAGENUM}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Expand search radius to {$plustwenty} miles?</a></p>

<p align="center"><a href="#naturalfoods">For <b>Natural Foods Stores</b>, see below.</a>
   </td>
   </tr>

  </table> 

{else}

   <a name="grocery"></a>
   <table width="90%" cellspacing="0" cellpadding="7" border="0">

   <tr>
   <td>
   <p align="center"><span class="pageHd">Grocery Stores</span>
   <br>(<b>0 stores found</b>)</p>
   <p align="center" style="color:red;"><b>{$iri_error} <a href="#naturalfoods">For <b>Natural Foods Stores</b>, see below.</a></b></p>
   </td>
   </tr>

   </table> 

{/if}

<!-- Local Results -->

<br>
{if $brand_count != 0}

   <a name="naturalfoods"></a>
   <table width="90%" cellspacing="0" cellpadding="7" border="0">
   
   <tr>
   <td colspan="3">
   <hr width="100%" noshade align="center" size="2">
   <p align="center"><span class="pageHd">Natural Food Stores</span>
   <br>(<b>{$company_count} stores found.</b>)</p>
   <p align="center">We know these <b>Natural Food Stores</b> carry the selected brand of products. Please call the store to find out if they carry the specific product that you are looking for.
   <br>&nbsp;
   <br>For more options, <a href="#others"><b>try the Additional Stores listed below</b></a>. There's a good chance they'll have what you want, but you'll want to call ahead to make sure.</p>
   </td>
   </tr>
   <tr>
   <td width="60%" class="HeadingCell"><b>NATURAL FOOD STORES</b></td>
   <td width="20%" class="HeadingCell"><b>DISTANCE</b></td>
   <td width="20%" class="HeadingCell"><b>MAP</b></td>
   </tr>

   {section name="store" loop=$brand_store}
   
      <tr>
      <td width="60%" valign="top" class="FieldCell"><b>{$brand_store[store].StoreName}</b>
      <br>{$brand_store[store].Address1}{if $brand_store[store].Address2 != ""},{$brand_store[store].Address2}{/if}, {$brand_store[store].City}, {$brand_store[store].State} {$brand_store[store].Zip} 
      <br>{$brand_store[store].Phone}</td>
      <td width="20%" valign="top" class="FieldCell">{$brand_store[store].distance}{if $brand_store[store].distance != "unknown"} mi.{/if}</td>
      <td width="20%" valign="top" class="FieldCell"><A HREF="http://www.infousa.com/cgi-bin/map/mqcustomconnect.cgi?link=map&icontitles=yes&level=9&POI1iconid=31&POI1name={$brand_store[store].StoreName|escape:"url"}&POI1streetaddress={$brand_store[store].Address1|escape:"url"}{if  $brand_store[store].Address2 != ""}+{$brand_store[store].Address2|escape:"url"}{/if}&POI1city={$brand_store[store].City}&POI1state={$brand_store[store].State}&POI1zip={$brand_store[store].Zip}&POI1phone={$brand_store[store].Phone}" target="InfoUSA" class="AnchorAction">InfoUSA</A></td>
      </tr>

   {/section}
   
   </table>

{else}

   <a name="naturalfoods"></a>
   <table width="90%" cellspacing="0" cellpadding="7" border="0">

   <tr>
   <td colspan="3">
   <hr width="100%" noshade align="center" size="2">
   <p align="center"><span class="pageHd">Natural Food Stores</span>
   <br>(<b>0 stores found.</b>)</p>
   <p align="center">No Results found for Natural Food Stores.
   <br>&nbsp;
   <br>For more options, <a href="#others"><b>try the stores listed below</b></a>. There's a good chance they'll have what you want, but you'll want to call ahead to make sure.</p>
   </td>
   </tr>

   </table>

{/if}

</center>
</div>


<!--  HAIN STORES -->

<div align="center">
<center>

{if $company_count != 0}

   <a name="others"></a>
   <table width="90%" cellspacing="0" cellpadding="7" border="0">
   <tr>
   <td colspan="3">
   <hr width="100%" noshade align="center" size="2">
   <p align="center"><span class="pageHd">Additional Natural Food Stores to Try</span>
   <br>(<b>{$company_count} stores found</b>)</p>
   <p align="center">We know these stores carry at least some of the <b>Hain Celestial Group family of products</b>. There's a good chance they'll have what you want, but you'll want to call ahead to make sure.</p>
   </td>
   </tr>

   <tr>
   <td width="60%" class="HeadingCell"><b>ADDITIONAL STORES</b></td>
   <td width="20%" class="HeadingCell"><b>DISTANCE</b></td>
   <td width="20%" class="HeadingCell"><b>MAP</b></td>
   </tr>

   {section name="store" loop=$company_store}
   
      <tr>
      <td width="60%" valign="top" class="FieldCell"><b>{$company_store[store].StoreName}</b>
      <br>{$company_store[store].Address1}{if $company_store[store].Address2 != ""},{$company_store[store].Address2}{/if}, {$company_store[store].City}, {$company_store[store].State} {$company_store[store].Zip} 
      <br>{$company_store[store].Phone}</td>
      <td width="20%" valign="top" class="FieldCell">{$company_store[store].distance}{if $company_store[store].distance != "unknown"} mi.{/if}</td>
      <td width="20%" valign="top" class="FieldCell"><A HREF="http://www.infousa.com/cgi-bin/map/mqcustomconnect.cgi?link=map&icontitles=yes&level=9&POI1iconid=31&POI1name={$company_store[store].StoreName|escape:"url"}&POI1streetaddress={$company_store[store].Address1|escape:"url"}{if $company_store[store].Address2 != ""}+{$company_store[store].Address2|escape:"url"}{/if}&POI1city={$company_store[store].City}&POI1state={$company_store[store].State}&POI1zip={$company_store[store].Zip}&POI1phone={$company_store[store].Phone}" target="InfoUSA" class="AnchorAction">InfoUSA</A><!--<br><a href="edit_store.php?action=edit&store_id={$company_store[store].StoreID}">Edit</a>--></td>
      </tr>

   {/section}
   
   <tr>
   <td colspan="3">&nbsp;</td>
   </tr>
   <tr>
   <td colspan="3" bgcolor="#FFFF00"><p align="center"><b>Do you know something about these stores that we don't?</b>
<br>If you know any of the stores in this last section (or one that isn't listed) carry {$brand_name} products, <a href="/about_us/webmaster.php">send a message to the webmaster</a> and help us make our store locator better for everyone.</p></td>
   </tr>

   </table>

{else}

   <a name="others"></a>
   <table width="90%" cellspacing="0" cellpadding="7" border="0">
   <tr>
   <td colspan="3">
   <hr width="100%" noshade align="center" size="2">
   <p align="center"><span class="pageHd">Additional Natural Food Stores to Try</span>
   <br>(<b>0 stores found</b>)</p>
   <p align="center">No Results found for Additional Natural Food Stores to Try.</p>
   </td>
   </tr>

   </table>

{/if}

</center>
</div>

</font>