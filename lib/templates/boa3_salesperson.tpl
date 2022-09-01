<span class="pageHd">Business Objects Salesperson Lookup</span>

<p class="notice">You are assigned to all of the regions listed in red below.
<br />You are <i>not</i> assigned to all regions listed in grey.</p>

<table class="datatable">

<tr class="datarow1">
  <td colspan="3">
    User: <b>{$user_id}</b>
  </td>
</tr>

<tr class="dataheader">
   <td>00000</td>
   <td colspan="2">Unknown</td>
</tr>

{section name="reg" loop=$regions }
         
   {if ($regions[reg].DWSPERSONCODE == -1) }

      <tr class="datarow1">
      <td>&nbsp;</td>
      <td>{if $regions[reg].selected == 0}<font color="#999999">{else}<font color="#FF0000">{/if}
      {$regions[reg].DWSPERSONCODE}</font></td>
      <td>{if $regions[reg].selected == 0}<font color="#999999">{else}<font color="#FF0000">{/if}
      {$regions[reg].DWSPERSONDESC}</font></td></tr>
                
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
         <td>{if $divisions[div].selected == 0}<font color="#999999">{else}<font color="#FF0000">{/if}
         {$divisions[div].DWDIVISIONCODE}</font></td>
         <td colspan="2">{if $divisions[div].selected == 0}<font color="#999999">{else}<font color="#FF0000">{/if}
         {$divisions[div].DWDIVISIONDESC}</font></td>
         </tr>

         {section name="reg" loop=$regions }
         
            {if ($regions[reg].DWSPERSONCODE > $divisions[div].DWDIVISIONCODE) &&
                ($regions[reg].DWSPERSONCODE < $divisions[div].DWDIVISIONCODE + 100) }

               <tr class="datarow1">
               <td>&nbsp;</td>
               <td>{if $regions[reg].selected == 0}<font color="#999999">{else}<font color="#FF0000">{/if}
               {$regions[reg].DWSPERSONCODE}</font></td>
               <td>{if $regions[reg].selected == 0}<font color="#999999">{else}<font color="#FF0000">{/if}
               {$regions[reg].DWSPERSONDESC}</font></td></tr>
                
            {/if}

         {/section}

      {/if}

   {/section}

{/section}

</table>
