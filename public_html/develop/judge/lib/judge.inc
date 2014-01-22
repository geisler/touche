<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2003, 2005, 2013 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/lib/judge.inc
#

include_once('database.inc');
include_once('responses.inc');

$link = mysql_connect($db_host, $db_user, $db_pass);
if (!$link) {
    print "Sorry.  Database connect failed.  Retrying...";
    exit;
}

$connect_good = mysql_select_db($db_name);
if (!$connect_good) {
    print "Sorry.  Database selection failed.  Retrying...";
    exit;
}

$contest_info = mysql_query("SELECT * FROM SITE WHERE SITE_ID = 1");
if (!$contest_info) {
    print "Sorry.  Database request failed.  Retrying...";
    exit;
}

$row = mysql_fetch_assoc($contest_info);
    if (mysql_num_rows($contest_info) < 1) {
	print "Sorry.  No contest configuration information exists.<br /> ";
	print "Please contact a system administrator.";
	exit;
    }

    $contest_host = $row['CONTEST_HOST'];
    $contest_name = $row['CONTEST_NAME'];
    $contest_date = $row['CONTEST_DATE'];
    list ($contest_year, $contest_month, $contest_day)
	= split('-', $contest_date);

    $contest_start_time = $row['START_TIME'];
    list ($contest_start_hour, $contest_start_minute)
	= split(':', $contest_start_time);
    $contest_freeze_time = $row['FREEZE_DELAY'];
    $contest_end_time = $row['CONTEST_END_DELAY'];
    $base_dir = $row['BASE_DIRECTORY'];

    $judge_user = $row['JUDGE_USER'];
    $judge_pass = $row['JUDGE_PASS'];

#    $contest_start_ts = mktime($contest_start_hour,
#			       $contest_start_minute,
#			       0,
#			       $contest_month,
#			       $contest_day,
#			       $contest_year);
	$contest_start_ts = $row['START_TS'];
    $contest_freeze_ts = $contest_start_ts + $contest_freeze_time;
    $contest_end_ts = $contest_start_ts + $contest_end_time;
    if(time() > $contest_end_ts) {
#	system("crontab $base_dir/stop_contest.crontab", $result);
	system("rm -f $base_dir/../active-contests/$contest_name", $result);
        if ($result != 0){
                echo "<p><font color=$hd_txt_color2>Warning! Crontab Failed to start, please contact the system administrator</font></p>";
        }
    }
	$contest_started = $row['HAS_STARTED'];

$teams_query = mysql_query("SELECT * FROM TEAMS");
if (!$teams_query) {
    # Added the "mysql_error() part. -sb 2006-10-05
    print "Sorry.  Database request (TEAMS) failed with " . mysql_error() . " Retrying...";
    exit;
}
$num_teams = mysql_num_rows($teams_query);
while ($row = mysql_fetch_assoc($teams_query)) {
    $team_id = $row['TEAM_ID'];
    $team_user = $row['USERNAME'];
    $teams[$team_id]['user'] = $team_user;

    $teams[$team_id]['id'] = $team_id;
    $users[$team_user]['team_id'] = $team_id;

    $team_name = $row['TEAM_NAME'];
    $teams[$team_id]['name'] = $team_name;
    $users[$team_user]['team_name'] = $team_name;

    $teams[$team_id]['school'] = $row['ORGANIZATION'];

    $team_password = $row['PASSWORD'];
    $teams[$team_id]['password'] = $team_password;
    $users[$team_user]['password'] = $team_password;
}

$sql = "SELECT ct.TEAM_ID, c.CATEGORY_NAME";
$sql .= " FROM CATEGORY_TEAM ct, CATEGORIES c";
$sql .= " WHERE ct.CATEGORY_ID = c.CATEGORY_ID";
$categories_query = mysql_query($sql);
if (!$categories_query) {
    print "Sorry.  Database request (CATEGORIES) failed.  Retrying...";
    exit;
}
while ($row = mysql_fetch_assoc($categories_query)) {
    $cat_name = $row['CATEGORY_NAME'];
    if ($cat_name) {
	$categories[$cat_name]['name'] = $cat_name;
	if (!isset($categories[$cat_name]['team_ids']) || 
	    !is_array($categories[$cat_name]['team_ids'])) {
	    $categories[$cat_name]['team_ids'] = array();
	}
	array_push($categories[$cat_name]['team_ids'], $row['TEAM_ID']);
	//print $categories[$cat_name]['team_ids'].$row['TEAM_ID']."<br />";
    }
}

$problems_query = mysql_query("SELECT * FROM PROBLEMS ORDER BY PROBLEM_ID");
if (!$problems_query) {
    print "Sorry.  Database request (PROBLEMS) failed.  Retrying...";
    exit;
}
$num_problems = mysql_num_rows($problems_query);
while ($row = mysql_fetch_assoc($problems_query)) {
    $problem_id = $row['PROBLEM_ID'];
    $problems[$problem_id]['id'] = $row['PROBLEM_ID'];
    $problems[$problem_id]['name'] = $row['PROBLEM_NAME'];
    $problems[$problem_id]['loc'] = $row['PROBLEM_LOC'];
    $problems[$problem_id]['note'] = $row['PROBLEM_NOTE'];
}

$responses_query = mysql_query("SELECT * FROM RESPONSES");
if (!$responses_query) {
    print "Sorry.  Database request (RESPONSES) failed.  Retrying...";
    exit;
}
while ($row = mysql_fetch_assoc($responses_query)) {
    $response_id = $row['RESPONSE_ID'];
    $responses[$response_id]['id'] = $response_id;
    $responses[$response_id]['response'] = $row['RESPONSE'];
    $responses[$response_id]['color'] = $row['RESPONSE_COLOR'];
}
?>
