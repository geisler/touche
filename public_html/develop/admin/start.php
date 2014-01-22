<?
#
# Copyright (C) 2013 Jonathan Geisler
# Copyright (C) 2005 David Crim
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/start.php
#

include_once("lib/header.inc");
include_once("lib/judge.inc");

function make_file_readable($fp, $missing_files) {
	if (file_exists($fp)) {
		chmod($fp, 0755);
	} else {
		++$missing_files;
	}
}

if($_POST['submit'] == 'Start' || $_POST['test_submit'] == 'Test Start')
{
/* code for changing type of team to the contest state, move as needed or delete
	if($_POST['submit'] == 'Start'){
		$sql = "SELECT * FROM TEAMS";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0){
			while($row=mysql_fetch_assoc($result)){
				if($row['TEST_TEAM']==0){
					$row['TEST_TEAM']=1;
					$sql = "update TEAMS set TEST_TEAM = '" . $row['TEST_TEAM'];
					$sql .= "' where TEAM_ID = " . $row['TEAM_ID'];
					mysql_query($sql);
				}
			}
		}
		else{
			echo "error with sql.";
		}
	}*/
	$cur_hour = date(G);
	$cur_minute = date(i);
	$cur_second = date(s);
#	system("crontab $base_dir/start_contest.crontab", $result);
	system("touch $base_dir/../active-contests/$contest_name", $result);
        if ($result != 0){
                echo "<div class='error'>Warning! Crontab Failed to start, please contact the system administrator</div>";
        }
	
	foreach($_POST['chksite'] as $site)
	{
//		if($site == 'contest') for master start page
//		{
			$contest_started = true;
			
			//set permissions for html and pdf files
			$sql = "SELECT * FROM PROBLEMS";
			$result = mysql_query($sql);
			if(mysql_num_rows($result) > 0) { 
				while($row = mysql_fetch_assoc($result)){
					$dir_name = "../problems/" . $row['PROBLEM_LOC'];
					chmod($dir_name, 0755);

					$missing_files = 0;
					$problem_name = "$dir_name/" . $row['PROBLEM_NAME'];
					make_file_readable("$problem_name.html", &$missing_files);
					make_file_readable("$problem_name.ps", &$missing_files);
					make_file_readable("$problem_name.pdf", &$missing_files);
					if ($missing_files == 3) {
						echo "<div class='error'><br>Warning: no problem file for " . $row['PROBLEM_NAME'] . "<br /></div>";
					}
				}
			}
			//print "cur hour: $cur_hour cur minute: $cur_minute cur second: $cur_second";
			$sql = "UPDATE SITE set START_TIME = '$cur_hour:$cur_minute:$cur_second' WHERE SITE_ID = 1";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
			}
			$sql = "UPDATE SITE set START_TS = '" . time() . "' WHERE SITE_ID = 1";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
			}
/*			
			if($_POST['test_submit'] == 'Test Start'){
				$sql = "UPDATE SITE WHERE SITE_ID = 1 set HAS_STARTED = '2'";
				$result = mysql_query($sql);
				if(!$result)
				{
					print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
				}
			}
			
			if($_POST['submit'] == 'Start'){
				$sql = "UPDATE SITE set HAS_STARTED = '1' WHERE SITE_ID = 1";
				$result = mysql_query($sql);
				if(!$result)
				{
					print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
				}
			}
			
*/			
//		}
//		else
//		{
			$sql = "UPDATE SITE set START_TIME = '$cur_hour:$cur_minute:$cur_second' WHERE SITE_ID = '$site'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
			}
			$sql = "UPDATE SITE set START_TS = '" . time() . "' WHERE SITE_ID = '$site'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
			}
			
			if($_POST['test_submit'] == 'Test Start'){
			$sql = "UPDATE SITE set HAS_STARTED = '2' WHERE SITE_ID = '$site'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
			}
			}
			else{
			$sql = "UPDATE SITE set HAS_STARTED = '1' WHERE SITE_ID = '$site'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "<div class='error'><br>Grevious error: update failed: " . mysql_error() . "\n<br>$sql</div>";
			}
			}
//		}
	}
}


echo "<form action=start.php method=post>";



echo "<div class='table-responsive'>";
echo "<table class='table' align=center width=100%>\n";
echo "<tr><td colspan=2 align='center'>\n";
echo "<h3>Start Contest</h3></td></tr>\n";
/*
echo "<tr><td align='right'>Start contest</td><td>";

//$cur_time = time();


if(!$contest_started)
{
	echo "<input type=checkbox name=chksite[] value='contest'>";
}
else
{
	echo "<input type=checkbox name=chksite[] value='contest' disabled checked>";
	echo "<div class='success'><br>Contest has started!</div>";
	//$contest_started = true;
}

echo "</td></tr>";
*/
$sql = "SELECT * FROM SITE";
$result = mysql_query($sql);
if(!$result)
{
	echo "<div class='success'><br><tr><td>SELECT from SITE table failed</tr></td></div>";
}

while($row = mysql_fetch_assoc($result))
{
	if($row['SITE_ID']!='1'){
		echo "<tr><td align = right>" . $row['SITE_NAME'];
		echo "</td><td>";
		
		$site_started = $row['HAS_STARTED'];
		if($site_started!=1)
		{
			$test_running = "";
			if($site_started==2){
				$test_running = "Running Test";
			}
			echo "<input type=checkbox name=chksite[] value='" . $row['SITE_ID'] . "' > <font color='red'>$test_running</td></tr>";
		}
	}
}

echo "<tr><td align=center><button class=\"btn btn-success\" type=submit name=submit value=Start>Official Start</button></td>";
echo "<td align=center><button class=\"btn btn-warning\" type=submit name=test_submit value='Test Start'>Test Start</button></td></tr>";


echo "</table>\n";
echo "</div>";

	
    include("lib/footer.inc");
?>
