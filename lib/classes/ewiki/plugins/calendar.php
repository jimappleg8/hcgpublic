<?php


/**
* this plugin is in european date format only at the moment.
*
* returns a calender for the current / every page,
* example:
*
*	<!php
*		#-- plugin, CSS
*		include("plugins/calendar.php");
*		include("fragments/calendar.css");
*
*		#-- show page
*		echo ewiki_page();
*
*		#-- show calendar
*		if (calendar_exists()) {
*			echo calendar();
*		}
*	!>
*
* this plugin was
* contributed_by("Carsten Senf <ewiki@csenf.de>");
* and en_ localization was added by Andy Fundinger Andy@burgiss.com
*
*/ 

$ewiki_t["en"]["MONTH01"] = "January";
$ewiki_t["en"]["MONTH02"] = "February";
$ewiki_t["en"]["MONTH03"] = "March";
$ewiki_t["en"]["MONTH04"] = "April";
$ewiki_t["en"]["MONTH05"] = "May";
$ewiki_t["en"]["MONTH06"] = "June";
$ewiki_t["en"]["MONTH07"] = "July";
$ewiki_t["en"]["MONTH08"] = "August";
$ewiki_t["en"]["MONTH09"] = "September";
$ewiki_t["en"]["MONTH10"] = "October";
$ewiki_t["en"]["MONTH11"] = "November";
$ewiki_t["en"]["MONTH12"] = "December";

$ewiki_t["de"]["MONTH01"] = "Januar";
$ewiki_t["de"]["MONTH02"] = "Februar";
$ewiki_t["de"]["MONTH03"] = "März";
$ewiki_t["de"]["MONTH05"] = "Mai";
$ewiki_t["de"]["MONTH06"] = "Juni";
$ewiki_t["de"]["MONTH07"] = "Juli";
$ewiki_t["de"]["MONTH10"] = "Oktober";
$ewiki_t["de"]["MONTH12"] = "Dezember";

$ewiki_t["en"]["DAYABBRV01"] = "Mo";
$ewiki_t["en"]["DAYABBRV02"] = "Tu";
$ewiki_t["en"]["DAYABBRV03"] = "We";
$ewiki_t["en"]["DAYABBRV04"] = "Tr";
$ewiki_t["en"]["DAYABBRV05"] = "Fr";
$ewiki_t["en"]["DAYABBRV06"] = "Sa";
$ewiki_t["en"]["DAYABBRV07"] = "Su";		

$ewiki_t["de"]["DAYABBRV02"] = "Di";
$ewiki_t["de"]["DAYABBRV03"] = "Mi";
$ewiki_t["de"]["DAYABBRV04"] = "Do";
$ewiki_t["de"]["DAYABBRV07"] = "So";	

$ewiki_t["en"]["CALENDERFOR"] = "Calender for";		
$ewiki_t["de"]["CALENDERFOR"] = "Kalender für";

define("EWIKI_ACTION_CALENDAR", "calendar");
define("EWIKI_PAGE_CALENDAR", "PageCalendar");
define("EWIKI_PAGE_YEAR_CALENDAR", "PageYearCalendar");

$ewiki_plugins["page"][EWIKI_PAGE_CALENDAR] = "ewiki_page_calendar";
$ewiki_plugins["page"][EWIKI_PAGE_YEAR_CALENDAR] = "ewiki_page_year_calendar";
$ewiki_plugins["action"][EWIKI_ACTION_CALENDAR] = "ewiki_page_calendar";
$ewiki_plugins["action_links"]["view"][EWIKI_ACTION_CALENDAR] = EWIKI_PAGE_CALENDAR;
 


function calendar() {
        ($id = $GLOBALS["ewiki_id"]) or ($id = EWIKI_PAGE_CALENDAR);
	return(ewiki_page_calendar($id, array("id"=>$id)));
}


function calendar_exists($always=false) {
        $id = $GLOBALS["ewiki_id"];
		
        $result = $always || ($id)
                  && ($result = ewiki_database("SEARCH", array("id" => $id.'_')))
                  && ($result->count());
	return( ($id) && ($id != EWIKI_PAGE_CALENDAR) && ($id != EWIKI_PAGE_YEAR_CALENDAR)
                && empty($_REQUEST["year"]) && $result );
}


function ewiki_page_calendar($id, $data=0) {

	if ($_REQUEST["year"]) {
		return(ewiki_page_year_calendar($id, $data));
	}
	else {
		return(renderCalendar($id, TRUE));
	}
}



function ewiki_page_year_calendar($id, $data=0) {

	($year = $_REQUEST['year']) or ($year = date("Y"));
	($pgname = $_REQUEST['pgname']) or ($pgname = $id);
	
	$prev = $year-1;
	$next = $year+1;
		
	$html = '<h2>'.ewiki_t("CALENDERFOR").' <a href="'.ewiki_script("",$pgname).'">'.$pgname."</a> - ".$year.'</h2><center><table cellpadding=\"10\">'."\n";
	
	for($i=1; $i<12; $i+=4) {
		$html .= "<tr>\n";
		for($month=$i; $month<$i+4; $month++) {
			$html .= "<td valign=\"top\">\n" . RenderCalendar($pgname, FALSE, $year, $month) . "\n</td>\n";
		}
		$html .= "</tr>\n";
	}
	
	$html .= "<tr>
		<td align=\"left\" valign=\"bottom\">".
		'<a href="'.ewiki_script(EWIKI_ACTION_CALENDAR,$pgname,'year='.$prev).'">'.
		"&lt; $prev</a>
		</td>
		<td align=\"center\" colspan=\"2\"></td>
		<td align=\"right\" valign=\"bottom\">".
		'<a href="'.ewiki_script(EWIKI_ACTION_CALENDAR,$pgname,'year='.$next).'">'.
		"$next &gt;</a>
		</td>
		";
	$html .= "</table></center>";
	return $html;
}



