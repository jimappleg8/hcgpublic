{if $num_jobs > 0 }

   <ul>
   {section name="job" loop="$jobs"}
     <li><a href="/whoweare/jobs/jobs.php?job_num={$jobs[job].jobid}">{$jobs[job].title}</a></li>
   {/section}
   </ul>

{else}

   <ul>
   <li>There are no open positions at this time. We are only 
able to accept resumes for open positions.</li>
   </ul>
   
{/if}