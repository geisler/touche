<?
#
# Copyright (C) 2013 Jonathan Geisler
# Copyright (C) 2005 David Crim
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/start.php
#
include_once("lib/config.inc");
include_once("lib/judge.inc");
include_once("lib/header.inc");

judge_header(0);

function make_file_readable($fp, $missing_files) {
	if (file_exists($fp)) {
		chmod($fp, 0755);
	} else {
		++$missing_files;
	}
}

if($_POST['submit'] == 'Start')
{
	$cur_hour = date(G);
	$cur_minute = date(i);
	$cur_second = date(s);
#	system("crontab $base_dir/start_contest.crontab", $result);
	system("touch $base_dir/../active-contests/$contest_name", $result);
        if ($result != 0){
                echo "<p><font color=$hd_txt_color2>Warning! Crontab Failed to start, please contact the system administrator</font></p>";
        }

	foreach($_POST['chksite'] as $site)
	{
		if($site == 'contest')
		{
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
			echo "Warning: no problem file for " . $row['PROBLEM_NAME'] . "<br />";
		}
        }
}
	//		print "cur hour: $cur_hour cur minute: $cur_minute cur second: $cur_second";
			$sql = "UPDATE CONTEST_CONFIG set START_TIME = '$cur_hour:$cur_minute:$cur_second'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "Grevious error: update failed: " . mysql_error() . "\n<br>$sql";
			}
			$sql = "UPDATE CONTEST_CONFIG set START_TS = '" . time() . "'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "Grevious error: update failed: " . mysql_error() . "\n<br>$sql";
			}
			$sql = "UPDATE CONTEST_CONFIG set HAS_STARTED = '1'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "Grevious error: update failed: " . mysql_error() . "\n<br>$sql";
			}
		}
		else
		{
			$sql = "UPDATE SITE set START_TIME = '$cur_hour:$cur_minute:$cur_second' WHERE SITE_ID = '$site'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "Grevious error: update failed: " . mysql_error() . "\n<br>$sql";
			}
			$sql = "UPDATE SITE set START_TS = '" . time() . "' WHERE SITE_ID = '$site'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "Grevious error: update failed: " . mysql_error() . "\n<br>$sql";
			}
			$sql = "UPDATE SITE set HAS_STARTED = '1' WHERE SITE_ID = '$site'";
			$result = mysql_query($sql);
			if(!$result)
			{
				print "Grevious error: update failed: " . mysql_error() . "\n<br>$sql";
			}
		}
	}
}

echo "<form action=start.php method=post>";
echo "<table align=center width=60% cellpadding=5 cellspacing=1 border=0>\n";
echo "<tr><td colspan=2 align=center bgcolor=$hd_bg_color1>\n";
echo "<font color=$hd_txt_color1><b>Start Contest</b></font></td></tr>\n";
echo "<tr><td bgcolor=$hd_bg_color2>Start contest</td><td bgcolor=$hd_bg_color2 align=center>";
//$cur_time = time();
if(!$contest_started)
{
	echo "<input type=checkbox name=chksite[] value='contest'>";
}
else
{
	echo "<input type=checkbox name=chksite[] value='contest' disabled checked>";
//	$contest_started = true;
}

echo "</td></tr>";

$sql = "SELECT * FROM SITE";
$result = mysql_query($sql);
if(!$result)
{
	echo "<tr><td bgcolor=$hd_bg_color2>SELECT from SITE table failed</tr></td>";
}

while($row = mysql_fetch_assoc($result))
{
	echo "<tr><td bgcolor=$data_bg_color1>" . $row['SITE_NAME'];
	echo "</td><td align=center bgcolor=$data_bg_color1>";
	if(!$contest_started)
	{	
		echo "<input type=checkbox name=chksite[] value='" . $row['SITE_ID'] . "' disabled></td></tr>";
	}
	else
	{
		$site_started = $row['HAS_STARTED'];
		if(!$site_started)
		{
			echo "<input type=checkbox name=chksite[] value='" . $row['SITE_ID'] . "' ></td></tr>";
		}
		else
		{
			echo "<input type=checkbox name=chksite[] value='" . $row['SITE_ID'] . "' disabled checked></td></tr>";
		}
	}
}
echo "<tr><td>&nbsp;</td><td align=center><input type=submit name=submit value=Start></form></td></tr>";
echo "</table>\n";
	
    include("lib/footer.inc");
?>
