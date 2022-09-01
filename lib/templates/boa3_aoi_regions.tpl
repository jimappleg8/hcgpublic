
{literal}
<script type="text/javascript">
<!--
var toggle_regions = false;
function doallregions()
{
  toggle_regions = !toggle_regions;
  for(i = 0; i < document._form["region[]"].length; i++)
    document._form["region[]"][i].checked = toggle_regions;
}

function dochannel(elem)
{
   for (e=0; e<elem.length; e++) {
      if (elem[e].selected) {
         prefix = elem[e].value.substring(0, 2);
         for (i=0; i<document._form["region[]"].length; i++) {
            if (document._form["region[]"][i].value.substring(0, 2) == prefix) {
               document._form["region[]"][i].checked = true;
               detectChange(i);
            }
         }
         prefix = elem[e].value.substring(0, 1);
         for (i=0; i<document._form.division_id.length; i++) {
            if (document._form.division_id[i].text.substring(0, 1) == prefix) {
               document._form.division_id[i].selected = true;
            }
         }
      }
   }
}

function dodivision(elem)
{
   for (e=0; e<elem.length; e++) {
      if (elem[e].selected) {
         prefix = elem[e].value.substring(0, 3);
         for (i=0; i<document._form["region[]"].length; i++) {
            if (document._form["region[]"][i].value.substring(0, 3) == prefix) {
               document._form["region[]"][i].checked = true;
               detectChange(i);
            }
         }
         prefix = elem[e].value.substring(0, 1);
         for (i=0; i<document._form.channel_id.length; i++) {
            if (document._form.channel_id[i].text.substring(0, 1) == prefix) {
               document._form.channel_id[i].selected = true;
            }
         }
      }
   }
}

// begin general functions

var changes = new Array(false);

function detectChange(listindex)
{
   if (changes[listindex] != true) {
      changes[listindex] = false;
   }
   changes[listindex] = !changes[listindex];
}

function changesMade()
{
   for (i=0; i<changes.length; i++) {
      if (changes[i] == true) {
         return true;
      }
   }
   return false;
}

function dotab(page)
{
   if (changesMade()) {
      answer = confirm("Discard changes? To save the changes you've made, click Cancel and press the save button at the bottom of the page.");
      if (answer) {
         document.location = page;
      }
   } else {
      document.location = page;
   }
}

function docancel()
{
   if (changesMade()) {
      answer = confirm("Discard changes? To save the changes you've made, click Cancel and press the save button at the bottom of the page.");
      if (answer) {
         document.location = "aoi_admin.php";
      }
   } else {
      document.location = "aoi_admin.php";
   }
}

function docopy()
{
   var answer = prompt("Enter the username to copy to:", "");
   if ((answer != " ") || (answer != null)) {
      document._form.copy_to.value = answer;
      document._form.what.value = "save";
      document._form.action = "aoi_admin.php";
      document._form.submit();
   } else {
      alert("Cancelled copy.");
   }
}

function doemail()
{
   if (document._form.user_email.value == "0") {
      default_email = "";
   } else {
      default_email = "{/literal}{$user_email}{literal}";
   }
   var answer = prompt("Enter the address to mail to:", default_email);
   if (answer) {
      document._form.mail_to.value = answer;
      document._form.what.value = "email";
      document._form.action = "aoi_admin.php";
      document._form.submit();
   } else {
      alert("Cancelled e-mail.");
   }
}

function doremove()
{
   var answer = confirm("Are you sure you want to remove this user?");
   if (answer) {
      document._form.what.value = "remove";
      document._form.action = "aoi_admin.php";
      document._form.submit();
   }
}

// end general functions

function dosave()
{
   ok = false;
   for (i=0; i<document._form["region[]"].length; i++) {
      if (document._form["region[]"][i].checked) {
         ok = true;
      }
   }
   if (document._form["usertype[]"].checked) {
      ok = true;
   }
   if (!ok) {
      alert("Please select at least one source region.");
      return;
   }
/*   answer = confirm("Would You like to send an email to this user showing these changes?");
   if (answer) {
      if (document._form.user_email.value == "0") {
         alert("Unable to find email address in the LDAP directory. This record will be saved, but no email will be sent. Please click the \"Email\" button below to enter the email address manually.");  
      } else {
         document._form.email_user.value = true;
      }
   } */
   document._form.what.value = "save";
   document._form.submit();
}
-->
</script>
{/literal}

