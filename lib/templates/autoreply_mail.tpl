To: {$mail.FName} {$mail.LName} <{$mail.Email}>
From: {$mail.brand_name} <do-not-reply@hain-celestial.com>
Subject: Your e-mail to {$mail.brand_name} was received

{$mail.DateSent}

{if $mail.FName != ""}
Dear {$mail.FName}, our Valued Customer:
{else}
Hello:
{/if}

This is an auto-response email let you know that we have received your email. Thank you for taking the time to contact us, as always, we value your input.

Currently we are experiencing an increased email volume and responses may take up to 30 days. For further assistance our Consumer Relations Team is available to help you by calling 1-800-434-4246 (Monday - Friday 9-5 mtn time). Thank you.
