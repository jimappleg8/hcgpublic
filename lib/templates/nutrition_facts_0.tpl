<!-- page header 0 -->

<div align="center">

{if $nutfacts.display_hd == true }
<table width="246" cellpadding="1" cellspacing="0" border="0" bgcolor="#FFFFFF">
<tr>
<td><span class="productSbhd">{$nutfacts.ProductName}</span></td>
</tr>
</table>
{/if}

<!-- end page header -->

<!-- table header -->

<table width="246" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
<tr><td>

   <table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#FFFFFF" class="Nutrition">
   
   <tr> <!-- row that establishes grid -->
   <td width="5"><img src="/images/dot_clear.gif" width="3" height="1" alt=""></td>
   <td width="13"><img src="/images/dot_clear.gif" width="11" height="1" alt=""></td>
   <td width="59"><img src="/images/dot_clear.gif" width="57" height="1" alt=""></td>
   <td width="31"><img src="/images/dot_clear.gif" width="29" height="1" alt=""></td>
   <td width="27"><img src="/images/dot_clear.gif" width="25" height="1" alt=""></td>
   <td width="4"><img src="/images/dot_clear.gif" width="2" height="1" alt=""></td>
   <td width="54"><img src="/images/dot_clear.gif" width="52" height="1" alt=""></td>
   <td width="8"><img src="/images/dot_clear.gif" width="6" height="1" alt=""></td>
   <td width="38"><img src="/images/dot_clear.gif" width="36" height="1" alt=""></td>
   <td width="5"><img src="/images/dot_clear.gif" width="3" height="1" alt=""></td>   
   </tr>
   
   <tr> <!-- row 1: establishes the top and side margins -->
   <td rowspan="{$nutfacts.total_rows}" width="4">&nbsp;</td>
   <td colspan="8"><img src="/images/dot_clear.gif" width="232" height="2" alt=""></td>
   <td rowspan="{$nutfacts.total_rows}" width="4">&nbsp;</td>
   </tr>

<!-- end table header -->

<!-- section 1 -->

   <tr>
   <td colspan="8" class="NutritionHd"><b>Nutrition Facts</b></td>
   </tr>

   <tr>
   <td colspan="8" class="Nutrition">
   Serving Size: {$nutfacts.SSIZE|default:"???"}</td>
   </tr>

   {if ($nutfacts.MAKE != "") }
      <tr>
      <td colspan="8" class="Nutrition">Makes: {$nutfacts.MAKE}</td>
      </tr>
   {/if}

   <tr>
   <td colspan="8" class="Nutrition">
   Servings Per Container: {$nutfacts.SERV|default:"???"}</td>
   </tr>

<!-- end section 1 -->

<!-- section 2 -->

   {draw_line width="8" indent="no"}
   
   <!-- Amount Per Serving -->
   <tr>
   <td colspan="8" class="NutritionSm"><b>Amount Per Serving</b></td>
   </tr>

   {draw_line width="1" indent="no"}
   
   <!-- Calories and Fat from Calories -->
   <tr>
   <td colspan="3" class="Nutrition">
   <b>Calories</b> {$nutfacts.CAL|default:"???"}</td>
   {if ($nutfacts.FATCAL != "") }
      <td colspan="5" class="Nutrition"><div align="right">
      Calories from Fat {$nutfacts.FATCAL}</div></td>
   {else}
      <td colspan="5" class="Nutrition">&nbsp;</td>   
   {/if}
   </tr>

   {draw_line width="4" indent="no"}
   
   <tr>
   <td colspan="8" class="NutritionSm"><div align="right"><b>% Daily Value*</b></div></td>
   </tr>

