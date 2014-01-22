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

include("lib/admin_config.inc");
include("lib/data.inc");
include("lib/session.inc");
include("lib/header.inc");

if($_SERVER['REQUEST_METHOD'] == 'POST' && !$_POST['team']) {

    $sql  = "UPDATE JUDGED_SUBMISSIONS ";
    $sql .= "SET RESPONSE_ID = $_POST[result], JUDGED = 1 ";
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
if ($team_filter != "all")
	$where = " AND T.TEAM_NAME = '" . $team_filter . "'";
else
	$where = "";

if ($_GET['problem'] && $_GET['problem'] != "all")
	$where .= " AND P.PROBLEM_NAME = '" . $_GET['problem'] . "'";

#get contest start time
$sql = "SELECT * ";
$sql .= "FROM CONTEST_CONFIG ";

$sql_result = mysql_query($sql);

if(!$sql_result){
        sql_error($sql);
}
$row = mysql_fetch_assoc($sql_result);
$start_ts = $row['START_TS'];




$sql = "SELECT * ";
$sql .= "FROM PROBLEMS";
$sql_result = mysql_query($sql);
if (!$sql_result){
        sql_error($sql);
}
echo "<center>";

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
echo "</center>";

$sql = "SELECT TEAM_NAME FROM TEAMS ORDER BY TEAM_NAME";
$sql_result = mysql_query($sql);
if(!$sql_result){
        sql_error($sql);
}



echo "<center><form action='" . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . "' method=POST name='teamFilter'>\n";
echo "Filter By Team: <select name='team' onChange='teamFilter.submit()'>\n";
if ($team_filter == "all")
        echo "<option value='all' selected>All</option>\n";
else
        echo "<option value='all'>All</option>\n";

while($row = mysql_fetch_assoc($sql_result)){
        echo "<option value='$row[TEAM_NAME]'";
if($row['TEAM_NAME'] == $team_filter)
        echo" selected";
        echo">$row[TEAM_NAME]</option>\n";
}
echo "</select></form></center>";

#get all of the changes from the old to new judgement
$sql = "SELECT DISTINCT JSC.TS, 
P.PROBLEM_NAME, 
T.TEAM_NAME, 
JSC.ATTEMPT, 
JS.JUDGED_ID,
P.PROBLEM_ID,
T.TEAM_ID,
R1.RESPONSE AS 'JUDGE', 
R2.RESPONSE AS 'AUTO',
R3.RESPONSE AS 'CURRENT',
JS.JUDGED

FROM AUTO_RESPONSES AR, 
JUDGED_SUBMISSIONS JS, 
JUDGED_SUBMISSIONS_COPY JSC, 
RESPONSES R1, 
RESPONSES R2, 
RESPONSES R3,
TEAMS T, 
PROBLEMS P

WHERE P.PROBLEM_ID = JSC.PROBLEM_ID 
    AND T.TEAM_ID = JSC.TEAM_ID 
    AND AR.JUDGED_ID = JS.JUDGED_ID 
                AND R1.RESPONSE_ID = JSC.RESPONSE_ID
                AND R2.RESPONSE_ID = AR.AUTO_RESPONSE
		AND R3.RESPONSE_ID = JS.RESPONSE_ID
                AND JS.TEAM_ID = JSC.TEAM_ID
                AND JS.ATTEMPT = JSC.ATTEMPT
                AND JS.PROBLEM_ID = JSC.PROBLEM_ID
                AND AR.AUTO_RESPONSE <> JSC.RESPONSE_ID" . $where;

$sql_result = mysql_query($sql);
if (!$sql_result){
        sql_error($sql);
}



 # display appropreate queued submissions
echo "<br><table align=center bgcolor=#000000 width=50%
        cellpadding=0 cellspacing=0 border=0><tr><td>\n";
echo "<table align=center width=100% cellpadding=6 cellspacing=1 border=0>\n";
echo "<tr><td colspan=8 align=center bgcolor=$hd_bg_color1>\n";
echo "<font color=$hd_txt_color1><b>Review All Submissions</b></font></td></tr>\n";
echo "<tr><td bgcolor=$hd_bg_color2>Submission Time</td>\n";
echo "<td bgcolor=$hd_bg_color2 align=center>Problem</td>\n";
echo "<td bgcolor=$hd_bg_color2 align=center>Team ID</td>\n";
echo "<td bgcolor=$hd_bg_color2 align=center>Attempt</td>\n";
echo "<td bgcolor=$hd_bg_color2 align=center>Old Judge Response</td>\n";
echo "<td bgcolor=$hd_bg_color2 align=center>Auto Response</td>\n";
echo "<td bgcolor=$hd_bg_color2 align=center>Current Response</td>\n";
echo "<td bgcolor=$hd_bg_color2 align=center>Change Jugdement</td></tr>\n";

while($row = mysql_fetch_assoc($sql_result)){
	$color_preserve = $data_bg_color1;
	if ($row['JUDGED'] != 0)
		$data_bg_color1 = $hd_bg_color2;
	$min = intval(($row['TS'] - $start_ts)/60);
        echo "<td bgcolor=$data_bg_color1 align=center>$min</td>\n";
        echo "<td bgcolor=$data_bg_color1 align=center>" . $row['PROBLEM_NAME'] . "</td>\n";
        echo "<td bgcolor=$data_bg_color1 align=center>" . $row['TEAM_NAME'] . "</td>\n";
        echo "<td bgcolor=$data_bg_color1 align=center>" . $row['ATTEMPT'] . "</td>\n";
        echo "<td bgcolor=$data_bg_color1 align=center>" . $row['JUDGE'] . "</td>\n";
        echo "<td bgcolor=$data_bg_color1 align=center>" . $row['AUTO'] . "</td>\n";
        echo "<td bgcolor=$data_bg_color1 align=center>" . $row['CURRENT'] . "</td>\n";
 	echo "<td bgcolor=$data_bg_color1 align=center><a href='judge_response.php?judged_id=" .
        	$row['JUDGED_ID'] . "&team_id=" . $row['TEAM_ID'] .
                "&problem=" . $row['PROBLEM_ID'] . "&attempt=" . $row['ATTEMPT'] . "&page=review.php'>judge submission</a></td>\n";
	echo "</tr>\n";
	$data_bg_color1 = $color_preserve;
}

echo "</table></table>\n";

echo "<p><center>\n";
echo "<form action='rejudge.php' method='POST'>\n";
echo "Undo rejudgement:<input type='submit' value='UNDO'>(I'm still wary of this button)\n";
echo "<input type='hidden' name='undo' value='true'>\n";
echo "</form></center></p>";
include("lib/footer.inc");
?>
