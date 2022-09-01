
<p>Item: <select name="productid">
<option value="">-- Choose a Product --</option>
{section name="prods" loop=$products}
   <option value="{$products[prods].LocatorCode}">{$products[prods].ProductName}</option>
{/section}
</select></p>

<p>Zip: <input type="text" name="zip" size="15" maxlength="5"></p>

<p>Distance: <select name="searchradius">
   <option value="0">-- Choose a Distance --
   <option value="5">Within 5 miles</option>
   <option value="10" selected>Within 10 miles</option>
   <option value="15">Within 15 miles</option>
   <option value="20">Within 20 miles</option>
   <option value="25">Within 25 miles</option>
   <option value="50">Within 50 miles</option>
   <option value="100">Within 100 miles</option>
</select></p>

<input type="hidden" name="productfamilyid" value="HNCL">
<input type="hidden" name="clientid" value="69">
<input type="hidden" name="template" value="default.xsl">
<input type="hidden" name="stores" value="1">
<input type="hidden" name="storespagenum" value="1">
<input type="hidden" name="storesperpage" value="10">
<input type="hidden" name="producttype" value="agg">
<input type="hidden" name="brand" value="{$brand}">

<p><input type="submit" value="Find Stores"></p>
