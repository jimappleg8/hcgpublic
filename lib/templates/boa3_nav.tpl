<div id="leftnav">
<ul>
<li class="head">Business Objects</li>
{section name="nav" loop=$data}
<li{if $data[nav].highlight == 1} class="hilite"{/if}><a href="{$data[nav].URL}">{$data[nav].label}</a></li>
{/section}
</ul>
</div>