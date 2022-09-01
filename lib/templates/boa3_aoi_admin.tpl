
{literal}
<script type="text/javascript">
<!--
function docancel1()
{
  document.location = "index.php";
}

function docancel2()
{
  document.location = "aoi_admin.php";
}

function docreate()
{
  document._form.what.value = "create";
  document._form.submit();
}

function doload()
{
  document._form.what.value = "load";
  document._form.submit();
}
-->
</script>
{/literal}

<h1 class="pageHd">Business Objects Area of Interest (AOI) Administration</h1>

<div class="notice">
Please select the user you'd like to edit, or click 'Create new' to create
a new user in the database.
</div>

<div id="dataarea">

<form name="_form" action="aoi_admin.php" method="post">

<input type="hidden" name="what" value="list" />
<input type="hidden" name="copy_to" value="" />
<input type="hidden" name="mail_to" value="" />
<input type="hidden" name="email_user" value="" />
<input type="hidden" name="user_email" value="{$user_email}" />


<div class="selectuser">

    <b>User:</b>&nbsp;
{if $what == "create" }
   <input type="textfield" name="user_id" size="20" />
   <input type="submit" value="Continue" onclick="doload()" />
{else}
   <select name="user_id" size="1" onchange="doload()">
   <option value="0">(select one)</option>
   {section name="user" loop=$user_list }
      <option value="{$user_list[user].user_id}">{$user_list[user].user_id}</option>
   {/section}
   </select><font color="#FFFFFF">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</b></font>
{/if}

</div> {* selectuser *}

<div class="datafooter">

   {if $what == "create"}
   <input type="button" value="Cancel" onclick="docancel2()" />
   {else}
   <input type="button" value="Create New" onclick="docreate()" />
   <input type="button" value="Cancel" onclick="docancel1()" />   
   {/if}

</div> {* datafooter *}

</form>

</div> {* dataarea *}