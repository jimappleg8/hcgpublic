{if $num_faqs > 0 }

<p>This first section shows how a list can be generated to link to other pages where each question and answer has it's own page.</p>

   <ul>
   {section name="faq" loop="$faqs"}
      {if $faqs[faq].shortquestion != "" }
         <li><a href="/faqs/faq_detail.php?faq_num={$faqs[faq].faqid}">{$faqs[faq].shortquestion}</a>{if $faqs[faq].flagasnew == 1} <span class="red">NEW!</span><br>{/if}</li>
      {else}
         <li><a href="/faqs/faq_detail.php?faq_num={$faqs[faq].faqid}">{$faqs[faq].question}</a>{if $faqs[faq].flagasnew == 1} <span class="red">NEW!</span><br>{/if}</li>      
      {/if}
   {/section}
   </ul>
   
<p>This section creates a list with anchor links to sections within the same page. This is how you can list all the FAQs on a single page.</p>

   <ul>
   {section name="faq" loop="$faqs"}
      {if $faqs[faq].shortquestion != "" }
         <li><a href="/faqs/faq_list.php#faqid{$faqs[faq].faqid}">{$faqs[faq].shortquestion}</a>{if $faqs[faq].flagasnew == 1} <span class="red">NEW!</span><br>{/if}</li>
      {else}
         <li><a href="/faqs/faq_list.php#faqid{$faqs[faq].faqid}">{$faqs[faq].question}</a>{if $faqs[faq].flagasnew == 1} <span class="red">NEW!</span><br>{/if}</li>      
      {/if}
   {/section}
   </ul>
   
   {section name="faq" loop="$faqs"}
   
   <a name="faqid{$faqs[faq].faqid}"></a>
   <p><b>{$faqs[faq].question}</b></b>
   <p>{$faqs[faq].answer}
   <br>&nbsp;</p>

   {/section}

{else}

   <ul>
   <li>There are no FAQs at this time.</li>
   </ul>
   
{/if}