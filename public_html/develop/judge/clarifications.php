<?
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2003 Jonathan Geisler
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/clarifications.php
#
    include_once("lib/config.inc");
    include_once("lib/judge.inc");
    include_once("lib/header.inc");


judge_header(60);

echo "<div class='container'>";
echo "<div class='innerglow'>";
echo "<div class='table-responsive'>";

$clar_id = $_GET["clar_id"];
if(!isset($_GET['sort']) || $_GET['sort'] == 'time') {
	$sort = 'time';
}
else {
	$sort = 'team';
}


if(!isset($clar_id)) {
    $clar_id = -2;
}
echo "<center>\n";
if ($clar_id == -2) {
    echo "Pending";
}
else {
    echo "<a href='clarifications.php?clar_id=-2&sort=$sort'>Pending</a>";
}
if ($clar_id == 0) {
    echo " | All";
}
else {
    echo " | <a href='clarifications.php?clar_id=0&sort=$sort'>All</a>";
}

if ($clar_id == -1) {
    echo " | General";
}
else {
    echo " | <a href='clarifications.php?clar_id=-1&sort=$sort'>General</a>";
}
foreach ($problems as $problem) {
	if ($problem[id] == $clar_id) {
		echo " | $problem[name]";
	} else {
		echo " | <a href=clarifications.php?clar_id=$problem[id]&sort=$sort>$problem[name]</a>";
	}
}
echo "</center>\n";


echo "<form action=clarifications.php method=get>";
echo "<div class='text-center'>";
echo "<b>Sort By:</b><select name='sort' onchange='JavaScript:submit()'>";
echo "<option value='time'";
if($sort == 'time') {
	echo "selected='selected'";
}
echo ">Time</option>\n";
echo "<option value='team'";
if($sort == 'team') {
	echo "selected='selected'";
}
echo ">Team</option>\n";
echo "</select>";
echo "<input type='hidden' name='clar_id' value='$clar_id'>";
echo "</form>\n";
	
if ($clar_id == -2) {
    $sql  = "SELECT * FROM CLARIFICATION_REQUESTS ";
    $sql .= "WHERE ";
    $sql .= "    RESPONSE='' ";
    if($sort == 'time')
    	$sql .= "ORDER BY SUBMIT_TS DESC ";
    else
    	$sql .= "ORDER BY TEAM_ID ASC ";
}
else if($clar_id == 0) {
    $sql = "SELECT * FROM CLARIFICATION_REQUESTS ";
    if($sort == 'time')
        $sql .= "ORDER BY SUBMIT_TS DESC ";
    else
        $sql .= "ORDER BY TEAM_ID ASC ";
}
else{
    $sql  = "SELECT * FROM CLARIFICATION_REQUESTS ";
    $sql .= "WHERE ";
    $sql .= "    PROBLEM_ID = $clar_id AND ";
    $sql .= "	 RESPONSE<>'' AND RESPONSE<>'IGNORED'";
    if($sort == 'time')
    	$sql .= "ORDER BY SUBMIT_TS DESC ";
    else
    	$sql .= "ORDER BY TEAM_ID ASC ";
}

$result = mysql_query($sql);

echo "</div>";

echo "<br><table class='table' align=center width=90% 
    cellpadding=0 cellspacing=0 border=0>\n";
echo "<tr><td colspan=5 align=center >\n";
echo "<h3>Clarifications</h3></td></tr>\n";

$clar = 0;
while ($row = mysql_fetch_assoc($result)) {
    $clar = 1;
    echo "<tr>\n";
    echo "<td align=center width=33%>";
    echo "<b>Team</b></td>\n";
    echo "<td align=center width=33%>";
    echo "<b>Submission Time</b></td>\n";
    echo "<td align=center width=34%>";
    echo "<b>Reply Time</b></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    if ($row['TEAM_ID'] != -1) {
	echo "<td align=center bgcolor='#CCCCCC'>";
	echo $teams[$row['TEAM_ID']]['name'] . "</td>\n";
    }
    else {
	echo "<td align=center>Judge</td>\n";
    }
    echo "<td align=center>\n";
    echo date("g:ia",$row['SUBMIT_TS'])."\n";
    echo "</td>\n";
    echo "<td align=center>\n";
    if($row['REPLY_TS'] != 0) {
	echo date("g:ia",$row['REPLY_TS'])."\n";
    }
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=left valign=top><b>Problem</b></td>\n";
    echo "		<td colspan=2>\n";
    if ($row['PROBLEM_ID']==-1) {
	echo "General\n";
    }
    else {
	echo $problems[$row['PROBLEM_ID']]['name']."\n";
    }
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=left valign=top>";
    echo "<b>Question</b></td>\n";
    echo "<td colspan=2>$row[QUESTION]</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<td align=left valign=top bgcolor='#CCCCCC'>";
    echo "<b>Response</b></td>\n";
    echo "<td colspan=2>$row[RESPONSE]</td>\n";
    echo "</tr>\n";
    if ($row['RESPONSE']=='') {
	echo "	<tr>\n";
	echo "<td colspan=3>";
	echo "<a href='clarification_response_form.php?clarification_id=$row[CLARIFICATION_ID]'>";
	echo "Respond to Clarification</a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
	echo "<a href='clarification_response_form.php?clarification_id=$row[CLARIFICATION_ID]&ignore=y'>";
	echo "Ignore Clarification</a></td>\n";
	echo "	</tr>\n";
    }
}
    if(!$clar){
	echo "<tr><td align=center bgcolor=$data_bg_color1>";
	echo "There are no new clarifications</td></tr>";
    }
    echo "</table>";
    echo "<center><a href='clarification_response_form.php?clarification_id=0'><button class=\"btn btn-default\">Make new Clarification</button></a></center>\n";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    include("lib/footer.inc");
?>
