To: Jim Applegate <japplega@hain-celestial.com>
From: Hain Celestial Group <do-not-reply@hain-celestial.com>
Subject:

Subject: webform
URL: http://{$mail.URL}
{if $mail.FName != ""}
fname: {$mail.FName}
{/if}
{if $mail.LName != ""}
lname: {$mail.LName}
{/if}
{if $mail.Address1 != ""}
address1: {$mail.Address1}
{/if}
{if (trim($mail.Address2) != "")}
address2: {$mail.Address2}
{/if}
{if $mail.City != ""}
city: {$mail.City}
{/if}
{if $mail.State != ""}
state: {$mail.State}
{/if}
{if $mail.Zip != ""}
zip: {$mail.Zip}
{/if}
{if $mail.Phone != ""}
phone: {$mail.Phone}
{/if}
{if $mail.Email != ""}
email: {$mail.Email}
{/if}
{if $mail.Comment != ""}
comment: {$mail.Comment}
{/if}
{if $mail.Favorites != ""}
favorites: {$mail.Favorites}
{/if}
{if $mail.Site != ""}
site: {$mail.Site}
{/if}
{if $mail.Marketing != ""}
marketing: {$mail.Marketing}
{/if}
{if $mail.Release != ""}
release: {$mail.Release}
{/if}

