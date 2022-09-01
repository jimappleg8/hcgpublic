<?php
 # Hans B Pufal (http://www.aconit.org/hbp/CCC/Ewiki/index.php)


$ewiki_plugins["mpi"]["calendar"] = "ewiki_mpi_calendar";


function ewiki_mpi_calendar ($action, $args=array())
{
   if ($action == 'desc')
      return "Display one month calendar";

   if ($action == "doc")
      return "
      The <b>calendar</b> plugin displays a single month calendar.
      Various parameters control the format of the display.
      ";

   if ($action != "html")
      return '<b>ewiki_mpi_calendar</b> cannot do "' . $action . '"\n';

   $now = localtime(time(), 1);

   !isset ($args['year']) && $args['year'] = $now['tm_year'] + 1900;
   !isset ($args['month']) && $args['month'] = $now['tm_mon']+1;
   !isset ($args['month_offset']) && $args['month_offset'] = 0;
   !isset ($args['start_wday']) && $args['start_wday'] = 0;
   !isset ($args['wday_color']) && $args['wday_color'] = '#C0C0C0';
   !isset ($args['today_color']) && $args['today_color'] = '#E0E0E0';
   !isset ($args['wend_color']) && $args['wend_color'] = '#B0B0B0';

   if (isset($args['locale']))
   {
      $oldlocale = setlocale(LC_TIME, 0);
      $args['locale'] = setlocale(LC_TIME, $args['locale']);
   }
   else
      $args['locale'] = false;

   $o = "";

   $month = $args['month'] + $args['month_offset']; // month (1-12)
   while ($month > 12)
   {
      $month -= 12;
      $args['year']++;
   }

   while ($month < 1)
   {
      $month += 12;
      $args['year']--;
   }

   $time = mktime(12, 0, 1,                    // hh, mm, ss,
       $month, // month (1-12)
       1,                                      // mday (1-31)
       $args['year']);

   $t = localtime($time, 1);
   $today = ($now['tm_year'] == $t['tm_year'] && $month == $now['tm_mon']+1) ? $now['tm_mday'] : false;

   $o .= "<table cellspacing=0 cellpadding=2 class=cal>\n<tr>" .
	 "<th colspan=7>" . strftime ('%B %Y', $time) . "</th></tr><tr>\n";

   $col = (7 + $t['tm_wday'] - $args['start_wday']) % 7;
   $time -= $col * 24 * 60 * 60;
   $t     = localtime($time, 1);
   $col = 0;

   for ($i=0; $i < 7; $i++)
      $o .= "<th>" . substr(strftime ('%a', $time + $i * 24 * 60 * 60), 0, 1) . "</th>";

   while ($t['tm_mon']+1 <= $month)
   {
      if (($col++ % 7) == 0)
	 $o .= "</tr>\n<tr align=center>";

      if (($t['tm_mon']+1 == $month) && ($t['tm_mday'] == $today))
      {
	 $o .= '<td bgcolor="' . $args['today_color'] . '"><b>';
	 $e = "</b></td>\n";
      }
      else if (($t['tm_wday'] == 0) || ($t['tm_wday'] == 6))
      {
	 $o .= '<td bgcolor="' . $args['wend_color'] .'">';
	 $e = "</td>\n";
      }
      else
      {
	 $o .= '<td bgcolor="' . $args['wday_color'] .'">';
	 $e = "</td>\n";
      }


      if ($t['tm_mon']+1 == $month)
	 $o .= $t['tm_mday'];

      $o .= $e;

      $time += 24 * 60 * 60;	# SECONDS_PER_DAY;
      $t     = localtime($time, 1);
   }

   if ($col)
      $o .= "<td colspan=" . ((42 - $col) % 7) . "></td></tr>\n";

   $o .= "</table>\n";

   $args['locale'] && setlocale(LC_TIME, $oldlocale);

   return $o;
};

?>