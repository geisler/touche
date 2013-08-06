<?php
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
# Copyright (C) 2005 David Crim
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: standings.php
#
    include_once("lib/header.inc");
    include_once("lib/config.inc");
    include_once("lib/data.inc");
    include_once("lib/session.inc");

    if($contest_freeze_ts > time()) {
    	echo "<meta http-equiv = 'refresh' content = '60'>\n";
    }
   
    $link = mysql_connect ($db_host , $db_user, $db_pass) or die ("Could not connect to database");
    mysql_select_db ($db_name) or die ("Could not select database");

/*
    // There is apparently a problem with $HTTP_GET_VARS vs. $_GET. $HTTP_GET_VARS
    // does not contain the correct answers with the Evansville test. So I'm trying
    // to get it to work either way according to whatever that version of PHP wants.
    // --sb 2006-10-21
    // Original code is 5 lines below.
    if (!isset($HTTP_GET_VARS['selected_category'])) {
	$selected_category = "Overall";
    } else {
	$selected_category = $HTTP_GET_VARS['selected_category'];
    }
*/
    if (isset($_GET['selected_category'])) {
        $selected_category = $_GET['selected_category'];
    }
    else if (isset($HTTP_GET_VARS['selected_category'])) {
        $selected_category = $HTTP_GET_VARS['selected_category'];
    } else {
        $selected_category = "Overall";
    }


    echo "<center>\n";
    if ($selected_category=="Overall") {
	echo "Overall";
    } else {
	echo "<a href=\"standings.php?selected_category=Overall\">Overall</a>";
    }

    foreach ($categories as $category) {
	if ($selected_category == $category['name']) {
	    echo " | $category[name]";
	} else {
	    echo " | <a href=\"standings.php?selected_category=$category[name]\">$category[name]</a>";
	}
    }
    echo "</center>\n";

    if ($selected_category == "Overall") {
	$i=0;
	foreach ($teams as $team) {
	    $standings[$i]['team_id'] = $team['id'];
	    $standings[$i]['team_name'] = $team['name'];
	    if(!isset($standings[$i]['penalty'])){
		$standings[$i]['penalty'] = 0;
	    }
	    if(!isset($standings[$i]['problems_completed'])){
		$standings[$i]['problems_completed'] = 0;
	    }
	    $i++;
	}
    } else {
	$i=0;
	foreach ($categories[$selected_category]['team_ids'] as $category_team_id) {
	    $standings[$i]['team_id'] = $category_team_id;
	    $standings[$i]['team_name'] = $teams[$category_team_id]['name'];
	    if(!isset($standings[$i]['penalty'])){
		$standings[$i]['penalty'] = 0;
	    }
	    if(!isset($standings[$i]['problems_completed'])){
		$standings[$i]['problems_completed'] = 0;
	    }
	    $i++;
	}
    }

    $sql = "SELECT START_TS ";
    $sql .= "FROM SITE, TEAMS ";
    $sql .= "WHERE SITE.SITE_ID = TEAMS.SITE_ID AND ";
    $sql .= "TEAMS.TEAM_ID = $team_id";
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    $site_current_time = time() - ($row['START_TS'] - $contest_start_ts);
    

    for ($i = 0; $i < count($standings); $i++) {
	$sql  = "SELECT PROBLEM_ID, TS, ATTEMPT, RESPONSE_ID ";
	$sql .= "FROM JUDGED_SUBMISSIONS ";
	$sql .= "WHERE ";
	$sql .= "    TEAM_ID='".$standings[$i]['team_id'] . "'";
	if ($standings[$i]['team_id'] != $team_id) {
		$sql .= " AND ";
		$sql .= "    TS<'$contest_freeze_ts'  AND ";
		$sql .= "    TS<'$site_current_time' ";
	}
	#also need to make sure it's not more than the current contest
	$sql .= "ORDER BY PROBLEM_ID ASC ";

	$result = mysql_query($sql);

	while($row = mysql_fetch_assoc($result)) {
	    if($row['RESPONSE_ID'] == 9) {
		// each incorrect submission counts as 20 penalty points
		$incorrect_submission_penalty = ($row['ATTEMPT'] - 1) * 20;
		// each minute counts as one penalty point
		$time_penalty = (int) ((($row['TS'] - $contest_start_ts) / 60)/* + 0.5*/);
		$standings[$i]['problems'][$row['PROBLEM_ID']]['ts'] = $row['TS'];
		$standings[$i]['problems'][$row['PROBLEM_ID']]['penalty'] = $time_penalty + $incorrect_submission_penalty;
		$standings[$i]['problems_completed']++;
	    } 
	    $standings[$i]['problems'][$row['PROBLEM_ID']]['attempt'] = $row['ATTEMPT'];
	    $standings[$i]['problems'][$row['PROBLEM_ID']]['response_id'] = $row['RESPONSE_ID'];
	}
    }

    // total up the penalty points
    for($i=0; $i < count($standings); $i++) {
	foreach ($problems as $problem) {
	    if(!isset($standings[$i]['problems'])){
		$standings[$i]['problems'] = 0;
	    }

	    $standings[$i]['penalty'] += $standings[$i]['problems'][$problem['id']]['penalty'];
	}
    }

    function cmp ($a, $b) {
	if ($a['problems_completed'] > $b['problems_completed']) {
	    return -1;
	} elseif ($a['problems_completed'] < $b['problems_completed']) {
	    return 1;
	} else {
	    if ($a['penalty'] < $b['penalty']) {
		return -1;
	    } elseif ($a['penalty'] > $b['penalty']) {
		return 1;
	    }
	}
	return 0;
    }
    
    usort($standings, "cmp");
    //find the first team that is not exhibition
    $x = 0;
    while(checkexhib($standings[$x]['team_id']) == 1) {
	$standings[$x]['rank'] = '-';
	$x++;
    }
    $standings[$x]['rank'] = 1;

    $current_rank = 1;
    for($i=$x+1; $i < count($standings); $i++) {
        //check to see if the team is exhibition, if it is, don't rank them
        $excheck = checkexhib($standings[$i]['team_id']);

        //echo $standings[$i]['team_id'] ." - " . $excheck . "<br>";
        if($excheck == 1)
        {
                $standings[$i]['rank'] = '-';
        }
        else
        {
		if($standings[$i]['problems_completed'] == $standings[$i-1]['problems_completed'] && 
		    $standings[$i]['penalty'] == $standings[$i-1]['penalty']) 
		{
			#if the problems completed and the penalty seconds are the same then they are the same rank
		}
		else
		{
			$current_rank++;
		}
		$standings[$i]['rank'] = $current_rank;
	}
    }

    echo "<br><table align=center bgcolor=#000000 width=90% cellpadding=0 cellspacing=0 border=0><tr><td>\n";
    echo "<table align=center width=100% cellpadding=5 cellspacing=1 border=0>\n";
    echo "<tr><td align=center colspan=99 bgcolor=";
    if($contest_freeze_ts < time()) {
	echo "red>";
    }
    else {
	echo "$hd_bg_color1>";
    }
    echo "<font color=$hd_txt_color1><b>Standings";
    if($contest_freeze_ts < time()) {
	echo " - Frozen";
    }
    echo "</b></font>";
    echo "</td></tr>\n";
    echo "<tr bgcolor=$hd_bg_color2>\n";
    echo "<td>&nbsp</td>\n";
    echo "<td align=center><font color=$hd_txt_color2><b>Team Name</b></font></td>\n";
    for($i=1; $i<=$num_problems; $i++) { // changes this later
	echo "<td align=center><font color=$hd_txt_color2><b>Prob #$i</b></font></td>";
    }
    echo "<td align=center><font color=$hd_txt_color2><b>Completed</b></font></td>";
    echo "</tr>\n";

    for($i=0; $i<count($standings); $i++) {
	if($i%2 == 0) {
	    echo "<tr bgcolor=\"$data_bg_color1\">\n";
	} else {
	    echo "<tr bgcolor=\"$data_bg_color2\">\n";
	}
	echo "<td>\n";
	echo "<font face=\"Arial\" size=\"3\">\n";
	echo trim($standings[$i]['rank']);
	echo "</font>\n";
	echo "</td>\n";

	echo "<td>\n";
	echo "<font face=\"Arial\" size=\"3\">\n";
	echo $standings[$i]['team_name'];
	echo "</font>\n";
	echo "</td>\n";

	//hack so if problems don't start at 0
	$sql = "select PROBLEM_ID from PROBLEMS ORDER by PROBLEM_ID";
	$result = mysql_query($sql);
	$row = mysql_fetch_assoc($result);
	$min = $row['PROBLEM_ID'];
	for($j=$min; $j<($min + $num_problems); $j++) { // change this later
	    echo "<td align=center>\n";
	    echo "<font face=arial size=3>\n";
	    if(isset($standings[$i]['problems'][$j]['penalty'])) {
		echo gmdate("H:i", $standings[$i]['problems'][$j]['ts'] - $site_start_ts);
	    } else {
		echo "--";
	    }
	    echo "/";
	    if(isset($standings[$i]['problems'][$j]['attempt'])) {
		echo $standings[$i]['problems'][$j]['attempt'];
	    } else {
		echo "--";
	    }
	    echo "</font>\n";
	    echo "</td>\n";
	}

	echo "<td align=center>\n";
	if(!isset($standings[$i]['problems_completed']) || $standings[$i]['problems_completed']==0) {
	    echo "0";
	} else {
	    echo $standings[$i]['problems_completed'];
	}
	echo " ";
	if(!isset($standings[$i]['penalty']) || $standings[$i]['penalty']==0) {
	    echo "(0)";
	} else {
	    $tmp = chg_sec($standings[$i]['penalty']*60);
	    echo "($tmp)";		
	}

	echo "</td>\n";
	echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</table>\n";
    echo "<br>\n";
    include("lib/footer.inc");

    #function to print seconds into hours:mins
    function chg_sec($secs)
    {
    	$mins = 0;
	$hours = 0;
	
	$mins += (int) floor ($secs / 60);
	$secs = (int) $secs % 60;
		          
	$hours += (int) floor ($mins / 60);
	$mins = $mins % 60;
    	
	if($mins < 10)
	    $mins = "0$mins";
	
	return ("$hours:$mins"); 
    }

function checkexhib($team)
{
        global $selected_category;
        if($selected_category != 'Exhibition') {
                $sql  = "SELECT * FROM CATEGORIES AS C, CATEGORY_TEAM AS T WHERE C.CATEGORY_ID = T.CATEGORY_ID AND C.CATEGORY_NAME = 'Exhibition'";
                $sql .= " AND T.TEAM_ID = $team";
                $ex = mysql_query($sql);
                $num_rows = mysql_num_rows($ex);
                return $num_rows;
        }
        else {
                #allows teams to be ranked if they are exhibition if the page is exhibition
                return 0;
        }
}

?>
