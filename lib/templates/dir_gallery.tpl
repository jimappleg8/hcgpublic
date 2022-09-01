   <table cellpadding="0" cellspacing="0" border="0">
   <tr>
   <td colspan="3" bgcolor="#000000"><img src="/images/dot_clear.gif" width="590" height="1" alt=""></td>
   </tr>
   {foreach from=$gallery item=image }
   <tr>
   <td colspan="3"><img src="/images/dot_clear.gif" width="590" height="6" alt=""></td>
   </tr>
   <tr>
   <td width="80"><div align="center"><a href="javascript:popup('preview.php?image={$image.doc_pwd}{$image.preview}','preview')"><img src="{$image.doc_pwd}{$image.thumbnail}" alt="{$image.heading}" border="0"></a></div></td>
   <td width="10"><img src="/images/dot_clear.gif" width="10" height="80" alt=""></td>
   <td width="500">
      <p><b>{$image.heading}</b>
      <br>{$image.description}</p>

      <p><a href="javascript:popup('preview.php?image={$image.doc_pwd}{$image.preview}','preview')">View Preview File</a> | <a href="{$image.doc_pwd}{$image.file}">Download high-res file</a> ({$image.file}, {$image.size})</p>
   </td>
   </tr>
   <tr>
   <td colspan="3"><img src="/images/dot_clear.gif" width="590" height="6" alt=""></td>
   </tr>
   <tr>
   <td colspan="3" bgcolor="#000000"><img src="/images/dot_clear.gif" width="590" height="1" alt=""></td>
   </tr>
   {/foreach}
   </table>