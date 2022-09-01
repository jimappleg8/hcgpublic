
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
  document.location = "user_edit.php?";
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

function docreate()
{
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

function doload()
{
  document._form.what.value = "load";
  document._form.submit();
}

function dosave()
{
  ok = false;
  for(i = 0; i < document._form["region[]"].length; i++)
    if(document._form["region[]"][i].checked) ok = true;
  if(!ok)
  {
    alert("Please select at least one source region.");
    return;
  }

  document._form.what.value = "save";
  document._form.submit();
}

</script>
{/literal}

<span class="pageHd">Business Objects Change User Request</span>

{if $what == "save"}

<p class="notice">Thank you!
<br />
<br />Your request has been sent to the Help Desk.
Please watch your e-mail for your ticket number and
confirmation for this request.
<br />
<br />Requests take 1-3 days to complete; you will be notified via
e-mail when the new user account is activated.
<br />
<br />If this request is urgent, please contact the Help Desk
at 303-581-1310 after submitting the request, and discuss
the situation with an Information Technology representative.</p>

{elseif ($user_id)}

<p class="notice">Please fill out this form with the appropriate user options.
Click 'Submit' to send the information to the Help Desk.</p>

{else}

<p class="notice">Please select the user whose permissions you'd like to change.</p>

{/if}


<form name="_form" action="user_edit.php" method="post">
<input type="hidden" name="what" value="list" />
<input type="hidden" name="mail_to" value="" />

<table class="datatable">

{if $what == "save"}

<tr class="datarow1">
  <td>
    <input type="button" value="Another"
      onclick="document.location='user_edit.php'" />
  </td>
</tr>

{elseif ($user_id)}

<tr class="datarow1">
  <td colspan="3">
    User login name:<br />
    <b>{$user_id}</b>&nbsp;
    <input type="hidden" name="user_id" value="{$user_id}" />
    <input type="button" value="Cancel" onclick="docancel()" />
  </td>
</tr>

<tr class="datarow1">
  <td colspan="3">
    <p>Additional options (select all that apply):</p>
    <input type="checkbox" name="option_create_reports" value="1" />
      can create custom reports
    <br />
    <input type="checkbox" name="option_view_reports" value="1" />
      can view reports
  </td>
</tr>

<tr class="datarow1">
  <td colspan="3">
    <p>Source regions (select all that apply)
    <input type="button" value="All/none" onclick="doallregions()" />
    </p>
    
<tr class="dataheader">
   <td>00000</td>
   <td colspan="2">Unknown</td>
</tr>

{section name="reg" loop=$regions }
         
   {if ($regions[reg].DWSPERSONCODE == -1) }

      <tr class="datarow1">
      <td><input type="checkbox" name="region[]" value="{$regions[reg].DWSPERSONCODE}"{if $regions[reg].selected == 1} checked{/if} /></td>
      <td>{$regions[reg].DWSPERSONCODE}</td>
      <td>{$regions[reg].DWSPERSONDESC}</td></tr>
                
   {/if}

{/section}


{section name="chan" loop=$channels }

   <tr class="dataheader">
   <td>{$channels[chan].DWCHANNELCODE}</td>
   <td colspan="2">{$channels[chan].DWCHANNELDESC}</td>
   </tr>

   {section name="div" loop=$divisions }

      {if ($divisions[div].DWDIVISIONCODE > $channels[chan].DWCHANNELCODE) &&
          ($divisions[div].DWDIVISIONCODE < $channels[chan].DWCHANNELCODE + 10000) }

         <tr class="datarow1">
         <td>{$divisions[div].DWDIVISIONCODE}</td>
         <td colspan="2">{$divisions[div].DWDIVISIONDESC}</td>
         </tr>

         {section name="reg" loop=$regions }
         
            {if ($regions[reg].DWSPERSONCODE > $divisions[div].DWDIVISIONCODE) &&
                ($regions[reg].DWSPERSONCODE < $divisions[div].DWDIVISIONCODE + 100) }

               <tr class="datarow1">
               <td><input type="checkbox" name="region[]" value="{$regions[reg].DWSPERSONCODE}"{if $regions[reg].selected == 1} checked{/if} /></td>
               <td>{$regions[reg].DWSPERSONCODE}</td>
               <td>{$regions[reg].DWSPERSONDESC}</td></tr>
                
            {/if}

         {/section}

      {/if}

   {/section}

{/section}

  </td>
</tr>

<tr class="datafooter">
  <td colspan="3" class="datafooter">
    <input type="button" value="Submit" onclick="dosave()" />
    <input type="button" value="Cancel" onclick="docancel()" />
  </td>
</tr>

{else}

<tr class="datarow1">
   <td>
   User login name:<br />
   <select name="user_id" size="1" onchange="doload()">
      <option value="0">(select one)</option>
   {section name="user" loop=$user_list }
      <option value="{$user_list[user].USER_ID}"{if $user_list[user].USER_ID == $user_id} selected{/if}>{$user_list[user].USER_ID}</option>
    {/section}
   </select>';
   </td>
</tr>

{/if}

</table>

</form>