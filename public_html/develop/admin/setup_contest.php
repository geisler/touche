<?
#
# Copyright (C) 2003 David Whittington
# Copyright (C) 2003, 2004 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/setup_contest.php
#

	ob_start();
	include("lib/admin_config.inc");
	include("lib/data.inc");
	include("lib/session.inc");
	include("lib/header.inc");
	
	$link = mysql_connect($db_host, $db_user, $db_pass);
	if(!$link){
		print "Sorry.  Database connect failed.  Check your internet connection.";
		exit;
	}
	$connect_good = mysql_select_db($db_name);
	if (!$connect_good) {
		print "Sorry.  Couldn't select the database name $db_name. Exiting...";
		exit;
	}

	$sql = mysql_query("SELECT * FROM SITE WHERE SITE_ID = 1");
	if (!$sql) {
		print "Could not tell if a contest has been created.";
		exit;
		#die or break
	}
	if (mysql_num_rows($sql) > 0) {
	//a contest is already set up!  allow user to edit
		$contest=true;
		$row = mysql_fetch_assoc($sql);
		echo "<center>\n";
	
		# Print out any errors
		if(isset($error)) {
		    echo "<br>";
		    foreach($error as $er) {
			echo "<b>$er</b>";
		    }
		}
		
		echo "</center>";
		echo " <div class=\"container\">";
		echo " <form method=POST action=setup_contest.php>\n";
		echo " <div class=\"table-responsive\">";
		echo " <table class=\"table\" align=\"left\" width=90%>";
		echo " <td align='right'><h3>Edit Contest Info</h3></td>";
		$host = $row['CONTEST_HOST'];
		$contest_name = $row['CONTEST_NAME'];
		$today_month  = date('m', $contest_start_ts);
		$today_day    = date('d', $contest_start_ts);
		$today_year   = date('Y', $contest_start_ts);
		$today_hour   = date('H', $contest_start_ts);
		$today_minute = date('i', $contest_start_ts);
		//calculating the number of seconds since January 1 1970 at midnight
		//for our particular freeze/contest end values in seconds
		$freeze_hour = gmdate('H', $contest_freeze_time);
		$freeze_minute = gmdate('i', $contest_freeze_time);
		$freeze_second = gmdate('s', $contest_freeze_time);
		$end_hour = gmdate('H', $contest_end_time);
		$end_minute = gmdate('i', $contest_end_time);
		$end_second = gmdate('s', $contest_end_time);
		$username = $row['JUDGE_USER'];
		$password = $row['JUDGE_PASS'];
		$base_directory = $row['BASE_DIRECTORY'];
		$num_problems = $row['NUM_PROBLEMS'];
		$time_penalty = $row['TIME_PENALTY'];
		if($row['TEAM_SHOW'] == 1)
			$team_show = "checked";
		else
			$team_show = "";
		
		if ($row['IGNORE_STDERR'] == true) {
			$stderr_checked = "checked";
		}
		else {
			$stderr_checked = "";
		}
		$language_specifics = mysql_query("SELECT * FROM LANGUAGE");
		if (!$language_specifics) {
			echo "Could not find language specific info<br />";
			echo "Please contact an administrator.";
		}
		while ($lang_row = mysql_fetch_assoc($language_specifics)) {
			if ($lang_row['LANGUAGE_NAME'] == 'C') {
				$headers_c_checked = $lang_row['REPLACE_HEADERS'];
				$forbidden_c_checked = $lang_row['CHECK_BAD_WORDS'];
				if ($headers_c_checked) {
					$headers_c_checked = "checked";
				}
				if ($forbidden_c_checked) {
					$forbidden_c_checked = "checked";
				}
			}
			elseif ($lang_row['LANGUAGE_NAME'] == "CXX") {
				$headers_cpp_checked = $lang_row['REPLACE_HEADERS'];
				$forbidden_cpp_checked = $lang_row['CHECK_BAD_WORDS'];
				if ($headers_cpp_checked) {
					$headers_cpp_checked = "checked";
				}
				if ($forbidden_cpp_checked) {
					$forbidden_cpp_checked = "checked";
				}
			}
			elseif ($lang_row['LANGUAGE_NAME'] == "JAVA") {
				$headers_java_checked = $lang_row['REPLACE_HEADERS'];
				$forbidden_java_checked = $lang_row['CHECK_BAD_WORDS'];
				if ($headers_java_checked) {
					$headers_java_checked = "checked";
				}
				if ($forbidden_java_checked) {
					$forbidden_java_checked = "checked";
				}
			}
		}
	}
	else {
		$contest=false;
		echo "<center>\n";
		echo "<b>Set up a Contest</b><br><br>\n";
		echo "</center>\n";
		echo "<form method=POST action=setup_contest.php>\n";
		echo "<p>";
		echo "<table><tr><td>";
		echo "<table>\n";
		echo "<tr>\n";
		echo "<td align=\"center\" colspan=\"2\">";
		echo "<b>Contest Info</b></td></tr>\n";
		$host = "";
		$contest_name = "";
		$today_month  = date('m');
		$today_day    = date('d');
		$today_year   = date('Y');
		$today_hour   = date('H');
		$today_minute = date('i');
		$freeze_hour = "04";
		$freeze_minute = "00";
		$freeze_second = "00";
		$end_hour = "05";
		$end_minute = "00";
		$end_second = "00";
		$base_directory = "";
		$stderr_checked = "";
		$forbidden_c_checked = "";
		$forbidden_cpp_checked = "";
		$forbidden_java_checked = "";
		$headers_c_checked = "";
		$headers_cpp_checked = "";
		$headers_java_checked = "";
		$num_problems = 8;
		$username = "judge";
		$password = "";
	}


