To: {$mail.boa_admin}
From: Business Object Admin <webmaster@hain-celestial.com>
Reply-To: Business Object Admin <webmaster@hain-celestial.com>
Subject: User Change Request

A user change request has been submitted by {$mail.common_name}.

The list of regions below is all the regions this person should have access to.

User's login name : {$mail.user_id}
   Custom reports : {$mail.create_reports}
     View reports : {$mail.view_reports}

Source regions :
  {section name="region" loop=$mail.region_list}
     {$mail.region_list[region].DWSPERSONCODE} | {$mail.region_list[region].DWSPERSONDESC}
  {/section}