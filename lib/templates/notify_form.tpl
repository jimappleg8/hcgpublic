{if $doit != true }

   <center>

   <p class="pageSbhd">Sign up below to receive notices
   <br>as new features are added to the site.</p>

   <form name="notify" method="post" action="/about_us/notify_me.php">

   {input name="doit"}
   {input name="user_track"}
      
      <table cellpadding="3" cellspacing="0" border="0">

      <tr>
      <td valign="top"><div align="right">{label for="name"}<span class="red">*</span>:</div></td>
      <td>{input name="name"}
      {if isset($verify.name)}
      <br><span class="red">{$errors.name}</span>
      {/if}
      </td>
      </tr>

      <tr>
      <td valign="top"><div align="right">{label for="email"}<span class="red">*</span>:</div></td>
      <td>{input name="email"}
      {if isset($verify.email)}
      <br><span class="red">{$errors.email}</span>
      {/if}
      </td>
      </tr>

      <tr>
      <td valign="top"><div align="right">{label for="email2"}<span class="red">*</span>:</div></td>
      <td>{input name="email2"}
      {if isset($verify.email2)}
      <br><span class="red">{$errors.email2}</span>
      {/if}
      </td>
      </tr>

      <tr>
      <td>&nbsp;</td>
      <td>{input name="button_signup"} {input name="button_reset"}</td>
      </tr>
      
      <tr>
      <td width="180"><img src="/images/dot_clear.gif" width="180" height="1" alt=""></td>
      <td width="100%"><img src="/images/dot_clear.gif" width="560" height="1" alt=""></td>
      </tr>
      
      </table>

   </form>

   <p class="smalltxt"><b>Privacy Policy:</b> Your email address will be used to send you occasional notices as new features and information is added to the site. Your email address will not be sold, and you will not be added to any other mailing lists for this site. After one year, your name will be removed from the list. If you would like to receive other mailings from us, such as notices about promotions or special offers, please <a href="/about_us/contact_us.php">send us a message</a> and request that you be added to our mailing list.</p>

   </center>

{else}

   <center>

   <p>&nbsp;</p>

   <table width="500" cellpadding="0" cellspacing="0" border="0">
   <tr>
   <td>
   <center><h2>Thank You!</h2>
   <p>Your e-mail address has been saved. 
   <br>We'll notify you as new features are added to the site.</p>

   <p class="smalltxt"><b>Privacy Policy:</b> Your email address will be used to send you occasional notices as new features and information is added to the site. Your email address will not be sold, and you will not be added to any other mailing lists for this site. After one year, your name will be removed from the list. If you would like to receive other mailings from us, such as notices about promotions or special offers, please <a href="/about_us/contact_us.php">send us a message</a> and request that you be added to our mailing list.</p>

   <p>&nbsp;</p>
   <p><a href="/index.html">Return to Home Page</a></p>
   </center>
   </td>
   </tr>
   </table>

   </center>

{/if}