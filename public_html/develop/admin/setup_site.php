<?php
#
# Copyright (C) 2003 David Crim
# Copyright (C) 2003 David Whittington
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Jonathan Geisler
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/setup_site.php
#
include_once("lib/admin_config.inc");
include_once("lib/data.inc");
include_once("lib/session.inc");
include_once("lib/header.inc");
if ($_GET)
{
	if(isset($_GET['site_id']))
	{
		$site_id = $_GET['site_id'];
		$_SESSION['edit_site'] = $site_id;
		if($site_id != -1)
		{
			$sql = "select * from SITE WHERE SITE_ID = '$site_id'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg = "<br>SQL: $sql</div>";
			}
			else
			{
				if(mysql_num_rows($result)==0)
				{
					$error_msg = "<div class='error'><br>No rows returned: SQL: $sql</div>";
				}
				else
				{			
					$row = mysql_fetch_assoc($result);
					$edit_site_name = $row['SITE_NAME'];
				}
			}
		}
		$action = "Editing site: $edit_site_name";
	}
	else if(isset($_GET['remove_id']))
	{
		$remove_id = $_GET['remove_id'];
		//delete the offending site if no teams are in it
		$sql = "select * from TEAMS where SITE_ID = $remove_id";
		$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg = "<br>SQL: $sql</div>";
			}
		if(mysql_num_rows($result) > 0)
		{
			$error_msg = "<div class='error'><br>Sorry, there are teams in that site, you must move them to a different site";
			$error_msg .= " before you can delete this site.</div>";
		}
		else
		{
			$sql="delete from SITE where SITE_ID = $remove_id";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
			else
			{
				$error_msg = "<div class='success'><br>Site deleted successfully</div>";
			}
		}
		
			
	}
}
else if($_POST)
{
	if($_POST['submit'])
	{
		if(isset($_SESSION['edit_site']))
		{
			$sql = "update SITE set SITE_NAME = '" . $_POST['site_name'];
			$sql .= "' where SITE_ID = " . $_SESSION['edit_site'];
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
			else
			{
				unset($_SESSION['edit_site']);
				$error_msg = "<div class='success'><br>Site changed successfully</div>";
			}
		}
		else
		{		
			//adding a new person
			$sql = "INSERT into SITE (SITE_NAME, START_TIME) ";
			$sql .= "values('" . $_POST['site_name'] . "',";
			$sql .= "'" . $_POST['start_time_hours'] . ":" . $_POST['start_time_minutes'] . "')";
			mysql_query($sql);			
			$sql = "select * from SITE";
			$bigresult = mysql_query($sql);
			mysql_fetch_assoc($bigresult);
			$contest_host=$row['CONTEST_HOST'];
			$contest_name=$row['CONTEST_NAME'];
			$freeze_delay=$row['FREEZE_DELAY'];
			$contest_end_delay=$row['CONTEST_END_DELAY'];
			$base_dir=$row['BASE_DIRECTORY'];
			$num_problems=$row['NUM_PROBLEMS'];
			$ignore_stderr=$row['IGNORE_STDERR'];
			$judge_user=$row['JUDGE_USER'];
			$judge_pass=$row['JUDGE_PASS'];
			$team_show=$row['TEAM_SHOW'];
			$time_penalty=$row['TIME_PENALTY'];
			while($row = mysql_fetch_assoc($bigresult)){
				$sql = "update SITE set CONTEST_HOST = '$contest_host', ";
				$sql .= "CONTEST_NAME = '$contest_name', FREEZE_DELAY = $freeze_delay, ";
				$sql .= "CONTEST_END_DELAY = $contest_end_delay, BASE_DIRECTORY = '$base_dir', ";
				$sql .= "NUM_PROBLEMS = $num_problems, IGNORE_STDERR = $ignore_stderr, ";
				$sql .= "JUDGE_USER = '$judge_user', JUDGE_PASS = '$judge_pass', ";
				$sql .= "TEAM_SHOW = $team_show, TIME_PENALTY = $time_penalty ";
				$sql .= "where SITE_ID = '" . $row['SITE_ID'] . "'";
				$result = mysql_query($sql);
				if(!$result){
					echo "bad update";
				}
			}
			

			if($result)
			{
				$error_msg = "<div class='success'><br>Successfull: New site created</div>";
			}
			else{
				$error_msg = "<div class='error'><br>Error:" . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
		}
	}
}
/*******************************************************
End of POST section
*******************************************************/
//build some http strings we'll need later
if(!$action)
{
	$action = "<h3>Add a new site</h3>";
}
$cur_sites = "";
//get all the current categories
$sql = "select * from SITE";
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	while($row = mysql_fetch_assoc($result)){
		if($row['SITE_ID'] != '1'){
			$cur_sites .= "<tr><td>" . $row['SITE_NAME']; 
			$cur_sites .= " </td><td>";
			$cur_sites .= "<a href=setup_site.php?site_id=" . $row['SITE_ID'] . ">Edit</a>";
			$cur_sites .= "</td><td>";
			$cur_sites .= "<a href=setup_site.php?remove_id=" . $row['SITE_ID'] . ">Delete</a>";
			$cur_sites .= "<br>\n";
			$cur_sites .= "</td></tr>";
		}

	}
	$cur_sites .= "</table>";
}
else
{
	$cur_sites = "No current categories";
}

//must be a http GET

	
	echo " <div class=\"container\">";


	//Table for Adding a Site
	echo "<div class=\"col-md-5\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo " <form action=setup_site.php method=post>";
	echo "<td align='center' colspan='2' >";
	echo " <h3>$action</h3>";
	echo "</td>";
	echo " <tr>";
	echo " <td><input class='form-control' type='text' name='site_name' placeholder='Site Name' ";
	echo " value = '$edit_site_name'></td>";
	echo " </tr> ";
	echo " <tr><td align='center' colspan=2><button class=\"btn btn-default\" name=submit type=submit value='Submit'>Add Site</button></td></tr>";
	echo " </form>";
	echo " </td></tr>";

	if($error_msg)
	{
		echo "$error_msg";
	}


	echo " </table>";
	echo " </div>";
	echo " </div>";


	//table for Editing a Site
	echo "<div class=\"col-md-6\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo "<tr>";
	echo "<td align='center' colspan=3>";
	echo " <h3>Edit a Site</h3>";
	echo $cur_sites;
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "</div>";
	echo " </div>";


	include("lib/footer.inc");
?>
