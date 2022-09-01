{if $passwd.draw_form == 1 }

   <center>

   <table width="300" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
   <tr><td align="center">
   <table width="100%" cellpadding="4" cellspacing="0" border="0" bgcolor="#CC9900">
   <tr><td align="center">
   <table width="100%" cellpadding="12" cellspacing="0" border="0" bgcolor="#FFFFFF">

   <tr>
   <td><span class="pageHd">Change Your LDAP Password</span>
   <br>{$server}
   <br>&nbsp;
   <br><img src="/images/dot_black.gif" width="376" height="2" alt=""></td>
   </tr>
		
   <tr>
   <td>
   <p>This password is used for e-mail, calendar, and manbase. If you've forgotten your password, call the help desk (x1310), and they will reset it for you. <i><a target="_blank" href="/help/what_is_ldap.php" onclick="popup(this.href,'help'); return false;"><img src="/images/qmark.gif" width="13" height="13" alt="(?)" border="0"> What is LDAP?</a></i></p>
   </td>
   </tr>

   <tr>
   <td align="center">

   <form method="post" action="/utils/passwd.php">
   <table cellpadding="4" cellspacing="0" border="0" align="center">


   {if $passwd.access_level >= 2 }

      <tr>
      <td>Username: </td>
      <td>{$passwd.valid_user}</td>
      </tr>

   {else}

      <tr>
      <td>Username: </td>
      <td><input type="text" size="35" name="pw_userid" value="{$passwd.pw_userid}"></td>
      </tr>
		
   {/if}
   
   {if $passwd.error.pw_userid != "" }
      <tr>
      <td colspan="2"><span class="errorNotice">{$passwd.error.pw_userid}</span></td>
      </tr>
   {/if}
   <tr>
   <td>Old Password: </td>
   <td><input type="password" size="35" name="pw_password" value="{$passwd.pw_password}"></td>
   </tr>
   {if $passwd.error.pw_password != "" }
      <tr>
      <td colspan="2"><span class="errorNotice">{$passwd.error.pw_password}</span></td>
      </tr>
   {/if}
   <tr>
   <td>New Password: </td>
   <td><input type="password" size="35" name="pw_newpw1" value="{$passwd.pw_newpw1}"></td>
   </tr>
   <tr>
   <td><span class="red">Re-enter</span><br>New Password: </td>
   <td><input type="password" size="35" name="pw_newpw2" value="{$passwd.pw_newpw2}"></td>
   </tr>
   {if $passwd.error.pw_newpw2 != "" }
      <tr>
      <td colspan="2"><span class="errorNotice">{$passwd.error.pw_newpw2}</span></td>
      </tr>
   {/if}
   <tr>
   <td colspan="2"><div align="center"><input type="submit" value="Change Password"></div></td>
   </tr>
   </table>
   <input type="hidden" name="pw_type" value="user">
   <input type="hidden" name="no_cache" value="true">
   </form>


   {if $passwd.access_level == 3 }

      <p><img src="/images/dot_black.gif" width="376" height="2" alt=""></p>

      <p class="PageSbhd">Change Someone Else's Password</p>
      <form method="post" action="/utils/passwd.php">
      <table cellpadding="4" cellspacing="0" border="0" align="center">
      <tr>
      <td>Admin's Password: </td>
      <td><input type="password" size="35" name="pw_password" value="{$passwd.pw_password}"></td>
      </tr>
      {if $passwd.error.pw_password != "" }
         <tr>
         <td colspan="2"><span class="errorNotice">{$passwd.error.pw_password}</span></td>
         </tr>
      {/if}
      <tr>
      <td>Username: </td>
      <td><input type="text" size="35" name="admin_uid" value="{$passwd.admin_uid}"></td>
      </tr>
      {if $passwd.error.admin_uid != "" }
         <tr>
         <td colspan="2"><span class="errorNotice">{$passwd.error.admin_uid}</span></td>
         </tr>
      {/if}
      <tr>
      <td>New Password: </td>
      <td><input type="password" size="35" name="admin_newpw1" value="{$passwd.admin_newpw1}"></td>
      </tr>
      <tr>
      <td><span class="red">Re-enter</span><br>New Password: </td>
      <td><input type="password" size="35" name="admin_newpw2" value="{$passwd.admin_newpw2}"></td>
      </tr>
      {if $passwd.error.admin_newpw2 != "" }
         <tr>
         <td colspan="2"><span class="errorNotice">{$passwd.error.admin_newpw2}</span></td>
         </tr>
      {/if}
      <tr>
      <td colspan="2"><div align="center"><input type="submit" value="Change Password"></div></td>
      </tr>
      </table>
      <input type="hidden" name="pw_type" value="admin">
      <input type="hidden" name="no_cache" value="true">
      </form>

   {/if}
	
   <p>&gt; <a href="/index.php">Return to Home Page</a></p>
	
   </td></tr></table>
   </td></tr></table>
   </td></tr></table>

   </center>

{else}

   {if $passwd.error != "" }
   
      <div align="center">
      <p align="center"><b>There were some problems:</b></p>
      <p class="errorNotice">{$passwd.error}
      <br>&nbsp;</p>
      </div>
   
   {else}
   
      <p align="center"><b>Changes were saved successfully.</b></p>
   
   {/if}

   <p align="center"><a href="/index.php">[Return to Home Page]</a>
   {if $passwd.access_level == 3 }
      &nbsp;<a href="/utils/passwd.php">[Change Another Password]</a></p>
   {else}
      </p>
   {/if}


{/if}