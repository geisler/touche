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
# arch-tag: admin/setup_categories.php
#
include_once("lib/admin_config.inc");
include_once("lib/data.inc");
include_once("lib/session.inc");
include_once("lib/header.inc");
if ($_GET)
{
	if(isset($_GET['edit_id']))
	{
		$edit_id = $_GET['edit_id'];
		$_SESSION['edit_category'] = $edit_id;
		if($edit_id != -1)
		{
			$sql = "select * from CATEGORIES WHERE CATEGORY_ID = '$edit_id'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
			}
			else
			{
				$row = mysql_fetch_assoc($result);
				$edit_category_name = $row['CATEGORY_NAME'];
			}
		}
		$action = "Editing category: $edit_category_name";
	}
	else if(isset($_GET['remove_id']))
	{
		$remove_id = $_GET['remove_id'];
		//delete the offending category if no teams are in it
		$sql = "select * from TEAMS, CATEGORY_TEAM where TEAMS.TEAM_ID ";
		$sql .= "= CATEGORY_TEAM.TEAM_ID AND CATEGORY_TEAM.CATEGORY_ID = $remove_id";
		$result = mysql_query($sql);
		if(!$result)
		{
			$error_msg = "Error: " . mysql_error();
			$error_msg .= "<br>SQL: $sql";
		}
		if($result && mysql_num_rows($result) > 0)
		{
			$error_msg .= "Sorry, there are teams in that category, you must move them to a differant category";
			$error_msg .= " before you can delete this category";
		}
		else
		{
			$sql="delete from CATEGORIES where CATEGORY_ID = $remove_id";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
			}
			else
			{
				$error_msg = "Category deleted successfully";
			}
		}
		
			
	}
}
else if($_POST)
{
	if($_POST['submit'])
	{
		if(isset($_SESSION['edit_category']))
		{
			$sql = "update CATEGORIES set CATEGORY_NAME = '" . $_POST['category_name'];
			$sql .= "' where CATEGORY_ID = " . $_SESSION['edit_category'];
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "Error: " . mysql_error();
				$error_msg = "<br>SQL: $sql";
			}
			else
			{
				unset($_SESSION['edit_category']);
				$error_msg = "Category changed successfully";
			}
		}
		else
		{		
			//adding a new person
			$sql = "insert into CATEGORIES (CATEGORY_NAME) values('" . $_POST['category_name'] . "')";
			$result = mysql_query($sql);
			if($result)
			{
				$error_msg = "Successfull: New category created";
			}
			else
			{
				$error_msg = "Error:" . mysql_error();
				$error_msg = "<br>SQL: $sql";
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
	$action = "Add a new category";
}
$cur_categories = "";
//get all the current categories
$sql = "select * from CATEGORIES";
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	$cur_categories = "<a href=setup_categories.php><font size=+1>Add New Category</font></a><br>";
	$cur_categories .= "<br><table>";
	$cur_categories .= "<tr><td><font size=+1><b>Edit Current Categories</b></font></td></tr>";
	while($row = mysql_fetch_assoc($result)){
		$cur_categories .= "<tr><td>" . $row['CATEGORY_NAME']; 
		$cur_categories .= " </td><td><font size=-1>";
		$cur_categories .= "<a href=setup_categories.php?edit_id=" . $row['CATEGORY_ID'] . ">Edit</a>";
		$cur_categories .= "</font></td><td><font size=-1>";
		$cur_categories .= "<a href=setup_categories.php?remove_id=" . $row['CATEGORY_ID'] . ">Delete</a>";
		$cur_categories .= "</font><br>\n";
		$cur_categories .= "</td></tr>";
	}
	$cur_categories .= "</table>";
}
else
{
	$cur_categories = "No current categories";
}

//must be a http GET
	echo " <table align=center bgcoloer=#ffffff cellpadding=0 cellspacing=0 border=0 width=100%>";
	echo " <tr><td width=30% valign='top'>";
	echo $cur_categories;
	echo " </td>";
	echo " <td width=50%>";
	echo " <form action=setup_categories.php method=post>";
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
	echo "				<b>Add or Edit Categories</b></font>";
	echo "		</td>";
	echo "	  </tr>";
	echo "	  <tr bgcolor=$hd_bg_color2>";
	echo "		<td align='center' colspan=2><font color='$hd_txt_color2'>";
	echo "		<b>$action</b></font></td>";
	echo "	  </tr> ";
	echo "	  <tr bgcolor=\"$data_bg_color1\">";
	echo "		<td>Category name: </td>";
	echo "		<td><input type='text' name='category_name' ";
	echo "			value = '$edit_category_name'></td>";
	echo "	  </tr> ";
	echo "	<tr><td><input name=submit type=submit value='Submit'></td></tr>";
	echo "</form>";
	echo "</td></tr>";
	echo "</table>";
	echo "	</td><td width=20%></td></tr>";
	echo "</table>";
	include("lib/footer.inc");
?>
