<?php
#
# Copyright (C) 2002, 2003 David Whittington
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: clarification_request_form.php
#
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
	    #ls
	    #
	    # put header here so it will still redirect, but it will also
            # insert clarification into the databse, so don't use exit()
	    #
	    header ("Location: clarifications.php?problem_id=-1&success=true");
	}
	include_once("lib/header.inc");
	include_once("lib/config.inc");
	include_once("lib/data.inc");
	include_once("lib/session.inc");

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
	    $question = $_POST['question'];
	    $problem_id = $_POST['problem_id'];

	    $sql  = "INSERT ";
	    $sql .= "INTO CLARIFICATION_REQUESTS ";
	    $sql .= "    (TEAM_ID, PROBLEM_ID, QUESTION, SUBMIT_TS) ";
	    $sql .= " VALUES ";
	    $sql .= "    ('$team_id', '$problem_id', '".mysql_real_escape_string($question)."', '".time()."')";
	    mysql_query($sql);
	}

	echo "<br>\n";
	echo "<form action=clarification_request_form.php method=post>\n";
	echo "<table align=center bgcolor=#000000 cellpadding=0 cellspacing=0 border=0><tr><td>";
	echo "<table width=100% cellpadding=5 cellspacing=1 border=0>\n";
	echo "	<tr bgcolor=\"$hd_bg_color1\">\n";
	echo "		<td align=\"center\" colspan=\"2\"><font color=\"$hd_txt_color1\"><b>Request Clarification</b></font></td>\n";
	echo "	</tr>\n";
	echo "	<tr bgcolor=$hd_bg_color2>\n";
	echo "		<td align=\"center\" colspan=2><font color=\"$hd_txt_color2\"><b>Problem</b></font></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align=\"center\" bgcolor=\"$data_bg_color1\">\n";
	echo "			<font color=\"$data_txt_color1\">\n";
	echo "			<select name=problem_id>\n";
	echo "				<option value=\"-1\">General</option>\n";
	foreach ($problems as $problem) {
		echo "				<option value=\"$problem[id]]\">$problem[name]</option>\n";
	}
	echo "			</select>\n";
	echo "			</font>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "	<tr bgcolor=$hd_bg_color2>\n";
	echo "		<td align=\"center\" colspan=2><font color=\"$hd_txt_color2\"><b>Question</b></font></td>\n";
	echo "	</tr>\n";
	echo "	<tr bgcolor=\"$data_bg_color1\">\n";
	echo "		<td align=center><font color=\"$data_txt_color1\">\n";
	echo "			<textarea name=\"question\" rows=5 cols=70 wrap=virtual></textarea>\n";
	echo "			<br><br>\n";
	echo "			<input name=submit type=submit value=\"Submit Request\">\n";
	echo "		</td>\n";
	echo "	</tr>\n";
	echo "</table>\n";
	echo "</td></tr></table>";
	echo "</form>\n";

	include("lib/footer.inc");
?>
