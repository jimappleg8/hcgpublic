{* set display type *}
{* $dtype = 1 - display percentage match as number *}
{* $dtype = 2 - display images/stars *}

{assign var="dtype" value="1" }
{assign var="rating_image" value='<img src="/images/search/rating.gif" width="15" height="13" alt="*">' }


{* search form *}
<p>
<form method="post" action="/search/search_results.php">
<font size="-1">

Match: <select name="method">
{if $options.method == "and"}
<option value="and" selected>All</option>
{else}
<option value="and">All</option>
{/if}
{if $options.method == "or"}
<option value="or" selected>Any</option>
{else}
<option value="or">Any</option>
{/if}
{if $options.method == "boolean"}
<option value="boolean" selected>Boolean</option>
{else}
<option value="boolean">Boolean</option>
{/if}
</select>

Sort by: <select name="sort">
{if $options.sort == "score"}
<option value="score" selected>Score</option>
{else}
<option value="score">Score</option>
{/if}
{if $options.sort == "time"}
<option value="time" selected>Time</option>
{else}
<option value="time">Time</option>
{/if}
{if $options.sort == "title"}
<option value="title" selected>Title</option>
{else}
<option value="title">Title</option>
{/if}
{if $options.sort == "revscore"}
<option value="revscore" selected>Reverse Score</option>
{else}
<option value="revscore">Reverse Score</option>
{/if}
{if $options.sort == "revtime"}
<option value="revtime" selected>Reverse Time</option>
{else}
<option value="revtime">Reverse Time</option>
{/if}
{if $options.sort == "revtitle"}
<option value="revtitle" selected>Reverse Title</option>
{else}
<option value="revtitle">Reverse Title</option>
{/if}
</select>
</font>
<input type="hidden" name="config" value="htdig">
<input type="hidden" name="restrict" value="">
<input type="hidden" name="exclude" value="">
<br>
Search:
<input type="text" size="30" name="words" value="{$options.words}">
<input type="submit" value="Search">
</form>
<br>&nbsp;</p>

{if $results.MatchCount > 0}

&nbsp;<br><hr noshade size="1">

   {* results layout *}
   <p class="PageHd">Search results for '{$results.Words}'</p>

   <hr noshade size="1">
   <b>Documents {$results.FirstMatch} - {$results.LastMatch} of {$results.MatchCount} matches.  </b>
	
   {if $dtype == 2 }
      <b>More {$rating_image}'s indicate a better match.</b>
   {/if}

   <hr noshade size="1">


   {section name=match loop=$results.Matches}

   <dl><dt><strong><a href="{$results.Matches[match].URL}">{$results.Matches[match].Title}</a></strong>&nbsp;&nbsp;

   {php}

      $index = $this->_sections['match']['index'];
      $percent = $this->_tpl_vars['results']['Matches'][$index]['Percent'];
      $counter = ($percent/20);

      if ($this->_tpl_vars['dtype'] == 1) {
         echo $percent."% match";
      } 

      if ($this->_tpl_vars['dtype'] == 2) {
        for ( ; $counter>=1; $counter--) {
	       echo $this->_tpl_vars['rating_image'];
	    }
      }

   {/php}

   </dt><dd>{$results.Matches[match].Excerpt}<br>
   <em><a href="{$results.Matches[match].URL}">{$results.Matches[match].URL}</a></em>
   <font size="-1">{$results.Matches[match].Modified}, {$results.Matches[match].Size} bytes</font>
   </dd></dl>

   {/section}

   <hr>
   Pages:<br>

   {if $options.page != 1 }

   <a href="{$results.php_self}?method={$options.method}&matchesperpage={$options.matchesperpage}&words={$options.words}&page={math equation="x-1" x=$options.page}"><img src="/images/search/buttonl.gif" border="0" align="middle" width="30" height="30" alt="2"></a>

   {/if}

   {section name="page" loop=$results.page_loop }

   {if $results.page_loop[page] == $options.page}
   <img src="/images/search/button{$results.page_loop[page]}.gif" border="2" align="middle" width="30" height="30" alt="{$results.page_loop[page]}">
   {else}
   <a href="{$results.php_self}?method={$options.method}&matchesperpage={$options.matchesperpage}&words={$options.words}&page={$results.page_loop[page]}"><img src="/images/search/button{$results.page_loop[page]}.gif" border="0" align="middle" width="30" height="30" alt="{$results.page_loop[page]}"></a>
   {/if}

   {/section}

   {if $options.page != $results.num_pages }

   <a href="{$results.php_self}?method={$options.method}&matchesperpage={$options.matchesperpage}&words={$options.words}&page={math equation="x+1" x=$options.page}"><img src="/images/search/buttonr.gif" border="0" align="middle" width="30" height="30" alt="2"></a>

   {/if}

{elseif $results.MatchCount == 0 && $options.words != ""}

&nbsp;<br><hr noshade size="1">

<p class="PageHd">Search results for '{$options.words}'</p>

<hr noshade size="1">
<b>No results were found.</b>
<hr noshade size="1">

{/if}
