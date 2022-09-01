{if $faqs.status == 1}

   <p>{if $faqs.flagasnew == 1}<span class="red">NEW!</span><br>{/if}
   <b>{$faqs.question}</b></p>
   <p>{$faqs.answer}</p>

{else}

   <p>This FAQ is no longer available.</p>

{/if}