<?
#
# Copyright (C) 2005 Steve Overton
# Copyright (C) 2005 David Crim
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/judge.php
#
# Set up default directories

if($_SERVER['REQUEST_METHOD'] == 'POST' && !$_POST['team']) {
    if ($page == "judge.php"){
	$set = "SET RESPONSE_ID = $_POST[result], JUDGED = 1 ";
    }	
    else{
	$sql = "SELECT JUDGED ";
	$sql .= "FROM JUDGED_SUBMISSIONS ";
	$sql .= "WHERE JUDGED_ID = $_POST[judged_id] ";

	$sql_result = mysql_query($sql);
        if(!$sql_result) {
        	sql_error($sql);
        }
        $row = mysql_fetch_assoc($sql_result);
	$judged = $row['JUDGED'] + 1;

	$set = "SET RESPONSE_ID = $_POST[result], JUDGED = $judged ";

    }
    
    $sql  = "UPDATE JUDGED_SUBMISSIONS ";
    $sql .= $set;
    $sql .= "WHERE JUDGED_ID = $_POST[judged_id] ";

    $result = mysql_query($sql);
    if(!$result) {
        sql_error($sql);
    }
    ob_flush();
    sleep(2);
}
if ($_POST['team']){
	$_SESSION['team'] = $_POST['team'];
}

$team_filter = $_SESSION['team'];

$sql = "SELECT * ";
$sql .= "FROM PROBLEMS";
$sql_result = mysql_query($sql);
if (!$sql_result){
	sql_error($sql);
}
echo "<center>";

