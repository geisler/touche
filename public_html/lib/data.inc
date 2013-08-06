<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2003, 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: lib/data.inc
#

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

$contest_info = mysql_query("SELECT * FROM CONTEST_CONFIG");
if (!$contest_info) {
    print "Sorry.  Database request (CONTEST_CONFIG) failed.  Retrying...";
    exit;
}


$row = mysql_fetch_assoc($contest_info);
    if (mysql_num_rows($contest_info) < 1) {
	print "Sorry.  No contest configuration information exists.<br /> ";
	print "Please contact a system administrator.";
	exit;
    }

    $contest_host = $row['HOST'];
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

#    $contest_start_ts = mktime($contest_start_hour,
#			       $contest_start_minute,
#			       0,
#			       $contest_month,
#			       $contest_day,
#			       $contest_year);
	$contest_start_ts = $row['START_TS'];
    $contest_freeze_ts = $contest_start_ts + $contest_freeze_time;
    $contest_end_ts = $contest_start_ts + $contest_end_time;
	$contest_started = $row['HAS_STARTED'];

if(isset($team_id)){
    $sql = "SELECT * FROM SITE, TEAMS ";
    $sql .= "WHERE SITE.SITE_ID = TEAMS.SITE_ID AND TEAMS.TEAM_ID = $team_id";
    $site_info = mysql_query($sql);
    $row = mysql_fetch_assoc($site_info);
    $site_start_time = $row['START_TIME'];
    list ($site_start_hour, $site_start_minute, $site_start_second)
	= split(':', $site_start_time);
#  $site_start_ts = mktime($site_start_hour,
#			       $site_start_minute,
#			       $site_start_second,
#			       $contest_month,
#			       $contest_day,
#			       $contest_year);
	$site_start_ts = $row['START_TS'];
    $site_start_offset = $site_start_ts - $contest_start_ts;
	$site_started = $row['HAS_STARTED'];
    }


$teams_query = mysql_query("SELECT * FROM TEAMS");
if (!$teams_query) {
    print "Sorry.  Database request (TEAMS) failed.  Retrying...";
    exit;
}
$num_teams = mysql_num_rows($teams_query);
while ($row = mysql_fetch_assoc($teams_query)) {
    $team_id_ctr = $row['TEAM_ID'];
    $team_user = $row['USERNAME'];
    $team_password = $row['PASSWORD'];
    $my_team_name = $row['TEAM_NAME'];

    $users[$team_user]['team_id'] = $team_id_ctr;
    $users[$team_user]['password'] = $team_password;
    $users[$team_user]['team_name'] = $my_team_name;

    $teams[$team_id_ctr]['id'] = $team_id_ctr;
    $teams[$team_id_ctr]['user'] = $team_user;
    $teams[$team_id_ctr]['password'] = $team_password;
    // Hack to fix display name. Bit bigtime in standings display. I believe "$team_name" should be "$my_team_name". 
    // See setting "$users[$team_user]['team_name']" above to see why I think that..
    // $teams[$team_id_ctr]['name'] = $team_name;
    $teams[$team_id_ctr]['name'] = $my_team_name;
    $teams[$team_id_ctr]['organization'] = $row['ORGANIZATION'];
    $teams[$team_id_ctr]['coach_name'] = $row['COACH_NAME'];
    $teams[$team_id_ctr]['alternate_name'] = $row['ALTERNATE_NAME'];
    $teams[$team_id_ctr]['contestant_1_name'] = $row['CONTESTANT_1_NAME'];
    $teams[$team_id_ctr]['contestant_2_name'] = $row['CONTESTANT_2_NAME'];
    $teams[$team_id_ctr]['contestant_3_name'] = $row['CONTESTANT_3_NAME'];
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
	if (!isset($categories[$cat_name]['team_ids'])
		|| !is_array($categories[$cat_name]['team_ids'])) {
	    $categories[$cat_name]['team_ids'] = array();
	}
	array_push($categories[$cat_name]['team_ids'], $row['TEAM_ID']);
	//print $categories[$cat_name]['team_ids'].$row['TEAM_ID']."<br />";
    }
}

$problem_url = "problems";
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
}

$responses_query = mysql_query("SELECT * FROM RESPONSES");
if (!$responses_query) {
    print "Sorry.  Database request (RESPONSES) failed.  Retrying...";
    exit;
}
while ($row = mysql_fetch_assoc($responses_query)) {
    $response_id = $row['RESPONSE_ID'];
    $responses[$response_id]['response'] = $row['RESPONSE'];
    $responses[$response_id]['color'] = $row['RESPONSE_COLOR'];
}

?>
