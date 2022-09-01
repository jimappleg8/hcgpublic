To: Webmaster <webmaster@hain-celestial.com>
From: Jim Applegate <japplega@hain-celestial.com>
Subject: Default Mail Template 1

Dear Webmaster:

The following form information was sent via the default mail
template. There is probably a problem with the local template.
Please forward this information to the appropriate person.

{foreach key=key item=item from=$mail}
   {if (is_array($item)) }
      {foreach key=key2 item=item2 from=$item}
         {$key2}: {$item2}
      {/foreach}
   {else}
      {$key}: {$item}
   {/if}
{/foreach}

Sincerely,
   The MailForm script