# Checks to make sure that there are problems to judge
if($row = mysql_fetch_assoc($sql_result)){
	echo "Problems: ";
	
	if(!$_GET){
		echo "All ";
		echo "| <a href='$page?problem=" . $row['PROBLEM_NAME'] . "'>" . $row['PROBLEM_NAME'] . "</a> ";
		while ($row = mysql_fetch_assoc($sql_result)){
			echo "| <a href='$page?problem=" . $row['PROBLEM_NAME'] . "'>" . $row['PROBLEM_NAME'] . "</a> ";
		}
		$problem_name = "ALL";
	}
	else{
		echo "<a href='$page'>All</a> ";
		$problem_name = $_GET['problem'];
		if ($row['PROBLEM_NAME'] == $problem_name){
			echo "| $problem_name ";
			$problem_id = $row['PROBLEM_ID'];
		}
		else{
			echo "| <a href='$page?problem=" . $row['PROBLEM_NAME'] . "'>" . $row['PROBLEM_NAME'] . "</a> ";
		}

		while ($row = mysql_fetch_assoc($sql_result)){
			if ($row['PROBLEM_NAME'] == $problem_name){
		        	echo "| $problem_name ";
				$problem_id = $row['PROBLEM_ID'];				
			}
			else{
			       echo "| <a href='$page?problem=" . $row['PROBLEM_NAME'] . "'>" . $row['PROBLEM_NAME'] . "</a> ";
			}
		}
	}
	echo "</center>\n";

	$sql = "SELECT * ";
	$sql .= "FROM CONTEST_CONFIG ";

	$sql_result = mysql_query($sql);

	if(!$sql_result){
		sql_error($sql);
	}
	
	$row = mysql_fetch_assoc($sql_result);
	$start_ts = $row['START_TS'];
	$team_show = $row['TEAM_SHOW'];

	#get the teams for the drop down box
	if ($team_show == 1)
		$select = "TEAM_NAME";
	else
		$select = "TEAM_ID"; 
	$sql = "SELECT $select FROM TEAMS ORDER BY $select";
	$sql_result = mysql_query($sql);
        if(!$sql_result){
                sql_error($sql);
        }
	#enter the teams into a drop down box
	echo "<center><form action='" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "' method=POST name='teamFilter'>\n";
	echo "Filter By Team: <select name='team' onChange='teamFilter.submit()'>\n";
	if ($team_filter == "all")
		echo "<option value='all' selected>All</option>\n";
	else
		echo "<option value='all'>All</option>\n";

	while($row = mysql_fetch_assoc($sql_result)){
		echo "<option value='$row[$select]'";
		if($row[$select] == $team_filter)
			echo" selected";
		echo">$row[$select]</option>\n";
	}
	echo "</select></form></center>";
	
	#get all problems ordered by their time stape
	if ($page == "judge.php"){
		$sql = "SELECT * ";
		$sql .= "FROM JUDGED_SUBMISSIONS JS ";
		$sql .= "INNER JOIN TEAMS T ON JS.TEAM_ID = T.TEAM_ID ";
		$sql .= "INNER JOIN PROBLEMS P ON P.PROBLEM_ID = JS.PROBLEM_ID ";
		$sql .= "WHERE JS.JUDGED = 0 ";
	}
	else{
		$sql = "SELECT * ";
        	$sql .= "FROM JUDGED_SUBMISSIONS JS INNER JOIN TEAMS  T ON JS.TEAM_ID = T.TEAM_ID
                	INNER JOIN PROBLEMS P ON P.PROBLEM_ID = JS.PROBLEM_ID
                	INNER JOIN RESPONSES R ON R.RESPONSE_ID = JS.RESPONSE_ID ";
        	$sql .= "WHERE NOT JUDGED = 0 ";

	}
	#if there is a team filter specified
	if (isset($_SESSION['team'])){
		if ($team_filter != "all"){
			if ($select == "TEAM_NAME")
				$team_filter = "'$team_filter'";
	        		
			$sql .= "AND T.$select = " . $team_filter . " ";
		}
	}

	#if the judge has selected a specific problem limit to that problem
	if($problem_name != "ALL"){
		$sql .= "AND JS.PROBLEM_ID = $problem_id ";
	}
	$sql .= "ORDER BY JS.TS";
	
	$sql_result = mysql_query($sql);

	if(!$sql_result){
		sql_error($sql);
	}
	if (mysql_num_rows($sql_result) == 0)
		$end_string = "there are no submissions for this problem";	
	if ($team_show == 0)
        	$team_id = "Team ID";
        else
                $team_id = "Team Name";

	# display appropreate queued submissions
	echo "<br><table align=center bgcolor=#000000 width=50%
	cellpadding=0 cellspacing=0 border=0><tr><td>\n";
	echo "<table align=center width=100% cellpadding=5 cellspacing=1 border=0>\n";
	echo "<tr><td colspan=6 align=center bgcolor=$hd_bg_color1>\n";
	echo "<font color=$hd_txt_color1><b>Submissions Queued for Judging</b></font></td></tr>\n";
	echo "<tr><td bgcolor=$hd_bg_color2>Submission Time</td>\n";
	echo "<td bgcolor=$hd_bg_color2 align=center>Problem</td>\n";
	echo "<td bgcolor=$hd_bg_color2 align=center>$team_id</td>\n";
	echo "<td bgcolor=$hd_bg_color2 align=center>Attempt</td>\n";
	if ($page == "judge.php"){
		echo "<td bgcolor=$hd_bg_color2 align=center>Auto Response</td>\n";
        }
        else{
		echo "<td bgcolor=$hd_bg_color2 align=center>Current Response</td>\n";
        }
	echo "<td bgcolor=$hd_bg_color2 align=center>Go to judging page</td></tr>\n";
	    
	while($row = mysql_fetch_assoc($sql_result)){
		#calculate time sence start of contest
		$submit_ts = $row['TS'];
		// turns out we only want to display min
		//$hours = intval(($submit_ts - $start_ts)/3600);
		//$min = intval((($submit_ts - $start_ts)%3600)/60);
		//$sec = (($submit_ts - $start_ts)%3600)%60;
		$min = intval(($submit_ts - $start_ts)/60);
		
		#check to see if the submission has already been viewed or not so that other judges won't look at submissions already being viewed
		///html doesn't work this way
		if ($page == "judge.php"){
			$viewed = $row['VIEWED'];
			$sql = "SELECT * ";
			$sql .= "FROM AUTO_RESPONSES AR INNER JOIN RESPONSES R ON R.RESPONSE_ID = AR.AUTO_RESPONSE ";
			$sql .= "WHERE AR.JUDGED_ID = " . $row['JUDGED_ID']. " ";
		
			$response_result = mysql_query($sql);
        		if(!$response_result){
                		sql_error($sql);
        		}
			
			if (mysql_num_rows($response_result) == 0){
				$response = "pending";
			}
			else{
				$response_row = mysql_fetch_assoc($response_result);
				$response = $response_row['RESPONSE'];
				$response_id = $response_row['RESPONSE_ID'];
				while($response_row = mysql_fetch_assoc($response_result)){
					if($response_id != ECORRECT){
						if($response_id != $response_row['RESPONSE_ID'] && $response_row['RESPONSE_ID'] != ECORRECT){
							$response = "Multiple Responses";
						}
					}
					elseif ($response_row['RESPONSE_ID'] != ECORRECT){
						$response = $response_row['RESPONSE'];
						$response_id = $response_row['RESPONSE_ID'];
					}
							
				}
			}
		}
		else{
			$viewed = $row['JUDGED'] - 1;
			$response = $row['RESPONSE'];
		}

		$color_preserve = $data_bg_color1;
		
		if($viewed != 0)
			$data_bg_color1 = $data_bg_color2;



		if ($team_show == 0)
			$team_id = $row['TEAM_ID'];
		else
			$team_id = $row['TEAM_NAME'];
		
		echo "<tr><td bgcolor=$data_bg_color1>$min</td>\n";
		echo "<td bgcolor=$data_bg_color1 align=center>" . $row['PROBLEM_NAME'] . "</td>\n";
		echo "<td bgcolor=$data_bg_color1 align=center>" . $team_id . "</td>\n";
		echo "<td bgcolor=$data_bg_color1 align=center>" . $row['ATTEMPT'] . "</td>\n";
		echo "<td bgcolor=$data_bg_color1 align=center>$response</td>\n";
		echo "<td bgcolor=$data_bg_color1 align=center><a href='judge_response.php?judged_id=" . 
			$row['JUDGED_ID'] . "&team_id=" . $team_id . 
			"&problem=" . $row['PROBLEM_ID'] . "&attempt=" . $row['ATTEMPT'] . "&page=$page'>judge submission</a></td>\n";
		echo "</tr>\n";					    
		
		$data_bg_color1 = $color_preserve;

	}
	echo "</table></table>\n";
	echo "<center>$end_string</center>\n";
	
	
}
else{
	echo "there are curently no problems in this contest";
	echo "</center>\n";
}
echo "</body></html>";

# SQL ERROR
# Input: $sql - the query with the error
function sql_error($sql) {
    echo "<br>Error in SQL command: $sql";
    die;
}
?>
