{if $jobs.status == 1}

   <p class="PageSbhd">{$jobs.title}</p>

   {if $jobs.summary != ""}
      <p><b>Summary:</b> {$jobs.summary}</p>
   {/if}
   
   {if $jobs.location != ""}
      <p><b>Location:</b> {$jobs.location}</p>
   {/if}

   <p>{$jobs.description}</p>

{else}

   <p class="PageSbhd">{$jobs.title}</p>

   <p>This job is no longer available.</p>

{/if}