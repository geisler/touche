<?php
#
# Copyright (C) 2002 David Whittington
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/clarification_response_form.php
#
	include_once("lib/config.inc");
	include_once("lib/judge.inc");
	include_once("lib/header.inc");

	$clarification_id = $_GET['clarification_id'];
	$ignore_clarification = $_GET['ignore'];

	//if the clarification_id = 0, then the judge is making a clarification without one being submitted
	if($clarification_id > 0)
	{
		$sql  = "SELECT * FROM CLARIFICATION_REQUESTS ";
		$sql .= "WHERE CLARIFICATION_ID=$clarification_id ";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
	}
	else
	{
		$sql = "SELECT * FROM PROBLEMS ORDER BY PROBLEM_ID";
		$result = mysql_query($sql);
		$numrows = mysql_num_rows($result);
		$row = mysql_fetch_assoc($result);
	}

	echo "<br>\n";
	echo "<form action=respond_to_clarification.php method=post>\n";
	echo "<input type=hidden name=clarification_id value=$clarification_id>\n";
	echo "<table align=center bgcolor=#000000 cellpadding=0 cellspacing=0 border=0 width=60%><tr><td>";
	echo "<table width=100% cellpadding=5 cellspacing=1 border=0>\n";
	echo "<tr bgcolor=\"$hd_bg_color1\">\n";
	echo "<td align=\"center\" colspan=\"2\"><font color=\"$hd_txt_color1\"><b>Request Clarification</b></font></td>\n";
	echo "</tr>\n";
	echo "<tr bgcolor=$hd_bg_color2>\n";
	echo "<td align=center colspan=2><font color=$hd_txt_color2><b>Problem</b></font></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td align=center bgcolor=$data_bg_color1>\n";
	if($clarification_id != 0) {
		if ($row['PROBLEM_ID']!=-1) {
			echo $problems[$row['PROBLEM_ID']]['name']."\n";
		} else {
			echo "		General\n";
		}
	}
	else {
		echo "<select name=problem>\n";
		echo "<option value=-1 selected=selected>General</option>\n";
		for($x = 0; $x < $numrows; $x++) {
			echo "<option value=" . $row['PROBLEM_ID'] . ">" . $row['PROBLEM_NAME'] . "</option>\n";
			$row = mysql_fetch_assoc($result);
		}
		echo "</select>\n";
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr bgcolor=$hd_bg_color2>\n";
	echo "<td align=center colspan=2><font color=$hd_txt_color2><b>Question</b></font></td>\n";
	echo "</tr>\n";
	echo "<tr bgcolor=$data_bg_color1>\n";
	echo "<td align=center><font color=$data_txt_color1>\n";
	echo "$row[QUESTION]\n";
	echo "<br><br>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr bgcolor=$hd_bg_color2>\n";
	echo "<td align=center colspan=2><font color=$hd_txt_color2><b>Response</b></font></td>\n";
	echo "</tr>\n";
	echo "<tr bgcolor=$data_bg_color1>\n";
	echo "<td align=center><font color=$data_txt_color1>\n";

	if($ignore_clarification == "y") {
		echo "<textarea name=response rows=5 cols=70 wrap=virtual>IGNORED</textarea>\n";
	}else{
		echo "<textarea name=response rows=5 cols=70 wrap=virtual></textarea>\n";
	}
	echo "<br><br>\n";
	echo "<select name=\"broadcast\">\n";
	if($clarification_id != 0) {
		if ($row['TEAM_ID'] != -1) {
			echo "<option value=\"0\">Respond to ".$teams[$row['TEAM_ID']]['name']."</option>\n";
		}
		echo "<option value=1>Respond to All</option>\n";
	}

	if($clarification_id == 0) {
                $sql = "SELECT * FROM TEAMS ORDER BY TEAM_ID";
                $result = mysql_query($sql);
                $numrows = mysql_num_rows($result);
		echo "<option value=".($numrows+1).">Respond to All</option>\n";

//WORK ON THIS BUG
//                $row = mysql_fetch_assoc($result);
//		for($x = 0; $x < $numrows; $x++) {
//			echo "<option value='".$x."'>".($x+1)." - ".$row['TEAM_NAME']."</option>\n";
  //              	$row = mysql_fetch_assoc($result);
//		}
	}	
	echo "</select>\n";
	echo "<input name=submit type=submit value=Submit Response>\n";
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</td></tr></table>";
	echo "</form>\n";

	include("lib/footer.inc");
?>
