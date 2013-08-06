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
				$error_msg = "Error: " . mysql_error();
				$error_msg = "<br>SQL: $sql";
			}
			else
			{
				if(mysql_num_rows($result)==0)
				{
					$error_msg = "<br>No rows returned: SQL: $sql";
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
				$error_msg = "Error: " . mysql_error();
				$error_msg = "<br>SQL: $sql";
			}
		if(mysql_num_rows($result) > 0)
		{
			$error_msg = "Sorry, there are teams in that site, you must move them to a differant site";
			$error_msg .= " before you can delete this site";
		}
		else
		{
			$sql="delete from SITE where SITE_ID = $remove_id";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
			}
			else
			{
				$error_msg = "Site deleted successfully";
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
				$error_msg = "Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
			}
			else
			{
				unset($_SESSION['edit_site']);
				$error_msg = "Site changed successfully";
			}
		}
		else
		{		
			//adding a new person
			$sql = "INSERT into SITE (SITE_NAME, START_TIME) ";
			$sql .= "values('" . $_POST['site_name'] . "',";
			$sql .= "'" . $_POST['start_time_hours'] . ":" . $_POST['start_time_minutes'] . "')";
			$result = mysql_query($sql);
			if($result)
			{
				$error_msg = "Successfull: New site created";
			}
			else{
				$error_msg = "Error:" . mysql_error();
				$error_msg .= "<br>SQL: $sql";
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
	$action = "Add a new site";
}
$cur_sites = "";
//get all the current categories
$sql = "select * from SITE";
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	$cur_sites = "<a href=setup_site.php><font size=+1>Add New Site</font></a><br>";
	$cur_sites .= "<br><table>";
	$cur_sites .= "<tr><td><font size=+1><b>Edit Current Sites</b></font></td></tr>";
	while($row = mysql_fetch_assoc($result)){
		$cur_sites .= "<tr><td>" . $row['SITE_NAME']; 
		$cur_sites .= " </td><td><font size=-1>";
		$cur_sites .= "<a href=setup_site.php?site_id=" . $row['SITE_ID'] . ">Edit</a>";
		$cur_sites .= "</font></td><td><font size=-1>";
		$cur_sites .= "<a href=setup_site.php?remove_id=" . $row['SITE_ID'] . ">Delete</a>";
		$cur_sites .= "</font><br>\n";
		$cur_sites .= "</td></tr>";
	}
	$cur_sites .= "</table>";
}
else
{
	$cur_sites = "No current categories";
}

//must be a http GET
	echo " <table align=center bgcoloer=#ffffff cellpadding=0 cellspacing=0 border=0 width=100%>";
	echo " <tr><td width=30% valign='top'>";
	echo $cur_sites;
	echo " </td>";
	echo " <td width=50%>";
	echo " <form action=setup_site.php method=post>";
	echo "	<table width=100% cellpadding=5 cellspacing=1 border=0> ";
	if($error_msg)
	{
		echo "<tr><td><b>$error_msg</b></td></tr>";
	}
	else
	{
		echo "<tr><td><b>&nbsp</b></td></tr>";
	}
	echo "	  <tr bgcolor='$hd_bg_color1'> ";
	echo "		<td align='center' colspan=2>";
	echo "			<font color='$hd_txt_color1'>";
	echo "				<b>Add or Edit Sites</b></font>";
	echo "		</td>";
	echo "	  </tr>";
	echo "	  <tr bgcolor=$hd_bg_color2>";
	echo "		<td align='center' colspan=2><font color='$hd_txt_color2'>";
	echo "		<b>$action</b></font></td>";
	echo "	  </tr> ";
	echo "	  <tr bgcolor=\"$data_bg_color1\">";
	echo "		<td>Site name: </td>";
	echo "		<td><input type='text' name='site_name' ";
	echo "			value = '$edit_site_name'></td>";
	echo "	  </tr> ";
	echo "	<tr><td><input name=submit type=submit value='Submit'></td></tr>";
	echo "</form>";
	echo "</td></tr>";
	echo "</table>";
	echo "	</td><td width=20%></td></tr>";
	echo "</table>";
	include("lib/footer.inc");
?>
