
{literal}
<script language="javascript">

var toggle_regions = false;
function doallregions()
{
  toggle_regions = !toggle_regions;
  for(i = 0; i < document._form["region[]"].length; i++)
    document._form["region[]"][i].checked = toggle_regions;
}

function docancel()
{
  document.location = "aoi_admin.php";
}

function dochannel(elem)
{
  for(e = 0; e < elem.length; e++) if(elem[e].selected)
  {
    prefix = elem[e].value.substring(0, 2);
    for(i = 0; i < document._form["region[]"].length; i++)
      if(document._form["region[]"][i].value.substring(0, 2) == prefix)
        document._form["region[]"][i].checked = true;

    prefix = elem[e].value.substring(0, 1);
    for(i = 0; i < document._form.division_id.length; i++)
      if(document._form.division_id[i].text.substring(0, 1) == prefix)
        document._form.division_id[i].selected = true;
  }
}

function docopy()
{
  if(document._form.copy_to.value =
    prompt("Enter the username to copy to:", "")) dosave();
  else alert("Cancelled copy.");
}

function docreate()
{
  getemail();

  document._form.what.value = "create";
  document._form.submit();
}

function dodivision(elem)
{
  for(e = 0; e < elem.length; e++) if(elem[e].selected)
  {
    prefix = elem[e].value.substring(0, 3);
    for(i = 0; i < document._form["region[]"].length; i++)
      if(document._form["region[]"][i].value.substring(0, 3) == prefix)
        document._form["region[]"][i].checked = true;

    prefix = elem[e].value.substring(0, 1);
    for(i = 0; i < document._form.channel_id.length; i++)
      if(document._form.channel_id[i].text.substring(0, 1) == prefix)
        document._form.channel_id[i].selected = true;
  }
}

function getemail()
{
  if (document._form.user_email.value == "0")
  {
     default_email = "";
  }
  else
  {
     default_email = "{/literal}{$user_email}{literal}";
  }
  if (document._form.mail_to.value =
     prompt("Enter the address to mail to:", default_email))
  {
    document._form.email_user.value = true;
  }
    else
  {
     alert("Cancelled e-mail.");
  }
}

function doemail()
{
  getemail();
  if ( ! document._form.mail_to.value == "")
  {
    document._form.what.value = "email";
    document._form.submit();
  }
}

function doload()
{
  document._form.what.value = "load";
  document._form.submit();
}

function doremove()
{
  if(confirm("Are you sure you want to remove this user?"))
  {
    document._form.what.value = "remove";
    document._form.submit();
  }
}

function dosave()
{
{/literal}

{if $what == "create"}
  {literal}
  if(!document._form.user_id.value.length)
  {
    alert("Please enter the user ID to create.");
    return;
  }
  {/literal}
{/if}

{literal}
  ok = false;
  for(i = 0; i < document._form["usertype[]"].length; i++)
    if(document._form["usertype[]"][i].checked) ok = true;
  if(!ok)
  {
    alert("Please select at least one user type.");
    return;
  }

  ok = false;
  for(i = 0; i < document._form["region[]"].length; i++)
    if(document._form["region[]"][i].checked) ok = true;
  if(!ok)
  {
    alert("Please select at least one source region.");
    return;
  }
  
  getemail();

  document._form.what.value = "save";
  document._form.submit();
}

</script>
{/literal}

<span class="pageHd">Business Objects Area of Interest (AOI) Administration</span>

<p class="notice">
{if $user_id }
   {if $what == "save"}
This data has been saved.
   {else}
Please set the appropriate fields and click 'Save' to apply your changes
to the database.
<br />To e-mail this user's information to yourself or others,
click the 'E-mail' button.
<br />To copy this user's attributes and create a new user, click 'Copy'.
<br />To remove this user from the database, use the 'Remove' button.
<br />You may cancel any changes and select a new user with the 'Cancel' button.
   {/if}
{else}
Please select the user you'd like to edit, or click 'Create new' to create
a new user in the database.
{/if}
</p>

<form name="_form" action="aoi_admin.php" method="post">
<input type="hidden" name="what" value="list" />
<input type="hidden" name="copy_to" value="" />
<input type="hidden" name="mail_to" value="" />
<input type="hidden" name="email_user" value="" />
<input type="hidden" name="user_email" value="{$user_email}" />

<table class="datatable">

<tr class="datarow1">
  <td>
    User:<br />
{if $what == "create" }
   <input type="textfield" name="user_id" size="20" />
   <input type="button" value="Cancel" onclick="docancel()" />
{elseif ($user_id) }
   <b>{$user_id}</b>&nbsp;
   <input type="hidden" name="user_id" value="{$user_id}" />
   <input type="button" value="Cancel" onclick="docancel()" />
{else}
   <select name="user_id" size="1" onchange="doload()">
   <option value="0">(select one)</option>
   {section name="user" loop=$user_list }
      <option value="{$user_list[user].USER_ID}"{if $user_list[user].USER_ID == $user_id} selected{/if}>{$user_list[user].USER_ID}</option>
   {/section}
   </select>
   <input type="button" value="Create new" onclick="docreate()" />
{/if}
  </td>

{if ($user_id || ($what == "create")) }

  <td>
    User type:<br />
    {section name="utype" loop=$user_types}
    <input type="checkbox" name="usertype[]" value="{$user_types[utype].code}"{if $user_types[utype].selected == 1} checked{/if} />{$user_types[utype].desc}<br />
    {/section}
  </td>
</tr>

<tr class="datarow2">
  <td>
    Channel:<br />
    <select name="channel_id" size="10" multiple onchange="dochannel(this)">
    {section name="chan" loop="$channels"}
       <option value="{$channels[chan].DWCHANNELCODE}"{if $channels[chan].selected == 1} selected{/if}>{$channels[chan].DWCHANNELCODE} : {$channels[chan].DWCHANNELDESC}</option>
    {/section}
    </select>
  </td>
  <td>
    Division:<br />
    <select name="division_id" size="10" multiple onchange="dodivision(this)">
    {section name="div" loop="$divisions"}
       <option value="{$divisions[div].DWDIVISIONCODE}"{if $divisions[div].selected == 1} selected{/if}>{$divisions[div].DWDIVISIONCODE} : {$divisions[div].DWDIVISIONDESC}</option>
    {/section}
    </select>
  </td>
</tr>

<tr class="datarow1">
  <td colspan="2">
    <p>Source regions (select all that apply):</p>
    <input type="button" value="All/none" onclick="doallregions()" /><br />

<table border="0" cellspacing="0" cellpadding="4">
<tr>
<td width="34%" valign="top">
{section name="reg" loop=$regions}
   {if (($smarty.section.reg.index % $col_length) == 0)  && !$smarty.section.reg.first}
      </td><td width="33%" valign="top">
   {/if}
   <span class="datarowSm"><input type="checkbox" name="region[]" value="{$regions[reg].DWSPERSONCODE}"{if $regions[reg].selected == 1} checked{/if} />{$regions[reg].DWSPERSONCODE} : {$regions[reg].DWSPERSONDESC}</span><br />
{/section}
</td>
</tr>
</table>

  </td>
</tr>

<tr class="datafooter">
  <td colspan="2" class="datafooter">
    <input type="button" value="Save" onclick="dosave()" />
   {if ($user_id)}
    <input type="button" value="E-mail" onclick="doemail()" />
    <input type="button" value="Copy" onclick="docopy()" />
    <input type="button" value="Remove" onclick="doremove()" />
   {/if}
    <input type="button" value="Cancel" onclick="docancel()" />
  </td>
</tr>

{else} 
</tr>
{/if}

</table>

</form>