
<p>Item: <select name="productid" style="width:400px;">
<option value="">-- Choose a product --</option>
{section name="cat" loop=$cats}
   {if ( ! empty($cats[cat].Products))}
   <optgroup label="{$cats[cat].CategoryName}">
      {section name="prod" loop=$cats[cat].Products}
   <option value="{$cats[cat].Products[prod].LocatorCode}">{$cats[cat].Products[prod].ProductName}</option>
      {/section}
   </opgroup>
   {/if}
{/section}
</select></p>

<p>Zip: <input type="text" name="zip" size="15" maxlength="5"></p>

<p>Distance: <select name="searchradius">
   <option value="0">-- Choose a Distance --
   <option value="5">0-5 miles
   <option value="10" selected>6-10 miles
   <option value="15">10-15 miles
   <option value="20">15+ miles
</select></p>

<input type="hidden" name="productfamilyid" value="HNCL">
<input type="hidden" name="clientid" value="69">
<input type="hidden" name="template" value="default.xsl">
<input type="hidden" name="stores" value="1">
<input type="hidden" name="storespagenum" value="1">
<input type="hidden" name="storesperpage" value="10">
<input type="hidden" name="etailers" value="0">
<input type="hidden" name="etailerspagenum" value="1">
<input type="hidden" name="etailersperpage" value="15">
<input type="hidden" name="producttype" value="agg">
<input type="hidden" name="brand" value="{$brand}">
<input type="hidden" name="sort" value="DISTANCE" />

<p><input type="submit" value="Find Stores"></p>