//let's prompt for some content

	echo "		<tr>";
	echo "			<td align='right'>Name of the contest <b>host</b>:</td>";
	echo "			<td><input type=\"text\" class='form-control' name=\"contest_host\" ";
	echo "				size=\"30\" value=\"$host\">";
	echo "				</input></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>The contest's <b>name</b>:</td>";
	echo "			<td><input type=\"text\" class='form-control' name=\"contest_name\" ";
	echo "				size=\"30\" value=\"$contest_name\">";
	echo "				</input></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Amount of time (HH:mm:ss) until the standings are frozen:</td> ";
	echo "			<td><input type=\"text\" name=\"freeze_hour\" ";
	echo "				size=\"2\" maxlength=2 value=\"$freeze_hour\"></input>:";
	echo "			<input type=\"text\"  name=\"freeze_minute\" ";
	echo "				size=\"2\" maxlength=2 value=\"$freeze_minute\"></input>:";
	echo "			<input type=\"text\" name=\"freeze_second\" ";
	echo "				size=\"2\" maxlength=2 value=\"$freeze_second\"></input></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Duration of the contest (HH:mm:ss)</td> ";
	echo "			<td><input type=\"text\" name=\"end_hour\" size=\"2\"";
	echo "				maxlength=2 value=\"$end_hour\"></input>:";
	echo "			<input type=\"text\" name=\"end_minute\" size=\"2\"";
	echo "				maxlength=2 value=\"$end_minute\"></input>:";
	echo "			<input type=\"text\" name=\"end_second\" size=\"2\"";
	echo "				maxlength=2 value=\"$end_second\"></input></td> ";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Base directory of the contest information (ex: ";
	echo "			/usr/home/contest):</td> ";
	echo "			<td><input type=\"text\" class='form-control' name=\"base_directory\" ";
	echo "				size=\"30\" value=\"$base_directory\"></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Username for all the judges to use:</td> ";
	echo "			<td><input type=\"text\" class='form-control' name=\"username\" ";
	echo "				size=\"30\" value=\"$username\"></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Password for the judge account:</td> ";
	echo "			<td><input type=password name=\"password\" ";
	echo "				size=\"30\" class='form-control' value=\"$password\"></td>";
	echo "		</tr>";
	
	echo "		<tr>";
	echo "			<td align='right'>Penalty for incorrect submission (in minutes):</td> ";
	echo "			<td><input type=text name=\"time_penalty\" ";
	echo "				size=\"30\" class='form-control' value=\"$time_penalty\"></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td colspan=2 align='center'><h3>Customize the judging experience</h3></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Ignore standard error?</td>";
	echo "			<td><input type=checkbox name=stderr $stderr_checked >";
	echo "				</input>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Check for forbidden words (C, C++, Java)?</td>";
	echo "			<td> C: &nbsp";
	echo "			<input type=checkbox name=forbidden_c $forbidden_c_checked >";
	echo "				</input> &nbsp C++: &nbsp";
	echo "			<input type=checkbox name=forbidden_cpp $forbidden_cpp_checked >";
	echo "				</input> &nbsp Java: &nbsp";
	echo "			<input type=checkbox name=forbidden_java $forbidden_java_checked >";
	echo "				</input></td>";
	echo "		</tr>";

	echo "		<tr>";
	echo "			<td align='right'>Automatically include standard headers";
	echo "				(C, C++, Java)?</td>";
	echo "			<td> C: &nbsp";
	echo "			<input type=checkbox name=headers_c $headers_c_checked >";
	echo "				</input> &nbsp C++: &nbsp";
	echo "			<input type=checkbox name=headers_cpp $headers_cpp_checked >";
	echo "				</input> &nbsp Java: &nbsp";
	echo "			<input type=checkbox name=headers_java $headers_java_checked >";
	echo "				</input></td>";
	echo "		</tr>";

        echo "		<tr>";
        echo "                 <td align='right'>Display team names to judges?</td>";
        echo "                  <td><input type=checkbox name=team_show $team_show >";
        echo "                          </input>";
        echo "          </tr>";

	echo "		<tr>";


	echo "<td></td><td colspan=1 align='left'><button type=\"submit\" class=\"btn btn-default\" name=\"B1\">Submit</button></td>";
	echo "		</tr>";
	echo "	</table>";
	echo "	</form>";

		include("lib/footer.inc");
?>


<?php
if ($_POST)
{
	$failed = false;
	$host_name = $_POST['contest_host'];
	$contest_name = $_POST['contest_name'];
	$contest_month = $_POST['contest_month'];
	$contest_day = $_POST['contest_day'];
	$contest_year = $_POST['contest_year'];
	$freeze_hour = $_POST['freeze_hour'];
	$freeze_minute = $_POST['freeze_minute'];
	$freeze_second = $_POST['freeze_second'];
	$end_hour = $_POST['end_hour'];
	$end_minute = $_POST['end_minute'];
	$end_second = $_POST['end_second'];
	$time_penalty = $_POST['time_penalty'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$base_directory = $_POST['base_directory'];
	
	
	//if the three checkboxes are not checked, they are submitted
	//as undefined/not set.  Therefore, I used the isset function
	//exclusively to decide if they wanted these three options
	if (isset($_POST['team_show'])) {
		$show_team_names = 1;
	}
	else{
		$show_team_names = 0;
	}
	if (isset($_POST['stderr'])) {
		$ignore_stderr = 1;
	}
	else {
		$ignore_stderr = 0;
	}
	if (isset($_POST['forbidden_c'])) {
		$forbidden_c = 1;
	}
	else {
		$forbidden_c = 0;
	}
	if (isset($_POST['forbidden_cpp'])) {
		$forbidden_cpp = 1;
	}
	else {
		$forbidden_cpp = 0;
	}
	if (isset($_POST['forbidden_java'])) {
		$forbidden_java = 1;
	}
	else {
		$forbidden_java = 0;
	}
	if (isset($_POST['headers_c'])) {
		$headers_c = 1;
	}
	else {
		$headers_c = 0;
	}
	if (isset($_POST['headers_cpp'])) {
		$headers_cpp = 1;
	}
	else {
		$headers_cpp = 0;
	}
	if (isset($_POST['headers_java'])) {
		$headers_java = 1;
	}
	else {
		$headers_java = 0;
	}
	$num_problems = $_POST['num_problems'];
	
	$i = 0;
	if ( !$host_name ) {
		$error[$i] = "You forgot to give a contest host name.<br>";
		$i++;
		$failed=true;
	}
	if ( !$contest_name ) {
		$error[$i] = "You forgot to give the contest a name.<br>";
		$i++;
		$failed=true;
	}
	if ( !$freeze_hour ) {
		$error[$i] = "You forgot to give the contest a freeze hour.<br>";
		$i++;
		$failed=true;
	}
	if ( !$end_hour ) {
		$error[$i] = "You forgot to give the contest an end hour.<br>";
		$i++;
		$failed=true;
	}
/*	if (!date_validate($contest_month, $contest_day, $contest_year)) {
		$failed=true;
	}
	if (!time_validate($freeze_hour, $freeze_minute, $freeze_second)) {
		$failed=true;
	}
	if (!time_validate($end_hour, $end_minute, $end_second)) {
		$failed=true;
	}*/

	if ( !$base_directory ) {
		$error[$i] = "You forgot to give the contest a base directory.<br>";
		$i++;
		$failed=true;
	}
	if($failed){
		foreach($error as $err){
			echo "<br>$err\n";
		}
		exit;
	}

	$contest_exists = mysql_query("SELECT * FROM SITE WHERE SITE_ID = 1");
#		echo mysql_num_rows($contest_exists);
	$save_ts = 0;
	$save_hs = 0;
	$save_start = 0;
	
	$contest_date = $contest_year.'-'.$contest_month.'-'.$contest_day;
	$freeze_delay = $freeze_hour*3600 + $freeze_minute*60 + $freeze_second;
	$contest_delay = $end_hour*3600 + $end_minute*60 + $end_second;
	
	if (mysql_num_rows($contest_exists) > 0) {
		$row = mysql_fetch_assoc($contest_exists);
		$save_ts = $row['START_TS'];
		$save_hs = $row['HAS_STARTED'];
		$save_start = $row['START_TIME'];
		$sql = "UPDATE SITE SET CONTEST_HOST = '$host_name', CONTEST_NAME = '$contest_name', NUM_PROBLEMS = '$num_problems', ";
		$sql.= "CONTEST_DATE = '$contest_date', START_TIME = '$save_start', FREEZE_DELAY = '$freeze_delay', CONTEST_END_DELAY = '$contest_delay', ";
		$sql.= "BASE_DIRECTORY = '$base_directory', IGNORE_STDERR = '$ignore_stderr', JUDGE_USER = '$username', JUDGE_PASS = '$password', TEAM_SHOW = '$show_team_names', ";
		$sql.= "START_TS = '$save_ts', HAS_STARTED = '$save_hs', TIME_PENALTY = '$time_penalty'";
		$sql.= "WHERE SITE_ID = 1";
		$success = mysql_query($sql);
	}
	//whether or not a contest was there, it should be deleted now
	//and we can go ahead and create it.
	
	else{
		$sql = "INSERT INTO SITE (CONTEST_HOST, CONTEST_NAME, NUM_PROBLEMS, ";
		$sql.= "CONTEST_DATE, START_TIME, FREEZE_DELAY, CONTEST_END_DELAY, ";
		$sql.= "BASE_DIRECTORY, IGNORE_STDERR, JUDGE_USER, JUDGE_PASS, TEAM_SHOW, START_TS, HAS_STARTED, TIME_PENALTY) ";
		$sql.= "VALUES ('$host_name', '$contest_name', '$num_problems', '$contest_date', ";
		$sql.= "	     '$save_start', '$freeze_delay', '$contest_delay', ";
		$sql.= "	     '$base_directory', '$ignore_stderr', '$username', '$password', '$show_team_names', '$save_ts', '$save_hs', '$time_penalty')";
		$success = mysql_query($sql);
	}
	
	if ($success) {
		if ($forbidden_c == 1 || $forbidden_cpp == 1 || $forbidden_java == 1) {
			$forbidden = true;
		}
		else {
			$forbidden = false;
		}
		if ($headers_c == 1 || $headers_cpp == 1 || $headers_java == 1) {
			$headers = true;
		}
		else {
			$headers = false;
		}
		$insert_sql_c = "UPDATE LANGUAGE ";
		$insert_sql_c.= "SET 	REPLACE_HEADERS = '$headers_c',";
		$insert_sql_c.= "	CHECK_BAD_WORDS = '$forbidden_c' ";
		$insert_sql_c.= "WHERE LANGUAGE_NAME = 'C'";
		$insert_sql_cpp = "UPDATE LANGUAGE SET REPLACE_HEADERS = '$headers_cpp',";
		$insert_sql_cpp.= "			CHECK_BAD_WORDS = '$forbidden_cpp' ";
		$insert_sql_cpp.= "WHERE LANGUAGE_NAME = 'CXX'";
		$insert_sql_java = "UPDATE LANGUAGE SET REPLACE_HEADERS = '$headers_java',";
		$insert_sql_java.= "			CHECK_BAD_WORDS = '$forbidden_java' ";
		$insert_sql_java.= "WHERE LANGUAGE_NAME = 'JAVA'";
		$insert_c_success = mysql_query($insert_sql_c);
		$insert_cpp_success = mysql_query($insert_sql_cpp);
		$insert_java_success= mysql_query($insert_sql_java);
		if (!$insert_c_success || !$insert_cpp_success || !$insert_java_success) {
			echo "<div class='error'><br>Error!  Couldn't update the language sets<br />";
			echo "Please contact an administrator.</div>";
		}
		else {
			header ("Location: setup_problems.php");
			echo "<div class='success'><br>Submission succeeded.</div>";
		}
	}
	else {
		echo "<font color=\"#ff0000\"> Contest creation failed.
		Please contact administrator.";
	}
}
/*******************************************************
End of POST section
*******************************************************/
?>