<h1 class="pageHd">Business Objects Area of Interest (AOI) Administration</h1>

<div class="notice">
   {if $what == "save"}
This data has been saved.
   {else}
<b>Please set the appropriate fields and click 'Save' to apply your changes
to the database.</b>
   {/if}
</div>

<form name="_form" action="aoi_regions.php" method="post">

<div class="userHead">
   <div class="userName">
      <h2>Settings for {$user_id}</h2>
   </div>
   <div class="userButtons">
      <input type="button" value="E-mail" onclick="doemail()" title="E-mail this user's information to yourself or others" />
      <input type="button" value="Copy" onclick="docopy()" title="Copy this user's attributes and create a new user" />
      <input type="button" value="Remove" onclick="doremove()" title="Remove this user from the database" />
      <input type="button" value="Cancel" onclick="docancel()" title="Cancel any changes and select a new user" />
   </div>
</div>

{include file="boa_tabs.tpl"}

<div id="dataarea">

<input type="hidden" name="what" value="list" />
<input type="hidden" name="copy_to" value="" />
<input type="hidden" name="mail_to" value="" />
<input type="hidden" name="email_user" value="" />
<input type="hidden" name="user_email" value="{$user_email}" />
<input type="hidden" name="user_id" value="{$user_id}" />

<div class="datarow1">

   <input type="checkbox" id="usertype" name="usertype[]" value="SuperUser"{if $isSuperUser == 1} checked="checked"{/if} onchange="detectChange(1000);" title="give this user access to all regions" /><label for="usertype" title="give this user access to all regions">Super User</label><br />

</div> {* datarow1 *}

<table class="datatable">
<tr class="datarow2">
  <td>
    Channel:<br />
    <select name="channel_id" size="10" multiple="multiple" onchange="dochannel(this)">
    {section name="chan" loop="$channels"}
       <option value="{$channels[chan].DWCHANNELCODE}"{if $channels[chan].selected == 1} selected="selected"{/if}>{$channels[chan].DWCHANNELCODE} : {$channels[chan].DWCHANNELDESC|trim}</option>
    {/section}
    </select>
  </td>
  <td>
    Division:<br />
    <select name="division_id" size="10" multiple="multiple" onchange="dodivision(this)">
    {section name="div" loop="$divisions"}
       <option value="{$divisions[div].DWDIVISIONCODE}"{if $divisions[div].selected == 1} selected="selected"{/if}>{$divisions[div].DWDIVISIONCODE} : {$divisions[div].DWDIVISIONDESC|trim}</option>
    {/section}
    </select>
  </td>
</tr>
</table>

<div class="datarow1">

<p>Source regions (select all that apply):</p>
<input type="button" value="All/none" onclick="doallregions()" /><br />

<table width="100%" border="0" cellspacing="0" cellpadding="4">
<tr>
<td width="34%" valign="top">
{section name="reg" loop=$regions}
   {if (($smarty.section.reg.index % $col_length) == 0)  && !$smarty.section.reg.first}
      </td><td width="33%" valign="top">
   {/if}
   <span class="datarowSm"><input type="checkbox" id="region{$smarty.section.reg.index}" name="region[]" value="{$regions[reg].DWSPERSONCODE}"{if $regions[reg].selected == 1} checked="checked"{/if} onchange="detectChange({$smarty.section.reg.index});" /><label for="region{$smarty.section.reg.index}">{$regions[reg].DWSPERSONCODE} : {$regions[reg].DWSPERSONDESC}</label></span><br />
{/section}
</td>
</tr>
</table>

</div> {* datarow1 *}

<div class="datafooter">

    <input type="button" value="Save" onclick="dosave()" />

</div> {* datafooter *}

</form>

</div> {* dataarea *}