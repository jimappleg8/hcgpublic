{ section name="company" loop=$stock}
   
   <hr noshade size="1" width="100%">
   <table width="100%" border="0">

   <tr>
      <td colspan="5"><b>{$stock[company].symbol}: {$stock[company].name}</b></td>
   </tr>

   <tr>
      {if $stock[company].symbol == "HAIN"}
      <td><b><a href="http://www.nasdaq.com/asp/quotes_multi.asp?mode=stock&symbol=hain">HAIN - NASDAQ</a></b></td>
     {else}
     <td>&nbsp;</td>
     {/if}
     <td colspan="4"><div align="right">{$stock[company].date}, 
{if $stock[company].time != ""}
   {$stock[company].time|date_format:"%I:%M%p"} ET
{/if}
{if $stock[company].status != ""}
   - {$stock[company].status}
{/if}</div></td>
   </tr>
   
   <tr>
      <td>Last Sale:</td>
      <td><div align="right"><b>$&nbsp;{$stock[company].price_last}</b></div></td>
      <td>&nbsp;&nbsp;</td>
      <td>Net Change:</td>
      <td><div align="right">$&nbsp;{$stock[company].dchangeu} 
      {if $stock[company].direction == "up"}
         <img src="/images/greenArrowSmall.gif" alt="" height="11" width="11" alt="^"> {$stock[company].pchange}%
      {else if $stock[company].direction == "down"}
         <img src="/images/redArrowSmall.gif" alt="" height="11" width="11" alt="v"> {$stock[company].pchange}%
      {/if}
      </div></td>
   </tr>

   <tr>
      <td>Today's High:</td>
      <td><div align="right">$&nbsp;{$stock[company].price_max|string_format:"%.2f"}</div></td>
      <td>&nbsp;&nbsp;</td>
      <td>Today's Low:</td>
      <td><div align="right">$&nbsp;{$stock[company].price_min|string_format:"%.2f"}</div></td>
   </tr>

   <tr>
      <td>Share Volume:</td>
      <td><div align="right">{$stock[company].volume}</div></td>
      <td>&nbsp;&nbsp;</td>
      <td>Previous Close:</td>
      <td><div align="right">$&nbsp;{$stock[company].previous|string_format:"%.2f"}</div></td>
   </tr>

   <tr>
      <td>Best Bid:</td>
      {if $stock[company].best_bid != ""}
      <td><div align="right">$&nbsp;{$stock[company].best_bid|string_format:"%.2f"}</div></td>
      {else}
      <td><div align="right">unavailable</div></td>
      {/if}
      <td>&nbsp;&nbsp;</td>
      <td>Best Ask:</td>
      {if $stock[company].best_ask != ""}
      <td><div align="right">$&nbsp;{$stock[company].best_ask|string_format:"%.2f"}</div></td>
      {else}
      <td><div align="right">unavailable</div></td>
      {/if}
   </tr>

   </table>

{/section}