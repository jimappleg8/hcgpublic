{if $search.result_type == "groups"}

   <center>
   <table width="90%" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="1">
   <tr>
   <td colspan="3"><span class="pageHd">HCG E-mail Group Search Results</span></td>
   </tr>
   <tr>
   <td><b>Group Name</b></td>
   <td><b>Description</b></td>
   <td><b>E-mail</b></td>
   </tr>
   
   {section name=group loop=$search.cn}
      <tr>
      <td><a href="/emploc/grpprofile.php?cn={$search.cn_url[group]}">{$search.cn[group]|default:"&nbsp;"}</a></td>
      <td>{$search.description[group]|default:"&nbsp;"}</td>
      <td><a href="mailto:{$search.mail[group]}">{$search.mail[group]|default:"&nbsp;"}</a></td>
      </tr>
   {/section}

   </table>
   <p>&nbsp;</p>
   </center>

{elseif $search.result_type == "people"}

   <center>
   <table width="90%" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="1">
   <tr>
   {if $search.access_level == 3 }
      <td colspan="5"><span class="pageHd">HCG Employee Search Results</span></td>
   {else}
      <td colspan="4"><span class="pageHd">HCG Employee Search Results</span></td>
   {/if}
   </tr>
   <tr>
   <td><b>Name</b></td>
   <td><b>Telephone</b></td>
   <td><b>E-mail</b></td>
   <td><b>Title</b></td>
   {if $search.access_level == 3 }
      <td>&nbsp;</td>
   {/if}
   </tr>
   
   {section name=people loop=$search.cn}
      <tr>
      <td><a href="/emploc/profile.php?uid={$search.uid[people]}">{$search.cn[people]|default:"&nbsp;"}</a></td>
      <td>{$search.telephonenumber[people]|default:"&nbsp;"}</td>
      <td><a href="mailto:{$search.mail[people]}">{$search.mail[people]|default:"&nbsp;"}</a></td>
      <td>{$search.title[people]|default:"&nbsp;"}</td>
      {if $search.access_level == 3 }
         <td><a href="/emploc/editprofile.php?mod_action=edit&uid={$search.uid[people]}">[Edit]</a></td>
      {/if}
      </tr>
   {/section}

   </table>
   <p>&nbsp;</p>
   </center>

{elseif $search.result_type == "error"}

   <div align="center">
   <span class="errorNotice">{$search.error}
   <br>&nbsp;</span>
   </div>

{/if}


{if $search.formsize == "short"}

   <center>
   <table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%"> <tr><td> <table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%"> <tr><td>

   <span class="pageHd">Search Again</span>

   <center>
   <form method="POST" name="searchForm" action="/emploc/search.php">
   <span class="blockTxt">First or last name:
   <input name="quick" size="20">
   <input type="SUBMIT" value="Locate Employee">
&nbsp; &gt; <a href="/emploc/search.php">More Search Options</a></span>
   <input type="hidden" name="searchType" value="q">
   </form>
   </center>

</td></tr></table></td></tr></table>
</center>


{else}

   <center>
   <table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%"> <tr><td> <table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%"> <tr><td>

      <span class="pageHd">Employee Locator</span>
	
      <img src="/images/dot_clear.gif" width="100%" height="4" alt="">
      <img src="/images/dot_black.gif" width="100%" height="2" alt="">

      <p class="pageSbhd">Search for Employees</p>
	
      <p>Search for Hain Celestial Group employees by entering search criteria in the fields below and hitting the [Locate Employee] button.</p>

   <form method="post" action="/emploc/search.php">
   <pre>    First Name:  <input type="text" size="35" name="givenName" value="">
   <br>    Last Name:   <input type="text" size="35" name="sn" value="">
   <br>    Telephone:   <input type="text" size="35" name="telephonenumber" value="">
   <br>    Title:       <input type="text" size="35" name="title" value=""></pre>
   <br><input type="submit" value="Locate Employee">&nbsp;<input type="reset" value="Reset">
   <input type="hidden" name="searchType" value="a">
   </form>

      <img src="/images/dot_black.gif" width="100%" height="2" alt="">
	
      <p class="pageSbhd">Browse for Employees</p>
	
      <p>Browse Hain Celestial Group employees by last name by clicking on the appropriate letter.

      <p>
      <a href="/emploc/search.php?searchType=f&sn=A">A</a> | 
      <a href="/emploc/search.php?searchType=f&sn=B">B</a> | 
      <a href="/emploc/search.php?searchType=f&sn=C">C</a> | 
      <a href="/emploc/search.php?searchType=f&sn=D">D</a> | 
      <a href="/emploc/search.php?searchType=f&sn=E">E</a> | 
      <a href="/emploc/search.php?searchType=f&sn=F">F</a> | 
      <a href="/emploc/search.php?searchType=f&sn=G">G</a> | 
      <a href="/emploc/search.php?searchType=f&sn=H">H</a> | 
      <a href="/emploc/search.php?searchType=f&sn=I">I</a> | 
      <a href="/emploc/search.php?searchType=f&sn=J">J</a> | 
      <a href="/emploc/search.php?searchType=f&sn=K">K</a> | 
      <a href="/emploc/search.php?searchType=f&sn=L">L</a> | 
      <a href="/emploc/search.php?searchType=f&sn=M">M</a> | 
      <a href="/emploc/search.php?searchType=f&sn=N">N</a> | 
      <a href="/emploc/search.php?searchType=f&sn=O">O</a> | 
      <a href="/emploc/search.php?searchType=f&sn=P">P</a> | 
      <a href="/emploc/search.php?searchType=f&sn=Q">Q</a> | 
      <a href="/emploc/search.php?searchType=f&sn=R">R</a> | 
      <a href="/emploc/search.php?searchType=f&sn=S">S</a> | 
      <a href="/emploc/search.php?searchType=f&sn=T">T</a> | 
      <a href="/emploc/search.php?searchType=f&sn=U">U</a> | 
      <a href="/emploc/search.php?searchType=f&sn=V">V</a> | 
      <a href="/emploc/search.php?searchType=f&sn=W">W</a> | 
      <a href="/emploc/search.php?searchType=f&sn=Z">X</a> | 
      <a href="/emploc/search.php?searchType=f&sn=Y">Y</a> | 
      <a href="/emploc/search.php?searchType=f&sn=Z">Z</a>
      <br>&nbsp;</p>
	
      <img src="/images/dot_black.gif" width="100%" height="2" alt="">

      <p class="pageSbhd">Search for E-mail Groups</p>
	
      <p>Search for HCG E-mail groups by entering all or part of a group name in the field below and hitting the [Locate Group] button. Or view a <a href="/emploc/search.php?searchType=g&groupname=%25%25ALL%25%25">listing of all e-mail groups</a>.</p>

      <form method="post" action="/emploc/search.php">
      <pre>    Group Name:  <input type="text" size="35" name="groupname"></pre>
      <br><input type="submit" value="Locate Group">&nbsp;
      <input type="reset" value="Reset">
      <input type="hidden" name="searchType" value="g">
      </form>

   </td></tr></table></td></tr></table>
   </center>

{/if}