To: {$mail.mail_to}
From: Business Object Admin <webmaster@hain-celestial.com>
Reply-To: Business Object Admin <webmaster@hain-celestial.com>
{if $mail.email_user == true}
Subject: Your Current AOI Information ({$mail.user_id|trim})
{else}
Subject: AOI information for {$mail.user_id|trim}
{/if}
MIME-Version: 1.0
Content-Type: multipart/alternative; boundary="----=_NextPart_112231155711309"

------=_NextPart_112231155711309
Content-type: text/plain; charset="UTF-8"
Content-transfer-encoding: quoted-printable

AOI information for user {$mail.user_id}

{if $mail.email_user == true}
This email is to let you know what regions, brands, and sources systems you have access to in Business Objects. You may have recently been added as a Business Objects user, or your account may have been updated.

Please verify that you have access to the correct regions, brands, and source systems. If you need additions or deletions, please contact the help desk. Do not reply to this email.

REGIONS
   {if $mail.superuser.SalesPerson == 1}
You are a SuperUser and have access to all regions. See below for a list of all regions.
   {else}
You are assigned to all the regions with an "X" to the left of them. You are _not_ assigned to all regions without an "X".
   {/if}
{else}
   {if $mail.superuser.SalesPerson == 1}
This user is a SuperUser and has access to all regions.
   {else}
This user is assigned to all the regions with an "X" to the left of them. He or she is _not_ assigned to all regions without an "X".
   {/if}
{/if}

++++++++++++++++++++++++++++++++++++++++++++++++++++++
User: {$mail.user_id}
User type: {$mail.user_type}
++++++++++++++++++++++++++++++++++++++++++++++++++++++
------------------------------------------------------
00000  Unknown
------------------------------------------------------
{section name="reg" loop=$mail.regions }
{if ($mail.regions[reg].DWSPERSONCODE > $mail.divisions[div].DWDIVISIONCODE) && ($mail.regions[reg].DWSPERSONCODE < $mail.divisions[div].DWDIVISIONCODE + 100) }
{if $mail.regions[reg].selected == 1}X   {else}    {/if}{$mail.regions[reg].DWSPERSONCODE}   {$mail.regions[reg].DWSPERSONDESC}
{/if}
{/section}
{section name="chan" loop=$mail.channels }
------------------------------------------------------
{$mail.channels[chan].DWCHANNELCODE}  {$mail.channels[chan].DWCHANNELDESC}
------------------------------------------------------
{section name="div" loop=$mail.divisions }
{if ($mail.divisions[div].DWDIVISIONCODE > $mail.channels[chan].DWCHANNELCODE) && ($mail.divisions[div].DWDIVISIONCODE < $mail.channels[chan].DWCHANNELCODE + 10000) }
{$mail.divisions[div].DWDIVISIONCODE}  {$mail.divisions[div].DWDIVISIONDESC}
{section name="reg" loop=$mail.regions }
{if ($mail.regions[reg].DWSPERSONCODE > $mail.divisions[div].DWDIVISIONCODE) && ($mail.regions[reg].DWSPERSONCODE < $mail.divisions[div].DWDIVISIONCODE + 100) }
{if $mail.regions[reg].selected == 1}X   {else}    {/if}{$mail.regions[reg].DWSPERSONCODE}   {$mail.regions[reg].DWSPERSONDESC}
{/if}
{/section}
{/if}{/section}
{/section}

{if $mail.email_user == true}
BRANDS
   {if $mail.superuser.Brand == 1}
You are a SuperUser and have access to all brands. See below for a list of all brands.
   {else}
You are assigned to all the brands with an "X" to the left of them. You are _not_ assigned to all brands without an "X".
   {/if}
{else}
   {if $mail.superuser.Brand == 1}
This user is a SuperUser and has access to all brands.
   {else}
This user is assigned to all the brands with an "X" to the left of them. He or she is _not_ assigned to all brands without an "X".
   {/if}
{/if}

------------------------------------------------------
{section name="reg" loop=$mail.brands }
{if $mail.brands[reg].selected == 1}X   {else}    {/if}{$mail.brands[reg].BRANDCODE}   {$mail.brands[reg].BRANDDESC}
{/section}
------------------------------------------------------

