{if $arrayErrorMessage || $arrayWarningMessage || $arrayMiscMessage}

<div id="errmsg">
{if $arrayErrorMessage}
	<p class="ErrorMessage">	
{foreach from=$arrayErrorMessage item=currentMsg}
		<strong>Error:</strong><br/>
		{$currentMsg}<br/>
{/foreach}
	</p>
{/if}

{if $arrayWarningMessage}
	<p class="WarningMessage">
		<strong>Information:</strong><br/>
{foreach from=$arrayWarningMessage item=currentMsg}
		{$currentMsg}<br/>
{/foreach}
	</p>
{/if}

{if $arrayMiscMessage}
	<p class="MiscMessage">
		<strong>Misc Error:</strong><br/>
{foreach from=$arrayMiscMessage item=currentMsg}
		{$currentMsg}<br/>
{/foreach}
	</p>
{/if}
</div>

{/if}