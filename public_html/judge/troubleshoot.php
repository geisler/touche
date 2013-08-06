<?
#
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/troubleshoot.php
#
    include_once("lib/config.inc");
    include_once("lib/judge.inc");
    include_once("lib/header.inc");

echo "<br><table align=center bgcolor=#000000 width=90% cellpadding=0 
    cellspacing=0 border=0><tr><td>\n";
echo "<table align=center width=100% cellpadding=5 cellspacing=1 
    border=0>\n";
echo "<tr><td colspan=3 align=center bgcolor=$hd_bg_color1>\n";
echo "<font color=$hd_txt_color1><b>Troubleshooting</b></font></td></tr>\n";

echo "<form name=troubleshoot method='post' action='troubleshoot.php'>";
echo "<tr><td width=10% bgcolor=$hd_bg_color2>Team:</td>";
echo "<td width=25% bgcolor=$data_bg_color1>";
echo "<select name='team'>";
    foreach($teams as $team) {
	echo "<option value=$team[id]>";
	echo "$team[id] - $team[name]</option>";
    }
echo "</td><td bgcolor=$data_bg_color1>";
echo "All Teams?<input type=checkbox name=team_all></input>";
echo "</td></tr>";
echo "<tr><td bgcolor=$hd_bg_color2>Problem:</td>";
echo "<td bgcolor=$data_bg_color1>";
echo "<select name='prob'>";
    foreach($problems as $problem) {
	echo "<option value=$problem[id]>";
	echo "$problem[id] - $problem[name]</option>";
    }
echo "</td><td bgcolor=$data_bg_color1>";
echo "All Problems?<input type=checkbox name=problem_all></input>";
echo "</td></tr>";
echo "<tr><td bgcolor=$data_bg_color1></td>";
echo "<td bgcolor=$data_bg_color1><center>";
echo "<input type=submit name=submit value='Submit'></input>";
echo "</center></td>";
echo "<td bgcolor=$data_bg_color1></td></tr></form>";

if($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $team_id = $_POST['team'];
    $problem_id = $_POST['prob'];
    if(isset($_POST['team_all'])) {
	$all_team = $_POST['team_all'];
    }
    if(isset($_POST['problem_all'])) {
	$all_problems = $_POST['problem_all'];
    }
    
    $i = 0;
    foreach ($teams as $team) {
	if(isset($all_team) || $team['id'] == $team_id) {
	    $trouble[$i]['team_id'] = $team['id'];
	    $trouble[$i]['team_name'] = $team['name'];
    
	    echo "<tr><td colspan=3 align = center bgcolor=$hd_bg_color2>";
	    echo "Team: " . $trouble[$i]['team_id'];
	    echo "</td></tr>";
	    echo "<tr><td bgcolor=$data_bg_color1>"; 
	    echo "Team Name:</td><td bgcolor=$data_bg_color1>";
	    echo $trouble[$i]['team_name'];
	    echo "</td><td bgcolor=$data_bg_color1></td></tr>";
	
    	    foreach ($problems as $problem) {
    		$sql  = "SELECT * ";
    		$sql .= "FROM JUDGED_SUBMISSIONS ";
    		$sql .= "WHERE TEAM_ID = $team[id] ";
    		$sql .= "AND PROBLEM_ID = $problem[id] ";
    		$result = mysql_query($sql);
    		
    		$problem_header = 0;
    		do{
    		    if($row = mysql_fetch_assoc($result)) {
    			if(isset($all_problems) 
			    || $problem_id == $row['PROBLEM_ID']) {
    			
			    if(!$problem_header) {
				echo "<tr><td bgcolor=$hd_bg_color1>";
				echo "Problem:</td><td bgcolor=$data_bg_color1>";
				echo $problem['id'];
				echo "</td><td bgcolor=$data_bg_color1></td></tr>";
				$problem_header = 1;
			    }
			    # Attempt
			    echo "<tr><td bgcolor=$hd_bg_color2>";
			    echo "Attempt:</td><td bgcolor=$data_bg_color1>";
			    echo $row['ATTEMPT'];
			    echo "</td><td bgcolor=$data_bg_color1></td></tr>";
	
	    		    # Source code
	    		    echo "<tr><td bgcolor=$data_bg_color1></td>\n";
	    		    echo "<td bgcolor=$data_bg_color1>Source Code:</td>\n";
	    		    echo "<td bgcolor=$data_bg_color1>";
	    		    echo "<a href='judge_output.php?problem=$problem[id]&sub_source=$row[SOURCE_FILE]&format=2' target='blank'>";
	    		    echo $base_dir . "/judged/" . $row['SOURCE_FILE'] . "</a></td>\n";
	    		    echo "</td></tr>\n";
	    		    
	    		    # Output files
	    		    $tmp = explode(".", $row['SOURCE_FILE']);
	    		    $file_name = $tmp[0];
	    		    $output_files = glob($base_dir . "/judged/" . $file_name 
	    			. "_" . $problem['id'] . "*.out");
	    		    foreach($output_files as $out) {
	    			echo "<tr><td bgcolor=$data_bg_color1></td>\n";
	    			echo "<td bgcolor=$data_bg_color1>Output File:</td>\n";
	    			echo "<td bgcolor=$data_bg_color1>";
	    			echo "<a href='judge_output.php?problem=$problem[id]&sub_source=$out&format=3' target='blank'>";
	    			echo "$out</a></td>\n";
	    			echo "</td></tr>\n";
	    		    }
	    		    
	    		    # Diff files
	    		    $diff_files = glob($base_dir . "/judged/" . $file_name 
	    			. "_" . $problem['id'] . "*.out.diff");
	    		    foreach($diff_files as $diff) {
	    			echo "<tr><td bgcolor=$data_bg_color1></td>";
	    			echo "<td bgcolor=$data_bg_color1>Diff File</td>";
	    			echo "<td bgcolor=$data_bg_color1>";
	    			echo "<a href='judge_output.php?problem=$problem[id]&sub_source=$diff&format=3' target='blank'>";
	    			echo $diff . "</td></tr>";
	    		    }
			}		
    		    }
		}while($row);
	    }
	}
	$i++;
    }
}
?>