{if $mail.email_user == true}
SOURCE SYSTEMS
   {if $mail.superuser.SourceSystem == 1}
You are a SuperUser and have access to all source systems. See below for a list of all source systems.
   {else}
You are assigned to all the source systems with an "X" to the left of them. You are _not_ assigned to all source systems without an "X".
   {/if}
{else}
   {if $mail.superuser.SourceSystem == 1}
This user is a SuperUser and has access to all source systems.
   {else}
This user is assigned to all the source systems with an "X" to the left of them. He or she is _not_ assigned to all source systems without an "X".
   {/if}
{/if}

------------------------------------------------------
{section name="reg" loop=$mail.sourcesystems }
{if $mail.sourcesystems[reg].selected == 1}X   {else}    {/if}{$mail.sourcesystems[reg].SOURCESYSTEMKEY}   {$mail.sourcesystems[reg].DESCRIPTION}
{/section}
------------------------------------------------------

++++++++++++++++++++++++++++++++++++++++++++++++++++++


------=_NextPart_112231155711309
Content-type: text/html; charset="UTF-8"
Content-transfer-encoding: quoted-printable

<!DOCTYPE HTML PUBLIC "-//W3C//DTD W3 HTML//EN">
<html>

<head>
   {literal}
   <style type=3D"text/css">
   <!--
   .datatable { border: 1px solid #999999; }
   #frame { background: #ffffff; width: 98%; border: 0px solid #000000; }
   .notice { border: 1px solid #9999cc; background-color: #eeeeff; padding: 4px; }
   .dataheader { background-color: #c3ced2; font-weight: bold; }
   .datarow1, td.datatable { background-color: #f6f6f6; }
   -->
   </style>
   {/literal}
</head>
<body bgcolor=3D"#FFFFFF">

<table id=3D"frame" border=3D"0" cellspacing=3D"0" cellpadding=3D"0">
<tr>
<td>


<p><b>AOI information for user {$mail.user_id}</b></p>

{if $mail.email_user == true}
<p>This email is to let you know what regions, brands, and source systems you have access to in Business Objects. You may have recently been added as a Business Objects user, or your account may have been updated.</p>

<p>Please verify that you have access to the correct regions, brands, and source systems. If you need additions or deletions, please contact the help desk. Do not reply to this email.</p>

<p><b>REGIONS</b></p>
   {if $mail.superuser.SalesPerson == 1}
<p><b>You are a SuperUser and have access to all regions.</b> See below for a list of all regions.</p>
   {else}
<p>You are assigned to all the regions listed in red below. You are <i>not</i> assigned to all regions listed in grey.</p>
   {/if}
{else}
   {if $mail.superuser.SalesPerson == 1}
<p>This user is a SuperUser and has access to all regions.</p>
   {else}
<p>This user is assigned to all the regions listed in red below. He or she is <i>not</i> assigned to all regions listed in grey.</p>
   {/if}
{/if}

<table width=3D"100%" class=3D"datatable" cellpadding=3D"2" cellspacing=3D"1" border=3D"0">

<tr class=3D"datarow1">
  <td colspan=3D"3">User: <b>{$mail.user_id}</b></td>
</tr>

<tr class=3D"datarow1">
  <td colspan=3D"3">User type: {$mail.user_type}</b></td>
</tr>

<tr class=3D"dataheader">
   <td>00000</td>
   <td colspan=3D"2">Unknown</td>
</tr>

{section name="reg" loop=$mail.regions }
         
   {if ($mail.regions[reg].DWSPERSONCODE == -1) }

      <tr class=3D"datarow1">
      <td>&nbsp;</td>
      <td>{if $mail.regions[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
      {$mail.regions[reg].DWSPERSONCODE}</font></td>
      <td>{if $mail.regions[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
      {$mail.regions[reg].DWSPERSONDESC}</font></td></tr>
                
   {/if}

{/section}

{section name="chan" loop=$mail.channels }

   <tr class=3D"dataheader">
   <td>{$mail.channels[chan].DWCHANNELCODE}</td>
   <td colspan=3D"2">{$mail.channels[chan].DWCHANNELDESC}</td>
   </tr>

   {section name="div" loop=$mail.divisions }

      {if ($mail.divisions[div].DWDIVISIONCODE > $mail.channels[chan].DWCHANNELCODE) &&
          ($mail.divisions[div].DWDIVISIONCODE < $mail.channels[chan].DWCHANNELCODE + 10000) }

         <tr class=3D"datarow1">
         <td>{if $mail.divisions[div].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
         {$mail.divisions[div].DWDIVISIONCODE}</font></td>
         <td colspan=3D"2">{if $mail.divisions[div].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
         {$mail.divisions[div].DWDIVISIONDESC}</font></td>
         </tr>

         {section name="reg" loop=$mail.regions }
         
            {if ($mail.regions[reg].DWSPERSONCODE > $mail.divisions[div].DWDIVISIONCODE) &&
                ($mail.regions[reg].DWSPERSONCODE < $mail.divisions[div].DWDIVISIONCODE + 100) }

               <tr class=3D"datarow1">
               <td>&nbsp;</td>
               <td>{if $mail.regions[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
               {$mail.regions[reg].DWSPERSONCODE}</font></td>
               <td>{if $mail.regions[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
               {$mail.regions[reg].DWSPERSONDESC}</font></td></tr>
                
            {/if}

         {/section}

      {/if}

   {/section}

{/section}

</table>

</td>
</tr>
</table>

{if $mail.email_user == true}
<p><b>BRANDS</b></p>
   {if $mail.superuser.Brand == 1}
<p><b>You are a SuperUser and have access to all brands.</b> See below for a list of all brands.</p>
   {else}
<p>You are assigned to all the brands listed in red below. You are <i>not</i> assigned to all brands listed in grey.</p>
   {/if}
{else}
   {if $mail.superuser.Brand == 1}
<p>This user is a SuperUser and has access to all brands.</p>
   {else}
<p>This user is assigned to all the brands listed in red below. He or she is <i>not</i> assigned to all brands listed in grey.</p>
   {/if}
{/if}

<table width=3D"100%" class=3D"datatable" cellpadding=3D"2" cellspacing=3D"1" border=3D"0">

{section name="reg" loop=$mail.brands }
         
   <tr class=3D"datarow1">
   <td>&nbsp;</td>
   <td>{if $mail.brands[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
   {$mail.brands[reg].BRANDCODE}</font></td>
   <td>{if $mail.brands[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
   {$mail.brands[reg].BRANDDESC}</font></td></tr>
                
{/section}

</table>

{if $mail.email_user == true}
<p><b>SOURCE SYSTEMS</b></p>
   {if $mail.superuser.SourceSystem == 1}
<p><b>You are a SuperUser and have access to all source systems.</b> See below for a list of all source systems.</p>
   {else}
<p>You are assigned to all the source systems listed in red below. You are <i>not</i> assigned to all source systems listed in grey.</p>
   {/if}
{else}
   {if $mail.superuser.SourceSystem == 1}
<p>This user is a SuperUser and has access to all source systems.</p>
   {else}
<p>This user is assigned to all the source systems listed in red below. He or she is <i>not</i> assigned to all source systems listed in grey.</p>
   {/if}
{/if}

<table width=3D"100%" class=3D"datatable" cellpadding=3D"2" cellspacing=3D"1" border=3D"0">

{section name="reg" loop=$mail.sourcesystems }
         
   <tr class=3D"datarow1">
   <td>&nbsp;</td>
   <td>{if $mail.sourcesystems[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
   {$mail.sourcesystems[reg].SOURCESYSTEMKEY}</font></td>
   <td>{if $mail.sourcesystems[reg].selected == 0}<font color=3D"#999999">{else}<font color=3D"#FF0000">{/if}
   {$mail.sourcesystems[reg].DESCRIPTION}</font></td></tr>
                
{/section}

</table>

</body>
</html>

------=_NextPart_112231155711309--