<!-- end section 2 -->

   {if ($nutfacts.TFATQ != "") }
      <!-- Total Fat -->
      {draw_line width="1" indent="no"}
      <tr>
      <td colspan="7" class="Nutrition"><b>Total Fat</b> {$nutfacts.TFATQ}</td>
      {if ($nutfacts.TFATP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.TFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.SFATQ != "") }
      <!-- Saturated Fat - indented -->
      {draw_line width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Saturated Fat {$nutfacts.SFATQ}</td>
      {if ($nutfacts.SFATP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.SFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.HFATQ != "") }
      <!-- Trans (Hydrogenated) Fat - indented -->
      {draw_line width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition"><i>Trans</i> Fat {$nutfacts.HFATQ}</td>
      {if ($nutfacts.HFATP != "") }
         <td class="Nutrition"><div align="right">
         <b>{$nutfacts.HFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.PFATQ != "") }
      <!-- Polyunsaturated Fat - indented -->
      {draw_line width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Polyunsaturated Fat {$nutfacts.PFATQ}</td>
      {if ($nutfacts.PFATP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.PFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.MFATQ != "") }
      <!-- Monounsaturated Fat - indented -->
      {draw_line width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Monounsaturated Fat {$nutfacts.MFATQ}</td>
      {if ($nutfacts.MFATP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.MFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.CHOLQ != "") }
      <!-- Cholesterol -->
      {draw_line width="1" indent="no"}
      <tr>
      <td colspan="7" class="Nutrition"><b>Cholesterol</b> {$nutfacts.CHOLQ}</td>
      {if ($nutfacts.CHOLP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.CHOLP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.SODQ != "") }
      <!-- Sodium -->
      {draw_line width="1" indent="no"}
      <tr>
      <td colspan="7" class="Nutrition"><b>Sodium</b> {$nutfacts.SODQ}</td>
      {if ($nutfacts.SODP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.SODP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.POTQ != "") }
      <!-- Potassium -->
      {draw_line width="1" indent="no"}
      <tr>
      <td colspan="7" class="Nutrition"><b>Potassium</b> {$nutfacts.POTQ}</td>
      {if ($nutfacts.POTP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.POTP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.TCARBQ != "") }
      <!-- Total Carb. -->
      {draw_line width="1" indent="no"}
      <tr>
      <td colspan="7" class="Nutrition"><b>Total Carb.</b> {$nutfacts.TCARBQ}</td>
      {if ($nutfacts.TCARBP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.TCARBP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.DFIBQ != "") }
      <!-- Dietary Fiber - indented -->
      {draw_line width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Dietary Fiber {$nutfacts.DFIBQ}</td>
      {if ($nutfacts.DFIBP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.DFIBP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.SUGQ != "") }
      <!-- Sugars - indented -->
      {draw_line width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Sugars {$nutfacts.SUGQ}</td>
      {if ($nutfacts.SUGP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.SUGP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {if ($nutfacts.PROTQ != "") }
      <!-- Protein -->
      {draw_line width="1" indent="no"}
      <tr>
      <td colspan="7" class="Nutrition"><b>Protein</b> {$nutfacts.PROTQ}</td>
      {if ($nutfacts.PROTP != "") }
         <td class="Nutrition"><div align="right"> 
         <b>{$nutfacts.PROTP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   
   {draw_line width="8" indent="no"}

<!-- end section 3 -->

<!-- section 4 -->

   {assign var="toggle" value="1"}

   {if ($nutfacts.VITAP != "") }
      <!-- Vitamin A -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Vitamin A</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITAP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Vitamin A</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITAP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.VITCP != "") }
      <!-- Vitamin C -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Vitamin C</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITCP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Vitamin C</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITCP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.CALCP != "") }
      <!-- Calcium -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Calcium</td>
         <td class="Nutrition"><div align="right">{$nutfacts.CALCP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Calcium</td>
         <td class="Nutrition"><div align="right">{$nutfacts.CALCP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.IRONP != "") }
      <!-- Iron -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Iron</td>
         <td class="Nutrition"><div align="right">{$nutfacts.IRONP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Iron</td>
         <td class="Nutrition"><div align="right">{$nutfacts.IRONP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.VITDP != "") }
      <!-- Vitamin D -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Vitamin D</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITDP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Vitamin D</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITDP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.VITEP != "") }
      <!-- Vitamin E -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Vitamin E</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITEP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Vitamin E</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITEP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.VITB6P != "") }
      <!-- Vitamin B6 -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Vitamin B6</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITB6P}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Vitamin B6</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITB6P}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.VITB12P != "") }
      <!-- Vitamin B12 -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Vitamin B12</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITB12P}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Vitamin B12</td>
         <td class="Nutrition"><div align="right">{$nutfacts.VITB12P}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.THIAP != "") }
      <!-- Thiamin -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Thiamin</td>
         <td class="Nutrition"><div align="right">{$nutfacts.THIAP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Thiamin</td>
         <td class="Nutrition"><div align="right">{$nutfacts.THIAP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.RIBOP != "") }
      <!-- Riboflavin -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Riboflavin</td>
         <td class="Nutrition"><div align="right">{$nutfacts.RIBOP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Riboflavin</td>
         <td class="Nutrition"><div align="right">{$nutfacts.RIBOP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.PHOSP != "") }
      <!-- Phosphorous -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Phosphorous</td>
         <td class="Nutrition"><div align="right">{$nutfacts.PHOSP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Phosphorous</td>
         <td class="Nutrition"><div align="right">{$nutfacts.PHOSP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.MAGNP != "") }
      <!-- Magnesium -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Magnesium</td>
         <td class="Nutrition"><div align="right">{$nutfacts.MAGNP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Magnesium</td>
         <td class="Nutrition"><div align="right">{$nutfacts.MAGNP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   {if ($nutfacts.NIACP != "") }
      <!-- Niacin -->
      {if ($toggle == 1) }
         {assign var="toggle" value="2"}
         <tr>
         <td colspan="2" class="Nutrition">Niacin</td>
         <td class="Nutrition"><div align="right">{$nutfacts.NIACP}%</div></td>
      {else}
         {assign var="toggle" value="1"}
         <td class="Nutrition"><div align="center">&#149;</div></td>
         <td colspan="3" class="Nutrition">Niacin</td>
         <td class="Nutrition"><div align="right">{$nutfacts.NIACP}%</div></td>
         </tr>
         {draw_line width="1" indent="no"}
      {/if}
   {/if}

   
   {if ($toggle == 2) }
      <td colspan="5" class="Nutrition">&nbsp;</td>
      </tr>
   {/if}

<!-- end section 4 -->

<!-- section 5 -->

   {if ($toggle == 2) }
      {draw_line width="1" indent="no"}
   {/if}

   {if ($nutfacts.STMT1Q != "") }
      <tr>
      <td colspan="8" class="Nutrition">{$nutfacts.STMT1Q}</td>
      </tr>
      {draw_line width="1" indent="no"}
   {/if}

   {if strtoupper($nutfacts.STMT2) == "YES"}
      <tr>
      <td colspan="8" class="Nutrition">* {$nutfacts.STMT2Q}</td>
      </tr>
      <tr>
      <td colspan="8" class="Nutrition"><img src="/images/dot_clear.gif" width="2" height="1" alt=""></td>
      </tr>
   {/if}

   {if (strtoupper($nutfacts.PDV1) == "YES") }   
      <tr>
      <td colspan="8" class="Nutrition">{if strtoupper($nutfacts.STMT2) == "YES"}**{else}*{/if} Percent Daily Values are based on a 2,000 calorie diet.</td>
      </tr>
   {/if}

   {if (strtoupper($nutfacts.PDV2) == "YES") }
      <tr>
      <td colspan="8" class="Nutrition">{if strtoupper($nutfacts.STMT2) == "YES"}**{else}*{/if} Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.</td>
      </tr>
   {/if}

   {if (strtoupper($nutfacts.PDVT) == "YES") }
      <tr>
      <td colspan="8"><img src="/images/dot_black.gif" width="232" height="1" alt=""></td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">&nbsp;</td>
      <td colspan="3" class="NutritionSm">Calories:</td>
      <td class="NutritionSm">2,000</td>
      <td colspan="2" class="NutritionSm">2,500</td>
      </tr>

      <tr>
      <td colspan="8"><img src="/images/dot_black.gif" width="232" height="1" alt=""></td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">Total Fat</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">65g</td>
      <td colspan="2" class="NutritionSm">80g</td>
      </tr>

      <tr>
      <td class="NutritionSm">&nbsp;</td>
      <td class="NutritionSm">Sat Fat</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">20g</td>
      <td colspan="2" class="NutritionSm">25g</td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">Cholesterol</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">300mg</td>
      <td colspan="2" class="NutritionSm">300mg</td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">Sodium</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">2,400mg</td>
      <td colspan="2" class="NutritionSm">2,400mg</td>
      </tr>

      <tr>
      <td colspan="5" class="NutritionSm">Total Carbohydrate</td>
      <td class="NutritionSm">300g</td>
      <td colspan="2" class="NutritionSm">375g</td>
      </tr>

      <tr>
      <td class="NutritionSm">&nbsp;</td>
      <td colspan="4" class="NutritionSm">Dietary Fiber</td>
      <td class="NutritionSm">25g</td>
      <td colspan="2" class="NutritionSm">30g</td>
      </tr>
   {/if}
   
<!-- end section 5 -->

<!-- table footer -->

   <tr>
   <td colspan="8"><img src="/images/dot_clear.gif" width="232" height="6" alt=""></td>
   </tr>
   </table>
   
</td></tr>
</table>

<!-- end table footer -->

<table width="246" cellpadding="6" cellspacing="0" border="0">
<tr>
<td class="Nutrition"><p>The most accurate information is always on the label on the actual product. We periodically update our labels based on new nutritional analysis to verify natural variations from crop to crop and at times formula revisions. The website does not necessarily get updated at the same time. The values on the website are intended to be a general guide to consumers. For absolute values, the actual label on the product at hand should be relied on.</p></td>
</tr>
</table>

</div>