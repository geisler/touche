<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: submissions.php
#
	include_once("lib/header.inc");
	include_once("lib/config.inc");
	include_once("lib/data.inc");

	$state = $_GET['state'];
	
	if ($state == 1) {
		echo "<center><br><font color=\"#ee0000\">No file selected for submission!</font><br></center>\n";
	}
	
	if ($state == 2) {
		echo "<center><br><font color=\"#ee0000\">You have alread solved this problem!</font><br></center>\n";
	}
	
	if ($state == 3) {
		echo "<center><br><font color=\"#00aa00\">Submission successful. Judging pending.</b></font><br></center>\n";
	}
	
	if ($state == 4) {
		echo "<center><br><font color=\"#ee0000\">Judging pending on a previous submission, please wait.</b></font><br></center>\n";
	}
	?>
	<br><b><center>Submit a Solution</center></b><br>
	<table align="center" bgcolor="#000000" width="90%" cellpadding="0"  cellspacing="0" border="0"><tr><td>
		<table align="center" width="100%" cellpadding="5" cellspacing="1" border="0">
			
			<form method="post" enctype="multipart/form-data" action="submit_solution.php">
			<tr><td align="center" bgcolor="<?echo "$data_bg_color1"?>" colspan="3">
			<table>
				<tr><td align="right">Source File (C, C++, or Java) &nbsp</td>
				<td><input type="file" name="source_file"></td></tr>
				<tr><td align="right">Problem &nbsp</td><td>
				<select name="problem_id">
	<?
	$prob_num = 1;
	foreach ($problems as $problem) {
		echo " <option value=\"$problem[id]\">$prob_num - $problem[name]</option>";
		$prob_num++;
	}
	echo "</select>";
	echo "</td></tr><tr><td colspan=\"2\" align=\"center\">\n";
	echo "<p>\n";
	echo "<input type=\"submit\" value=\"Submit Solution\">\n";
	echo "<input type=\"reset\" value=\"Cancel\">\n";
	echo "</td></tr></table>\n";
	echo "</td></tr>\n";
	echo "</form></table>\n";
	echo "</td></tr></table>\n";
	
	reset($problems);
	$prob_num = 1;
	echo "<br><b><center>Submissions</center></b><br>\n";
	foreach ($problems as $problem) {
		echo "<table align=\"center\" bgcolor=\"#000000\" width=\"90%\" cellpadding=\"0\"  cellspacing=\"0\" border=\"0\"><tr><td>\n";
		echo "<table align=\"center\" width=\"100%\" cellpadding=\"5\" cellspacing=\"1\" border=\"0\">\n";
		echo "	<tr><td bgcolor=\"$hd_bg_color1\" colspan=\"3\">\n";
		echo "		<font color=\"$hd_txt_color1\"><b>Problem #$prob_num: ".$problem['name']."<b><br>\n";
		echo "	</td></tr>\n";
	
		$sql =  "SELECT TS, ATTEMPT, RESPONSE_ID ";
		$sql .= "FROM JUDGED_SUBMISSIONS ";
		$sql .= "WHERE ";
		$sql .= "    PROBLEM_ID='$problem[id]' AND TEAM_ID='$team_id' ";
		$sql .= "ORDER BY ATTEMPT ASC";
		$result = mysql_query($sql);
		echo mysql_error();
		$sql2 =  "SELECT TS, ATTEMPT   ";
		$sql2 .= "FROM QUEUED_SUBMISSIONS ";
		$sql2 .= "WHERE ";
		$sql2 .= "    PROBLEM_ID='$problem[id]' AND TEAM_ID='$team_id' ";
		$sql2 .= "ORDER BY ATTEMPT";
		$result2 = mysql_query($sql2);
		echo mysql_error();
		if(mysql_num_rows($result)||mysql_num_rows($result2)) {
			echo "	<tr bgcolor=\"$hd_bg_color2\">\n";
			echo "		<td align=\"center\" width=\"33%\">Attempt</small></td>\n";
			echo "		<td align=\"center\" width=\"33%\">Submission Time</small></td>\n";
			echo "		<td align=\"center\" width=\"34%\">Result</td>\n";
			echo "	</tr>\n";
			while($row = mysql_fetch_array($result)) {
				// --- START HACK ALERT ---
                                // sb hack - 2007-09-27
                                // Should be ECORRECT, not 9
                                // Was the following line.
                                //if($row['RESPONSE_ID'] == 0) {

                                if($row['RESPONSE_ID'] == 9) {
                                // --- END HACK ALERT ---
					$color = "green";
				}
				else {
					$color = "red";
				}
				echo "<tr bgcolor=\"$data_bg_color1\">\n";
				echo "<td align=\"center\"><font color=\"$color\">".$row['ATTEMPT']."</font></td>\n";
				echo "<td align=\"center\"><font color=\"$color\">".
					date("g:i:s a", $row['TS'])."</font></td>\n";
				echo "<td align=center><font color=\"$color\">";
				echo $responses[$row['RESPONSE_ID']]['response'];
				echo "</font></td>\n";
				echo "</tr>\n";
			}
			while($row = mysql_fetch_array($result2)) {
				$color = "red";
				echo "<tr bgcolor=\"$data_bg_color1\">\n";
				echo "<td align=\"center\"><font color=\"$color\">".$row['ATTEMPT']."</font></td>\n";
				echo "<td align=\"center\"><font color=\"$color\">".
					date("g:i:s a", $row['TS'])."</font></td>\n";
				echo "<td align=center><font color=\"$color\">";
				echo "Queued for judging";
				echo "</font></td>\n";
				echo "</tr>\n";
			}
		}
		echo "</td></tr>\n";
		echo "</table>\n";
		echo "</td></tr></table>\n";
		echo "<br>\n";
		$prob_num++;
	}
	
	include_once("lib/footer.inc");
?>
