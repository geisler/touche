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
		echo "<div class = 'error'><br>No file selected for submission!</div>";
	}
	
	if ($state == 2) {
		echo "<div class = 'error'><br>You have alread solved this problem!</div>";
	}
	
	if ($state == 3) {
		echo "<div class = 'success'><br>Submission successful. Judging pending.</div>";
	}
	
	if ($state == 4) {
		echo "<div class = 'error'><br>Judging pending on a previous submission, please wait.</div>";
	}
	?>
	
		<div class="container\">
			 <div class="table-responsive">
			 <table class="table" align="left" width=100%>
			 	<tr bgcolor="#CCCCCC">
			 		<td  colspan='2' align='center'>
			 			<h3>Submit a Solution</h3>
			 		</td>
				</tr>
			
			<form method="post" enctype="multipart/form-data" action="submit_solution.php">
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
	echo "</td></tr>\n";
	echo "</div>";
	echo "</div>";
	
	reset($problems);
	$prob_num = 1;
	echo "<table  class='table' align=\"center\" width=100%>";
	echo "<tr><td colspan=2 align='center' bgcolor='#CCCCCC'><h3>Submissions</h3></td></tr>";
	echo "</table>";
	foreach ($problems as $problem) {
		echo "<table  class='table' align=\"center\" width=100%>";
		echo " <tr><td  colspan=\"3\" align='center'>";
		echo "		<b>Problem #$prob_num: ".$problem['name']."</b><br>";
		echo " </td></tr>";
	
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
