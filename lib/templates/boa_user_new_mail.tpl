To: {$mail.boa_admin}
From: Business Object Admin <webmaster@hain-celestial.com>
Reply-To: Business Object Admin <webmaster@hain-celestial.com>
Subject: New User Request

A new user request has been submitted by {$mail.common_name}:

User's full name : {$mail.user_name}
        Location : {$mail.location}
  Custom reports : {$mail.create_reports}
    View reports : {$mail.view_reports}

  Source regions :
  {section name="region" loop=$mail.region_list}
     {$mail.region_list[region].DWSPERSONCODE} | {$mail.region_list[region].DWSPERSONDESC}
  {/section}