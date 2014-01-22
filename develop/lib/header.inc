<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2003 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: lib/header.inc
#
    include_once ("lib/session.inc");
    include_once ("lib/config.inc");
    include_once ("lib/data.inc");

?>

<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">
<html>
<body bgcolor="<?=$page_bg_color?>" link="0000cc" alink="000066" vlink="0000cc">
<table width="90%" align="center" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
	<tr><td>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr bgcolor="<?=$title_bg_color?>">	
				<td>
                <!-- Beautification hack. 2006-09-25 -sb -->
                <img src="images/favicon.ico">

  				<font color="#ffffff">
  				<b><?=$contest_name?></b> - <small><?=$contest_host?></small>
  				</font>
				</td>
				<td align="right">
 					 <font color="#ffffff">
 					 <b><?=$team_name?></b>
 					 </font>
				</td>
			</tr>
			<tr>	
				<td bgcolor="#dcdcdc" align="left"><b>
				<a href="problems.php">Problems</a> &nbsp;
  				<a href="submissions.php?state=0">Submissions</a> &nbsp;  
  				<a href="standings.php">Standings</a> &nbsp;  
  				<a href="clarifications.php?problem_id=-1">Clarifications</a> &nbsp;
  				<!--<a href="testcompile.php">-->Test Compile (N/A)<!--</a>--> &nbsp;
				<a href="readme.php">ReadMe</a></b>
				</td>
				<td align="right" bgcolor="#dcdcdc">
				<?
				/*
				 * the following lines find out how far
				 * away the freezing of the standings is
				 * and how many seconds the end of the contest is.
				 * Then, it stores these values for use below.
				 * Date was used previously but needed to be adjusted
				 * for the time zone.  gmdate does this automatically;
				 * therefore, a much better solution.
				 */
				$end_diff = $contest_end_ts + $site_start_offset - time();
				$freeze_diff = $contest_freeze_ts + $site_start_offset - time();
				if(!$site_started)
				{
					print "</td></tr><tr><td colspan=2 bgcolor=red><center><h2>Contest has not yet started</h2></center></td></tr>";
					include_once "footer.inc";
					die;
				}
				if($end_diff <0)
				{
					print "</td></tr><tr><td colspan=2 bgcolor=green><center><h2>Contest is over</h2></center></td></tr>";
					include_once "footer.inc";
					die;
				}
				$time_till_contest_over
				    = gmdate('H:i:s', $end_diff);
				$time_till_freeze_starts
				    = gmdate('H:i:s', $freeze_diff);
				$time_till_contest_starts
				    = gmdate('H:i:s', $site_start_offset + $contest_start_ts - time());
				print "<b>Official Time: " . date("H:i:s") . "</b>";

				//1200 = 20 minutes left
				//600 = 10 minutes left
				//300 = 5 minutes left
				if ($freeze_diff > 1200) {
					$freeze_color = "#000000";
				}
				elseif ($freeze_diff > 600) {
					$freeze_color = "#00aa00";
				}
				elseif ($freeze_diff > 300) {
					$freeze_color = "#ff8200";
				}
				elseif ($freeze_diff > 0) {
					$freeze_color = "#ff0000";
				}
				else {
					print "<br />";
					print "Standings frozen.";
				}

				if ($end_diff > 1200){
					$end_color = "#000000";
				}
				elseif ($end_diff > 600){
					$end_color = "#0000ff";
				}
				elseif ($end_diff > 0){
					$end_color = "#ff0000";
				}
				else {
					$end_color = "#000000";
					print "<br />";
					print "Contest over.";
				}

				if ($contest_start_ts > Time() ) {
					print "<font color = \"#000000\">";
					print "<br />";
					print "Contest starts in: ";
					print $time_till_contest_starts;
					print "</font>";
				}
				elseif ($freeze_diff > 0) {
					print "<font color = \"$freeze_color\">";
					print "<br />";
					print "Time left until freeze begins: ";
					print $time_till_freeze_starts;
					print "</font>";
				}
				elseif ($end_diff > 0) {
					print "<font color = \"$end_color\">";
					print "<br />";
					if ($end_diff < 300) {
						print "WARNING!! ";
					}
					print "Time until Contest ends: ";
					print $time_till_contest_over;
					print "</font>";
				}
				?>
				<font color = "#000000">
				</td>
			</tr>
			<tr>
				<td bgcolor="#ffffff" colspan="2">
