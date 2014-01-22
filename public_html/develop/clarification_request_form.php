<?php
//
// Copyright (C) 2002, 2003 David Whittington
//
// See the file "COPYING" for further information about the copyright
// and warranty status of this work.
//
// arch-tag: clarification_request_form.php
//
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
	    //ls
	    //
	    // put header here so it will still redirect, but it will also
            // insert clarification into the databse, so don't use exit()
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
		if(!$result)
		{
			$error_msg = "<div class = 'error'><br>Error: " . mysql_error();
			$error_msg .= "<br>SQL: $sql</div>";
		}
		else
		{
			$error_msg = "<div class = 'success'>Request successful.</div>";
		}
	}

	echo "<br>\n";
	echo "<form action=clarification_request_form.php method=post>\n";
	echo "<table class='table' align=center><tr><td>";
	echo "<table width=100% cellpadding=5 cellspacing=1 border=0>\n";
	echo "	<tr>\n";
	echo "		<td align=\"center\" colspan=\"2\"><h3>Request Clarification</h3></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align=\"center\" colspan=2><b>Problem or General Clarification?</b></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align=\"center\">\n";
	echo "			<select name=problem_id>\n";
	echo "				<option value=\"-1\">General</option>\n";
	foreach ($problems as $problem) {
		echo "				<option value=\"$problem[id]]\">$problem[name]</option>\n";
	}
	echo "			</select>\n";
	echo "		</td>\n";
	echo "	</tr>\n";
			if($error_msg)
			{
				echo $error_msg;
			}
	echo "	<tr>\n";
	echo "		<td align=\"center\" colspan=2><b>Write Question Below</b></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td align=center>\n";
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
