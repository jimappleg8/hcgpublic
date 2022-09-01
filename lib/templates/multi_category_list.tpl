<p class="pageHd">{$category.title}</p>

{section name="listitem" loop=$category.list}

   <p><span class="pageSbhd">{$category.list[listitem].CategoryName}</span>
   
   {section name="catitem" loop=$items[listitem]}

   <br><a href="/products/product.php?prod_id={$items[listitem][catitem].ProductID}&cat_name={$category.id}">{$items[listitem][catitem].ProductName}</a>

   {/section}
   
   </p>
	
{/section}

