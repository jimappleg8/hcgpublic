<html><head>
<title>PointComma Debug Console</title>
<link rel="stylesheet" href="img/debug.css" type="text/css" />
<script language=javascript>
<!--

//Build the information Array: each line represent a different debug entry 

document.arrayDebug = new Array();

//Filter scripts to modify the information displayed 

document.arrayDisplayedLine = new Array();

//Package index 0
//Error Level index 1
//Basic Comment index 2
//advanced Comment index 3
//File index 4
//Line index 5
//trace index 6

{foreach name='fillDebugInfo' from=$arrayDebug key=key item=debugInfo}
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}] = new Array(8);
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][0] ='{$debugInfo.package|escape:javascript}';
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][1] ='{$debugInfo.debugLevel}';
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][2] ="{$debugInfo.comment|escape:javascript}";
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][3] ="{$debugInfo.extracomment|escape:javascript}";
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][4] ="{$debugInfo.file|escape:javascript}";
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][5] ="{$debugInfo.line}";
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][6] ="{$debugInfo.trace|@debug_print_var:"0":"150"|escape:javascript}";
document.arrayDebug[{$smarty.foreach.fillDebugInfo.total-1-$key}][7] ="{$debugInfo.time|date_format:"%b %e %H:%M:%S"}";
document.arrayDisplayedLine[{$smarty.foreach.fillDebugInfo.total-1-$key}]=true;
{/foreach}
//-->
</script>
<script src="img/displaydebuginfo.js"></script>
<!-- 
Manage layer 
// -->
<script src="img/layer.js"></script>
</head><body>
<table border=0 width=100% cellspacing=0 cellpadding=0>
<tr><td colspan=3 class='title'>Pointcomma Debugging Screen<a href="javascript:location.href='debug.php'">Refresh information</td></tr>
<tr><td colspan=3 class='filterTitle'>Filters</td></tr>
<tr class='filterBox'><td width=34% class='filter'>
<label class=label>Package</label>
<select name=packageBox id=packageBox onChange="filterPackage(this.value)">
	{html_options values=$arrayUniquePackage selected='None' output=$arrayUniquePackage}
</select>
</td>
<td width=34% class='filter'>
<label class=label>Error Level</label>
<select name=errorLevelBox id=errorLevelBox onChange="filterError(this.value)">
	{html_options values=$arrayUniqueLevel selected='None' output=$arrayUniqueLevel}
</select>
</td>
<td width=34% class='filter'> 
<label class=label>Search</label>
<input type=text name=searchBox id=searchBox onChange="Search(this.value);"/>
</td></tr>
</table>

<table border=0 width=100% cellspacing=0 cellpadding=0 class='debugBox'>
<tr><td colspan=3 class='debugTitle'>Debug information:</td></tr>
<tr><td width=200px class='debugDesc'>Level Time</td><td width=200px class='debugDesc'>Package</td><td class='debugDesc'>Error</td></tr>
</table>
	<script language=javascript>
		displayDebugInfo(document.arrayDebug,document.boolFilter,document.arrayDisplayedLine);
	</script>
</body></html>
