{if $tabs}
<div id="menubar">
<ul>
{foreach key="name" item="url" from=$tabs}
   <li{if $name == $this_tab} id="current"{/if}><a href="{$url}" onclick="dotab(this.href); return false;">{$name}</a></li>
{/foreach}
   <li id="spacer">|</li>
</ul>
</div>
{/if}
