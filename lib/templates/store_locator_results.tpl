{if ($iri_count != 0) && ($iri_error == "ok")}

<div align="center">
<center>

<p>Results for <b>{$iri_query.PRODNAME}</b> within {$iri_query.SEARCHRADIUS} miles of <b>{$iri_query.ZIP}</b>
<br>(&nbsp;{$iri_count} stores found&nbsp;&nbsp;&mdash;&nbsp;&nbsp;page {$iri_query.STORESPAGENUM} of {$iri_pages}&nbsp;)</p>

<table cellspacing="0" cellpadding="7" border="0">

<tr>
<td class="HeadingCell">STORE</td>
<td class="HeadingCell">DISTANCE</td>
<td class="HeadingCell">MAP</td>
</tr>

{section name="store" loop=$iri_store}

<tr>
<td valign="top" class="FieldCell"><b>{$iri_store[store].NAME}</b>
<br>{$iri_store[store].ADDRESS}, {$iri_store[store].CITY}, {$iri_store[store].STATE} {$iri_store[store].ZIP} 
<br>{$iri_store[store].PHONE}</td>
<td valign="top" class="FieldCell"><div align="center">{$iri_store[store].DISTANCE} mi.</div></td>
<td valign="top" class="FieldCell"><div align="center"><A HREF="http://www.infousa.com/cgi-bin/map/mqcustomconnect.cgi?link=map&icontitles=yes&level=9&POI1iconid=31&POI1name={$iri_store[store].NAME|escape:"url"}&POI1streetaddress={$iri_store[store].ADDRESS|escape:"url"}&POI1city={$iri_store[store].CITY}&POI1state={$iri_store[store].STATE}&POI1zip={$iri_store[store].ZIP}&POI1phone={$iri_store[store].PHONE}" target="InfoUSA" class="AnchorAction">InfoUSA</A>&nbsp;&nbsp;</div></td>
</tr>

{assign var="last_store" value=$iri_store[store].NUMBER}

{/section}

</table>

{if $iri_store[0].NUMBER != 1}

   {assign var="previous" value="yes"}
   {math equation="x - y" x=$iri_query.STORESPAGENUM y=1 assign="prevpage"}
   <p><a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$iri_query.SEARCHRADIUS}&storespagenum={$prevpage}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Previous page</a>

{/if}

{if $last_store < $iri_count}

   {if $previous == "yes"}&nbsp;|&nbsp;{else}<p>{/if}
   {math equation="x + y" x=$iri_query.STORESPAGENUM y=1 assign="nextpage"}
   <a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$iri_query.SEARCHRADIUS}&storespagenum={$nextpage}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Next page</a></p>

{else}
</p>
{/if}

</center>
</div>

{elseif ($iri_count == 0) && ($iri_error == "ok")}

<div align="center">
<center>

<p>Results for <b>{$iri_query.PRODNAME}</b> within {$iri_query.SEARCHRADIUS} miles of <b>{$iri_query.ZIP}</b></p>

{math equation="x + y" x=$iri_query.SEARCHRADIUS y=10 assign="plusten"}
{math equation="x + y" x=$iri_query.SEARCHRADIUS y=20 assign="plustwenty"}

<p>No stores were found within {$iri_query.SEARCHRADIUS} miles radius.</p>

<p><a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$plusten}&storespagenum={$iri_query.STORESPAGENUM}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Expand search radius to {$plusten} miles?</a>
<br><a href="{$php_self}?zip={$iri_query.ZIP}&productid={$iri_query.PRODUCTID}&productfamilyid={$iri_query.PRODUCTFAMILYID}&clientid={$iri_query.CLIENTID}&searchradius={$plustwenty}&storespagenum={$iri_query.STORESPAGENUM}&storesperpage={$iri_query.STORESPERPAGE}&stores={$iri_query.STORES}&etailers={$iri_query.ETAILERS}&etailerspagenum={$iri_query.ETAILERSPAGENUM}&etailersperpage={$iri_query.ETAILERSPERPAGE}&template={$iri_query.TEMPLATE}&producttype={$iri_query.PRODUCTTYPE}">Expand search radius to {$plustwenty} miles?</a></p>

</center>
</div>

{else}

<div align="center">
<center>

<p>Results for <b>{$iri_query.PRODNAME}</b> near <b>{$iri_query.ZIP}</b></p>

<p>{$iri_error}</p>

</center>
</div>

{/if}