<div class="navhead">
Business Objects
</div>
<link rel="STYLESHEET" type="text/css" href="/style/ctBOAdmin.css">
{ foreach key=name item=url from=$data.nav }
<a href="{$url}" class="navitem">{$name}</a>
{ /foreach }
