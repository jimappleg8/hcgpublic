
<p class="PageHd">Search results for '{$results.Words}'</p>

<hr noshade size="1">
<strong>Documents {$results.FirstMatch} - {$results.LastMatch} of {$results.MatchCount} matches.  More <img src="/images/star.gif">'s indicate a better match.</strong>
<hr noshade size="1">

{* set display type *}
{* $dtype = 1 - display percentage match as number *}
{* $dtype = 2 - display images/stars *}
{* $ftrtype = "a" - display image *}
{* $ftrtype = "b" - display numerals as text *} 

{php}
$dtype = 2;
$ftrtype = "a";
$ratingimage = "/images/htdig/rating.gif";

$numstars = $results["Percent"];
$numstars = ($numstars/20);
settype ($numstars, 'integer');

for ($numstars; $numstar>=1; $numstar--) {
print ($ratingimage);
}


{/php}

{section name=match loop=$results.Matches}

<dl><dt><strong><a href="{$results.Matches[match].URL}">{$results.Matches[match].Title}</a></strong>&nbsp;&nbsp;{$results.Matches[match].Percent}
</dt><dd>{$results.Matches[match].Excerpt}<br>
<em><a href="{$results.Matches[match].URL}">{$results.Matches[match].URL}</a></em>
<font size="-1">{$results.Matches[match].Modified}, {$results.Matches[match].Size} bytes</font>
</dd></dl>

{/section}