<?
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2004 Jonathan Geisler
# Copyright (C) 2005 David Crim
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: submit-solution.php
#
	include_once('lib/config.inc');
	include_once('lib/data.inc');
	include_once('lib/session.inc');

	$problem_id = $_POST['problem_id'];

	$link = mysql_connect ($db_host, $db_user, $db_pass) or die ("Could not connect to database");
	mysql_select_db ($db_name) or die ("Could not select database");

	// check to see if a file is actually being submitted
	if ($_FILES[source_file][size] == 0) {
		header("location: submissions.php?state=1");
		exit(0);
	}

	//check if the contest has ended, if it has, then don't let them submit
	if($contest_end_ts < time()) {
		header("location: submissions.php?state=5");
		exit(0);
	}
	
	// check to see if we already have a successful submission 
	$sql  = "SELECT * FROM JUDGED_SUBMISSIONS ";
	$sql .= "WHERE TEAM_ID='$team_id' AND PROBLEM_ID='$problem_id' AND RESPONSE_ID='9' ";
	$result = mysql_query($sql);
	echo mysql_error();
	if (mysql_num_rows($result)>0) {
		header("location: submissions.php?state=2");
		exit(0);
	}
        $sql  = "SELECT * FROM JUDGED_SUBMISSIONS ";
        $sql .= "WHERE TEAM_ID='$team_id' AND PROBLEM_ID='$problem_id' AND RESPONSE_ID='0' ";
        $result = mysql_query($sql);
        echo mysql_error();
        if (mysql_num_rows($result)>0) {
                header("location: submissions.php?state=4");
                exit(0);
        }
	$sql  = "SELECT * FROM QUEUED_SUBMISSIONS ";
	$sql .= "WHERE TEAM_ID='$team_id' AND PROBLEM_ID='$problem_id'";
	$result = mysql_query($sql);
	echo mysql_error();
	if (mysql_num_rows($result)>0) {
		header("location: submissions.php?state=4");
		exit(0);
	}

	$sql  = "SELECT ATTEMPT ";
	$sql .= "FROM JUDGED_SUBMISSIONS ";
	$sql .= "WHERE ";
	$sql .= "    TEAM_ID='$team_id' AND PROBLEM_ID='$problem_id' ";
	$sql .= "ORDER BY ATTEMPT DESC";
	$result = mysql_query($sql);
	
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_assoc($result);
		$attempt = $row[ATTEMPT]+1;
	} else {
		$attempt = 1;
	}

	$ts = time();

	#dcrim - immeadiatly change the timestamp into contest timespace
	#this allows differant sites to start at differant times, yet still have
	#all the times be synchronized
	$ts = $ts - $site_start_offset;
	

	$extension="";
	for ($i=strlen($_FILES[source_file][name])-1; $i>=0; $i--) {
		if ($_FILES[source_file][name][$i]==".") {
			break;
		}
		$extension = $_FILES[source_file][name][$i].$extension;
	}
	
	$queue_file_name = "$team_id-$problem_id-$ts.$extension";
	move_uploaded_file($_FILES[source_file][tmp_name],
		       "$base_dir/queue/$queue_file_name");
    chmod("$base_dir/queue/$queue_file_name", 0644);

	$sql  = "INSERT INTO QUEUED_SUBMISSIONS (TEAM_ID, PROBLEM_ID, ATTEMPT, TS, SOURCE_FILE) ";
	$sql .= "VALUES ('$team_id','$problem_id', '$attempt', '$ts','$queue_file_name') ";
	$result = mysql_query($sql);

	header("location: submissions.php?state=3");
	exit(0);
?>
