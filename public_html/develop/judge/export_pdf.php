<?php
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/standings.php
#
#    include_once("lib/config.inc");
#    include_once("lib/judge.inc");
    include_once("lib/session.inc");
    include_once('class.ezpdf.php');
#    include_once("lib/header.inc");
#


if (!isset($HTTP_GET_VARS['selected_category'])) {
    $selected_category = "Overall";
} else {
    $selected_category = $HTTP_GET_VARS['selected_category'];
}

#echo "<center>Categories: \n";
if ($selected_category=="Overall") {
#    echo "Overall";
} else {
#    echo "<a href=\"standings.php?selected_category=Overall\">Overall</a>";
}

foreach ($categories as $category) {
    if ($selected_category == $category['name']) {
#        echo " | $category[name]";
    }
    else {
#        echo " | <a href=\"standings.php?selected_category=$category[name]\">";
#        echo "$category[name]</a>";
    }
}
#echo "</center>\n";

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
}
else {
    $i=0;
    foreach ($categories[$selected_category]['team_ids']
        as $category_team_id) {
        $standings[$i]['team_id'] = $category_team_id;
        $standings[$i]['team_name']
            = $teams[$category_team_id]['name'];
        if(!isset($standings[$i]['penalty'])){
            $standings[$i]['penalty'] = 0;
        }
        if(!isset($standings[$i]['problems_completed'])){
            $standings[$i]['problems_completed'] = 0;
        }
        $i++;
    }
}

for ($i = 0; $i < count($standings); $i++) {
    $sql  = "SELECT PROBLEM_ID, TS, ATTEMPT, RESPONSE_ID ";
    $sql .= "FROM JUDGED_SUBMISSIONS ";
    $sql .= "WHERE ";
    $sql .= "    TEAM_ID='" . $standings[$i]['team_id'] . "' ";
    $sql .= "ORDER BY PROBLEM_ID ASC ";
    $result = mysql_query($sql);

    while($row = mysql_fetch_assoc($result)) {
        if($row['RESPONSE_ID'] == 1) {
            // each incorrect submission counts as 20 penalty points
            $incorrect_submission_penalty = ($row['ATTEMPT'] - 1) * 20;
            // each minute counts as one penalty point
            $time_penalty = (int) ((($row['TS'] - $contest_start_ts) / 60) + 0.5);
            $standings[$i]['problems'][$row['PROBLEM_ID']]['ts'] = $row['TS'];
            $standings[$i]['problems'][$row['PROBLEM_ID']]['penalty'] = $time_penalty + $incorrect_submission_penalty;
            if(!isset($standings[$i]['problems_completed'])){
                $standings[$i]['problems_completed'] = 0;
            }
            $standings[$i]['problems_completed']++;
        }
        $standings[$i]['problems'][$row['PROBLEM_ID']]['attempt'] = $row['ATTEMPT'];
        $standings[$i]['problems'][$row['PROBLEM_ID']]['response_id'] = $row['RESPONSE_ID'];
    }
}

// total up the penalty points
for($i=0; $i < count($standings); $i++) {
    if(isset($problems)) {
        foreach ($problems as $problem) {
            if(!isset($standings[$i]['problems'])){
                $standings[$i]['problems'] = 0;
            }
            $standings[$i]['penalty']
                += $standings[$i]['problems'][$problem['id']]['penalty'];
        }
    }
}

function cmp ($a, $b) {
    if ($a['problems_completed'] > $b['problems_completed']) {
        return -1;
    }
    elseif ($a['problems_completed'] < $b['problems_completed']) {
        return 1;
    }
    else {
        if ($a['penalty'] < $b['penalty']) {
            return -1;
        }
        elseif ($a['penalty'] > $b['penalty']) {
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

#for($i=1; $i < count($standings); $i++) {
#    if($standings[$i]['problems_completed'] != $standings[$i-1]['problems_completed'] && $standings[$i]['penalty'] == $standings[$i-1]['penalty'])
#    {
#       #if the problems completed and the penalty seconds are the same, then they are the same rank
#    }
#    else
#    {
#       $current_rank++;
#    }
#    $standings[$i]['rank'] = $current_rank;
#}

#echo "<br><table align=center bgcolor=#000000 width=90% cellpadding=0 cellspacing=0 border=0><tr><td>\n";
#echo "<table align=center width=100% cellpadding=5 cellspacing=1 border=0>\n";
#echo "<tr><td colspan=99 align=center bgcolor=";
#    if($contest_freeze_ts < time()) {
#        echo "red>";
#    }
#    else {
#        echo "$hd_bg_color1>";
#    }
#echo "<font color=$hd_txt_color1><b>Standings</b></font></td></tr>\n";
#echo "<tr bgcolor=$hd_bg_color2>\n";
#echo "<td>&nbsp</td>\n";
#echo "<td align=center><font color=$hd_txt_color2><b>Team Name</b></font></td>\n";
#for($i=1; $i<=$num_problems; $i++) { // changes this later
#    echo "<td align=center><font color=$hd_txt_color2>";
#    echo "<b>Prob #$i</b></font></td>";
#}
#echo "<td align=center><font color=$hd_txt_color2><b>Completed</b></font></td>";
#echo "</tr>\n";

$pdf = new Cezpdf();
$pdf =& new Cezpdf();
$pdf->selectFont('./fonts/Helvetica.afm');
$dat = array(
array('num'=>1,'name'=>'gandalf','type'=>'wizard')
,array('num'=>2,'name'=>'bilbo','type'=>'hobbit','url'=>'http://www.ros.co.
nz/pdf/')
,array('num'=>3,'name'=>'frodo','type'=>'hobbit')
,array('num'=>4,'name'=>'saruman','type'=>'bad
dude','url'=>'http://sourceforge.net/projects/pdf-php')
,array('num'=>5,'name'=>'sauron','type'=>'really bad dude')
);
$pdf->ezTable($dat);
$pdf->ezStream();

for($i=0; $i<count($standings); $i++) {
$data[$i]['Rank'] = trim($standings[$i]['rank']);
$data[$i]['Team'] = $standings[$i]['team_name'];
#array('Rank'=>trim($standings[$i]['rank']), 'Team'=>$standings[$i]['team_name']), 
    for($j=1; $j<=$num_problems; $j++) { // change this later
        if(isset($standings[$i]['problems'][$j]['penalty'])) {
	$data[$i][$j] = $standings[$i]['problems'][$j]['penalty'];	
        }
        else {
	$data[$i][$j] = "--";
        }
        $data[$i][$j] += "/";
        if(isset($standings[$i]['problems'][$j]['attempt'])) {
            $data[$i][$j] += $standings[$i]['problems'][$j]['attempt'];
        }
        else {
            $data[$i][$j] += "--";
        }
    }

    if(!isset($standings[$i]['problems_completed']) || $standings[$i]['problems_completed']==0) {
       $data[$i]['Completed'] = "0"; 
    }
    else {
        $data[$i]['Completed'] = $standings[$i]['problems_completed'];
    }
#    echo " ";
    if(!isset($standings[$i]['penalty']) || $standings[$i]['penalty']==0) {
        $data[$i]['Completed'] += "(0)";
    }
    else {
        $data[$i]['Completed'] += "(".$standings[$i]['penalty'].")";
    }

#    echo "</td>\n";
#    echo "</tr>\n";
}
#echo "</table>\n";
#echo "</table>\n";
#echo "<br>\n";
#include("lib/footer.inc");
#$pdf->ezTable($data);
#$pdf->ezStream();
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

