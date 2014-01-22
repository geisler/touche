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
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
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
			$error_msg = "<div class='error'>Error: " . mysql_error();
			$error_msg .= "<br>SQL: $sql";
		}
		if($result && mysql_num_rows($result) > 0)
		{
			$error_msg .= "<div class='error'><br>Sorry, there are teams in that category, you must move them to a differant category";
			$error_msg .= " before you can delete this category</div>";
		}
		else
		{
			$sql="delete from CATEGORIES where CATEGORY_ID = $remove_id";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
			}
			else
			{
				$error_msg = "<div class='success'><br>Category deleted successfully</div>";
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
				$error_msg = "<div class='error'>Error: " . mysql_error();
				$error_msg = "<br>SQL: $sql</div>";
			}
			else
			{
				unset($_SESSION['edit_category']);
				$error_msg = "<div class='success'><br>Category changed successfully</div>";
			}
		}
		else
		{		
			//adding a new person
			$sql = "insert into CATEGORIES (CATEGORY_NAME) values('" . $_POST['category_name'] . "')";
			$result = mysql_query($sql);
			if($result)
			{
				$error_msg = "<div class='success'><br>Successfull: New category created</div>";
			}
			else
			{
				$error_msg = "<div class='error'><br>Error:" . mysql_error();
				$error_msg = "<br>SQL: $sql</div>";
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
	
	$cur_categories .= "<tr><td align='center' colspan=3><h3>Pick Category to Edit</h3></td></tr>";
	while($row = mysql_fetch_assoc($result)){
		$cur_categories .= "<tr><td align='center'>" . $row['CATEGORY_NAME']; 
		$cur_categories .= " </td><td align='center'>";
		$cur_categories .= "<a href=setup_categories.php?edit_id=" . $row['CATEGORY_ID'] . ">Edit</a>";
		$cur_categories .= "</td><td align='center'>";
		$cur_categories .= "<a href=setup_categories.php?remove_id=" . $row['CATEGORY_ID'] . ">Delete</a>";
		$cur_categories .= "</td></tr>";

	}
}
else
{
	$cur_categories = "No current categories";
}



//must be a http GET


	//Open Container
	echo " <div class=\"container\">";






	//Add a category
	echo "<div class=\"col-md-5\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo " <form action=setup_categories.php method=post>";

	echo "	  <tr>";
	echo "		<td align='center' colspan='2'>";
	echo "		<h3>$action</h3></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td><input class='form-control' type='text' name='category_name' placeholder='Enter a Category' ";
	echo "			value = '$edit_category_name'></td>";
	echo "	  </tr> ";


	echo "	<tr><td align='center'><button class=\"btn btn-default\" name=submit type=submit value='Submit'>Add Category</button></td></tr>";

	echo "</form>";
	echo "</table>";
	echo "</div>";
	echo "</div>";


	//Edit a category
	echo "<div class=\"col-md-6\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo $cur_categories;
	echo " </table>";
	echo "</div>";
	echo "</div>";
	echo "</div>";

	
//setup team categories	
	
	$link = mysql_connect ($db_host , $db_user, $db_pass) or die ("Could not connect to database");
    mysql_select_db ($db_name) or die ("Could not select database");

    if(isset($_POST["makechanges"]))
    {
    	$sql = "DELETE FROM CATEGORY_TEAM";
		$result = mysql_query($sql);
		if(!$result)
		{
			$error_msg = "<div class='error'>Error: " . mysql_error();
			$error_msg .= "<br>SQL: $sql";
		}
		else
		{
			$error_msg = "<div class='success'><br>Updated successfully</div>";
		}
	
    	foreach($_POST as $box => $value) {
			if($value == 'on') {
				$team = explode("|", $box);
			$sql = "INSERT INTO CATEGORY_TEAM (TEAM_ID, CATEGORY_ID) VALUES ('$team[0]', '$team[1]');";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
			}
			else
			{
				$error_msg = "<div class='success'><br>Updated successfully</div>";
			}
	    }
	}
    }

    $sql = "SELECT * FROM CATEGORIES";
    $category = mysql_query($sql);
    $num_cat = mysql_num_rows($category);
    $cat_row = mysql_fetch_assoc($category);

    $tmp = $num_cat * 10;
    echo " <div class=\"container\">";
    echo " <div class=\"table-responsive\">";
    echo "<form method='POST' action='setup_categories.php'>";
    echo "<div class=\"col-md-11\">";
    echo " <table class=\"table\" align=\"left\" width=100%>";
    echo "<tr><td align='center' colspan=5>";
    echo "<h3>Teams</h3>";
    echo "</td></tr>";
    echo "<tr>";
    echo "<td align='center'><h4>Team Name</h4></td>\n";
    for($i=1; $i<=$num_cat; $i++) { 
	echo "<td align='center'><b>".$cat_row["CATEGORY_NAME"]."</b></td>";
	$cat_row = mysql_fetch_assoc($category);
    }
    echo "</tr>\n";

    $sql = "SELECT * FROM TEAMS";
    $team = mysql_query($sql);
    $num_teams = mysql_num_rows($team);
    $team_row = mysql_fetch_assoc($team);
    
    for($i=0; $i<$num_teams; $i++) {
	if($i%2 == 0) {
	    echo "<tr>\n";
	} else {
	    echo "<tr>\n";
	}
	echo "<td align='center'>".$team_row["TEAM_NAME"]."</td>";

	for($x=1; $x<=$num_cat; $x++) {
	    $sql = "SELECT * FROM CATEGORY_TEAM WHERE TEAM_ID = ".$team_row["TEAM_ID"]." AND CATEGORY_ID=$x";
	    $query = mysql_query($sql);
	    $check = mysql_num_rows($query);
	    
	    echo "<td align='center'><input type='checkbox' ";
	    if($check==1)
	    	echo"checked=checked ";
	    echo "name='".$team_row["TEAM_ID"]."|$x'/></td>";
	}
	
	$team_row = mysql_fetch_assoc($team);
	echo "</tr>";
    }
    echo "<tr>";
    echo "<td align='center' colspan=5>";
    echo "<button class=\"btn btn-default\" type='submit' value='Make Changes' name='makechanges'/>Make Changes</button>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
	
	if($error_msg)
	{
		echo "$error_msg";
	}

    include("lib/footer.inc");
	
?>