function renderCalendar($pgname, $more, $year = NULL, $month = NULL) {
	
	if (preg_match("/^(.*?)_\d{8}$/", $pgname, $match)) {
		$pgname = $match[1];
	}
	
	$MonthFull["01"] = ewiki_t("MONTH01");
	$MonthFull["02"] = ewiki_t("MONTH02");
	$MonthFull["03"] = ewiki_t("MONTH03");
	$MonthFull["04"] = ewiki_t("MONTH04");
	$MonthFull["05"] = ewiki_t("MONTH05");
	$MonthFull["06"] = ewiki_t("MONTH06");
	$MonthFull["07"] = ewiki_t("MONTH07");
	$MonthFull["08"] = ewiki_t("MONTH08");
	$MonthFull["09"] = ewiki_t("MONTH09");
	$MonthFull["10"] = ewiki_t("MONTH10");
	$MonthFull["11"] = ewiki_t("MONTH11");
	$MonthFull["12"] = ewiki_t("MONTH12");
		
	if (!isset($year)) {
		$year = date("Y");
	}
	if (!isset($month)) {
		$month = date("n");
	}
	
	$shift = 0;
	$today_ts = mktime(0,0,0,$month,date("d"),$year); // non relative date
	$firstday_month_ts = mktime(0,0,0,$month,1,$year); // first day of the month
	$lastday_month_ts = mktime(0,0,0,$month+1,0,$year);    // last day of the month
	
	$numYear = date("Y",$firstday_month_ts);
	$numMonth = date("m",$firstday_month_ts);
	$textMonth = $MonthFull[(date("m",$firstday_month_ts))];
	$daysInMonth = date("t",$firstday_month_ts);
	
	$dayMonth_start = date("w",$firstday_month_ts);
	if ($dayMonth_start==0) { $dayMonth_start=7;} 
	
	
	$dayMonth_end = date("w",$lastday_month_ts);
	if ($dayMonth_end==0) { $dayMonth_end=7; }
	
	$ret = "";
	
	$ret .=  "<table class=\"caltable\" cellpadding=\"2\" cellspacing=\"1\">\n";
	$ret .=  "<tr><td class=\"calhead\" colspan=\"7\">";
	if ($more) {
		$ret.='<a href="'.ewiki_script(EWIKI_ACTION_CALENDAR,$pgname,'year='.$year).'">';
	}
	$ret .= $textMonth."&nbsp;&nbsp;".$numYear;
	if ($more) {
		$ret .= "</a>";
	}
	$ret .= "</td></tr>\n";
	$ret .=  "<tr>\n";

	
	$ret .=  "<th class=\"caldays\">".ewiki_t("DAYABBRV01").
		"</th><th class=\"caldays\">".ewiki_t("DAYABBRV02").
		"</th><th class=\"caldays\">".ewiki_t("DAYABBRV03").
		"</th><th class=\"caldays\">".ewiki_t("DAYABBRV04").
		"</th><th class=\"caldays\">".ewiki_t("DAYABBRV05").
		"</th><th class=\"caldays\">".ewiki_t("DAYABBRV06").
		"</th><th class=\"caldays\">".ewiki_t("DAYABBRV07").
		"</th></tr>\n";
	$ret .=  "<tr>\n";
	
	
	for ($k=1; $k<$dayMonth_start; $k++) {$ret .=  "<td>&nbsp;</td>\n";}

	#-- pre-scan for calendar day pages
	$f = array();
	for ($i=1; $i<=$daysInMonth; $i++) {
		$f[] = $pgname.'_'.$numYear.$numMonth.(strlen($i)<2 ? "0$i" : "$i");
	}
	$f = ewiki_database("FIND", $f);
	
	for ($i=1; $i<=$daysInMonth; $i++) {
		$day_i_ts=mktime(0,0,0,date("n",$firstday_month_ts),$i,date("Y",$firstday_month_ts));
		$day_i = date("w",$day_i_ts);
		
		if ($day_i==0) { $day_i=7;}

		$page = $pgname.'_'.$numYear.$numMonth. (strlen($i)<2 ? "0$i" : "$i");

		$link_i = '<a href="'.ewiki_script("",$page).'"'
			. ($f[$page]
				? ' class="calpg"><b>' . $i . '</b>'
				: ' class="calhide">' . $i )
			. "</a>";
	
		
		if ($month==date("n") && $today_ts==$day_i_ts) {
			$ret .=  "<td class=\"caltoday\">".$link_i."</td>";
		} 
		else {
			$ret .=  "<td class=\"calday\">".$link_i."</td>\n";
		}
		if ($day_i==7 && $i<$daysInMonth) {
				$ret .=  "</tr><tr>\n"; 
		} 
		else if ($day_i==7 && $i==$daysInMonth) {
				$ret .=  "</tr>\n";
		}
		else if ($i==$daysInMonth) {
				for ($h=$dayMonth_end; $h<7; $h++) { 
				$ret .=  "<td>&nbsp;</td>\n";
				 }
			$ret .=  "</tr>\n";
		}
	}
	$ret .=  "</table>\n";
	return $ret;
}



?>