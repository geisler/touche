<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: clarifications.php
#
	include("lib/header.inc");
	include_once("lib/config.inc");
	include_once("lib/data.inc");

$problem_id = $_GET['problem_id'];
$success = $_GET['success'];
if (!$problem_id) {
	$problem_id = 0;
}

echo "<center>\n";
if ($problem_id == 0) {
	echo "All";
} else {
	echo "<a href=clarifications.php?problem_id=0>All</a>";
}
if ($problem_id == -1) {
	echo " | General";
} else {
	echo " | <a href=clarifications.php?problem_id=-1>General</a>";
}
$contest_disabled = false;
foreach ($problems as $problem) {
	if (time() < $site_start_ts) {
	    echo " | Contest has not yet started at your site!";
	    $contest_disabled = true;
	    break;
	} elseif (time() > ($contest_end_ts + $site_start_offset)) {
		echo " | Contest has ended!";
		$contest_disabled = true;
		break;
	} else {
		if ($problem['id'] == $problem_id) {
			echo " | $problem[name]";
		} else {
			echo " | <a href=clarifications.php?problem_id=$problem[id]>$problem[name]</a>";
		}
	}
}
echo "</center>\n";
	

if($contest_disabled == false){
    echo "<br><center><b><a href=clarification_request_form.php?>Request Clarification</a></b></center><br>\n";
}
else{
    echo "<br><center><b>Request Clarification</b><br>The contest is not currently active</center><br>\n";
}
if(isset($success) && $success == true) { 
    echo "<center><font color=#00ff00><b>";
    echo "Clarification Request Successfully Submitted";
    echo "</b></font><center>";
}
echo "<br><table align=center bgcolor=#000000 width=90%
	cellpadding=0 cellspacing=0 border=0><tr><td>\n";
echo "<table align=center width=100% cellpadding=5 cellspacing=1 border=0>\n";
echo "<tr><td colspan=5 align=center bgcolor=$hd_bg_color1>\n";
echo "<font color=$hd_txt_color1><b>Clarifications</b></font></td></tr>\n";
	
$sql  = "SELECT * FROM CLARIFICATION_REQUESTS ";
$sql .= "WHERE ";
$sql .= "    (TEAM_ID='$team_id' OR BROADCAST='1') AND ";
$sql .= "    RESPONSE <> '' ";
if($problem_id != 0) {
    $sql .= "    AND PROBLEM_ID='$problem_id'";
}
$sql .= "ORDER BY SUBMIT_TS DESC ";
$result = mysql_query($sql);

$clar = 0;	
while ($row = mysql_fetch_assoc($result)) {
	$clar = 1;
	echo "<tr bgcolor=$hd_bg_color2>\n";
    echo "<td align=center width=33%>";
    echo "<font color=$hd_txt_color2><b>Team</b></font></td>\n";
    echo "<td align=center width=33%>";
    echo "<font color=$hd_txt_color2><b>Submission Time</b></font></td>\n";
    echo "<td align=center width=34%>";
    echo "<font color=$hd_txt_color2><b>Reply Time</b></font></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    if ($row['TEAM_ID'] != -1) {
        echo "<td align=center bgcolor=$data_bg_color1>";
        echo $teams[$row['TEAM_ID']]['name'] . "</td>\n";
    }
    else {
        echo "<td align=center bgcolor=$data_bg_color1>Judge</td>\n";
    }
    echo "<td align=center bgcolor=$data_bg_color1>\n";
    echo date("g:ia",$row['SUBMIT_TS'])."\n";
    echo "</td>\n";
    echo "<td align=center bgcolor=$data_bg_color1>\n";
    if($row['REPLY_TS'] != 0) {
		echo date("g:ia",$row['REPLY_TS'])."\n";
	}
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=left valign=top bgcolor=$hd_bg_color2><font color=$hd_txt_color2><b>Problem</b></font></td>\n";
    echo "<td bgcolor=$data_bg_color1 colspan=2>\n";
    if ($row['PROBLEM_ID']==-1) {
        echo "General\n";
    }
    else {
        echo $problems[$row['PROBLEM_ID']]['name']."\n";
    }
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=left valign=top bgcolor=$hd_bg_color2>";
    echo "<font color=$hd_txt_color2><b>Question</b></font></td>\n";
    echo "<td colspan=2 bgcolor=$data_bg_color1>$row[QUESTION]</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=left valign=top bgcolor=$hd_bg_color2>";
    echo "<font color=$hd_txt_color2><b>Response</b></font></td>\n";
    echo "<td colspan=2 bgcolor=$data_bg_color1>$row[RESPONSE]</td>\n";
    echo "</tr>\n";
	if ($row['RESPONSE']=='') {
        echo "  <tr>\n";
        echo "<td colspan=3 bgcolor=$data_bg_color1>";
        echo "<a href='clarification_response_form.php?clarification_id=$row[CLARIFICATION_ID]'>";
        echo "Respond to Clarification</a></td>\n";
        echo "  </tr>\n";
    }
}
    if(!$clar){
        echo "<tr><td align=center bgcolor=$data_bg_color1>";
        echo "There are no clarifications</td></tr>";
    }
    echo "</table></table>";

	include("lib/footer.inc");
?>
