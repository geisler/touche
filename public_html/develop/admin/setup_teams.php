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
# arch-tag: admin/setup_teams.php
#
include_once("lib/admin_config.inc");
include_once("lib/data.inc");
include_once("lib/session.inc");
include_once("lib/header.inc");
if ($_GET)
{
	if(isset($_GET['team_id']))
	{
		$team_id = $_GET['team_id'];
		$_SESSION['edit_team'] = $team_id;
		if($team_id != -1)
		{
			$sql = "select * from TEAMS WHERE TEAM_ID = '$team_id'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg .= "<div class='error'>Error:" . mysql_error();
                $error_msg .= "<br>SQL: $sql</div>";
			}
			else
			{
				if(mysql_num_rows($result)==0)
				{
					$error_msg = "<div class='success'>No rows returned: SQL: $sql</div>";
				}
				else
				{			
					$row = mysql_fetch_assoc($result);
					$edit_team_name = $row['TEAM_NAME'];
					$edit_organization = $row['ORGANIZATION'];
					$edit_username = $row['USERNAME'];
					$edit_password = $row['PASSWORD'];
					$edit_site_id = $row['SITE_ID'];
					$edit_coach_name = $row['COACH_NAME'];
					$edit_contestant_1_name = $row['CONTESTANT_1_NAME'];
					$edit_contestant_2_name = $row['CONTESTANT_2_NAME'];
					$edit_contestant_3_name = $row['CONTESTANT_3_NAME'];
					$edit_alternate_name = $row['ALTERNATE_NAME'];
					$edit_email = $row['EMAIL'];
					$edit_test_team = $row['TEST_TEAM'];
				}
			}
		}
		$action = "Editing team: $edit_team_name";
	}
	else if($_GET['remove_id'] >= 0)
	{
		$remove_id = $_GET['remove_id'];
		$sql="delete from TEAMS where TEAM_ID = $remove_id";
		$result = mysql_query($sql);

		if ($result) {
			$sql = "DELETE FROM CATEGORY_TEAM WHERE TEAM_ID = $remove_id";
			$result2 = mysql_query($sql);
		}

		if(!$result || !$result2)
		{
			$error_msg .= "<div class='error'>Error:" . mysql_error();
            $error_msg .= "<br>SQL: $sql</div>";
		}
		else
		{
			$error_msg = "<div class='success'>Team deleted successfully</div>";
		}
	}
}
else if($_POST)
{
	if(isset($_POST['submit']))
	{
		if(isset($_SESSION['edit_team']))
		{
			$sql = "update TEAMS set TEAM_NAME = '" . $_POST['team_name'] . "', ";
			$sql .= "ORGANIZATION = '" . $_POST['organization'] . "',  ";
			$sql .= "USERNAME = '" . $_POST['username'] . "',  ";
			$sql .= "PASSWORD = '" . $_POST['password'] . "',  ";
			$sql .= "SITE_ID = '" . $_POST['site_id'] . "',  ";
			$sql .= "COACH_NAME = '" . $_POST['coach_name'] . "',  ";
			$sql .= "CONTESTANT_1_NAME = '" . $_POST['contestant_1_name'] . "',  ";
			$sql .= "CONTESTANT_2_NAME = '" . $_POST['contestant_2_name'] . "',  ";
			$sql .= "CONTESTANT_3_NAME = '" . $_POST['contestant_3_name'] . "',  ";
			$sql .= "ALTERNATE_NAME = '" . $_POST['alternate_name'] . "', ";
			$sql .= "EMAIL = '" . $_POST['email'] . "', ";
			$sql .= "TEST_TEAM = '" . $_POST['test_team'];
			$sql .= "' where TEAM_ID = " . $_SESSION['edit_team'];
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg .= "<div class='error'>Error:" . mysql_error();
                $error_msg .= "<br>SQL: $sql</div>";
			}
			else
			{
				unset($_SESSION['edit_team']);
				$error_msg = "<div class='success'>Team changed successfully</div>";
			}
		}
		else
		{		
			//adding a new person
			$sql = "insert into TEAMS (TEAM_NAME, ORGANIZATION, USERNAME, PASSWORD, ";
			$sql .= "SITE_ID, COACH_NAME, CONTESTANT_1_NAME, CONTESTANT_2_NAME, ";
			$sql .= "CONTESTANT_3_NAME, ALTERNATE_NAME, EMAIL, TEST_TEAM) ";
			$sql .= "values('" . $_POST['team_name'] . "', '";
			$sql .= $_POST['organization'] . "', '";
			$sql .= $_POST['username'] . "', '";
			$sql .= $_POST['password'] . "', '";
			$sql .= $_POST['site_id'] . "', '";
			$sql .= $_POST['coach_name'] . "', '";
			$sql .= $_POST['contestant_1_name'] . "', '";
			$sql .= $_POST['contestant_2_name'] . "', '";
			$sql .= $_POST['contestant_3_name'] . "', '";
			$sql .= $_POST['alternate_name'] . "', '";
			$sql .= $_POST['email'] . "', '";
			$sql .= $_POST['test_team'] . "')";
			$result = mysql_query($sql);
			if($result)
			{
				$error_msg = "<div class='success'>Successful: New team created</div>";
			}
			else{
				 $error_msg .= "<div class='error'>Error:" . mysql_error();
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
	$action = "Add a new team";
}

$sql = "select * from SITE";
$result = mysql_query($sql);
if(!$result)
{
	echo "Error: Selecting from Site failed";
	echo "<br>" . mysql_error();
	echo "<br>$sql";
	exit();
}
if(mysql_num_rows($result) == 0)
{
	echo "You need to create a site before you can create any teams";
	exit();
}

$http_sites = "";
while($row = mysql_fetch_assoc($result))
{
	if($row['SITE_ID'] != "1"){
		$http_sites .= "<option value='" . $row['SITE_ID'] . "'"; 
		if($row['SITE_ID'] == $edit_site_id)
		{
			$http_sites .= " selected ";
		}
		$http_sites .= ">";
		$http_sites .= $row['SITE_NAME'];
		$http_sites .= "</option>\n";
	}
}
//-----------------------------------------------------------------------------------------
//$pdf = pdf_new();
//pdf_open_file($pdf, "teams.pdf");
//pdf_set_info($pdf, "Author", "Admin");
//pdf_set_info($pdf, "Title", "Teams");
//pdf_begin_page($pdf, 612, 792);
//$arial = pdf_findfont($pdf, "Arial", "host", 1);
//pdf_setfont($pdf, $arial, 14);
//-----------------------------------------------------------------------------------------
$cur_teams = "";
//get all the current categories
$sql = "select * from TEAMS";
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	//$cur_teams = "<a href=setup_teams.php><font size=+1>Add New Team</font></a><br>";

	while($row = mysql_fetch_assoc($result)){
		if($row['TEST_TEAM']=='1'){
			$isChecked="checked";
		}
		else{
			$isChecked="";
		}
		$cur_teams .= "<tr><td align='center'><input type='checkbox' disabled='disabled' name='show_test_team' value=1 $isChecked>";
		$cur_teams .= "</td><td align='center'>" . $row['TEAM_NAME']; 
		$cur_teams .= " </td><td align='center'>";
		$cur_teams .= "<a href=setup_teams.php?team_id=" . $row['TEAM_ID'] . ">Edit</a>";
		$cur_teams .= "</td><td align='center'>";
		$cur_teams .= "<a href=setup_teams.php?remove_id=" . $row['TEAM_ID'] . ">Delete</a>";
		$cur_teams .= "<br>\n";
		$cur_teams .= "</td></tr>";
//-------------------------------------------------------------------------------------------
//pdf_show_xy($pdf, $row['TEAM_NAME'], 15, 595);
//pdf_show_xy($pdf, $row['PASSWORD'], 15, 580);
//pdf_show_xy($pdf, $row['COACH_NAME'], 15, 565);
//pdf_show_xy($pdf, $row['CONTESTANT_1_NAME'], 15, 550);
//pdf_show_xy($pdf, $row['CONTESTANT_2_NAME'], 15, 535);
//pdf_show_xy($pdf, $row['CONTESTANT_3_NAME'], 15, 520);
//pdf_show_xy($pdf, $row['ALTERNATE_NAME'], 15, 505);
//-------------------------------------------------------------------------------------------
	}
//-------------------------------------------------------------------------------------------
//pdf_end_page($pdf);
//pdf_close($pdf);
//echo "<a href=\"teams.pdf\">PDF Teams</a>";
//-------------------------------------------------------------------------------------------
}
else
{
	$cur_teams = "No current teams";
}

//must be a http GET
	echo " <div class=\"container\">"; // open container





	echo "<div class=\"col-md-6\">";
	echo " <form action=setup_teams.php method=post>";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";


	echo "	  <tr>";
	echo "		<td align='center' colspan=2>";
	echo "		<h3>$action</h3></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Team name: </td>";
	echo "		<td><input class='form-control' type='text' name='team_name' ";
	echo "			value = '$edit_team_name'></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Organization: </td>";
	echo "		<td><input class='form-control' type='text' name='organization' ";
	echo "			value = '$edit_organization'></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Username</td>";
	echo "		<td><input class='form-control' type='text' name='username' ";
	echo "			value = '$edit_username'></td>";
	echo "	  </tr> ";
//-----------------------------------------------------------------------------------------------
if(!$edit_password) {
	srand((double)microtime()*1000000);
	$token = md5(uniqid(rand()));
	$edit_password = substr($token, 0, 3);
	$edit_password .= substr($token, 5, 2);
	$edit_password .= substr($token, -3);
}
//-----------------------------------------------------------------------------------------------
	echo "	  <tr>";
	echo "		<td align='right'>Password: </td>";
	echo "		<td><input class='form-control' type='text' name='password' ";
	echo "			value = '$edit_password'></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Site:</td>";
	echo "		<td><select name=site_id> ";
	echo "			$http_sites</select></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Coach: </td>";
	echo "		<td align='right'><input class='form-control' type='text' name='coach_name' ";
	echo "			value = '$edit_coach_name'></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Contestant 1: </td>";
	echo "		<td><input class='form-control' type='text' name='contestant_1_name' ";
	echo "			value = '$edit_contestant_1_name'></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Contestant 2: </td>";
	echo "		<td><input class='form-control' type='text' name='contestant_2_name' ";
	echo "			value = '$edit_contestant_2_name'></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Contestant 3: </td>";
	echo "		<td><input class='form-control' type='text' name='contestant_3_name' ";
	echo "			value = '$edit_contestant_3_name'></td>";
	echo "	  </tr> ";

	echo "	  <tr>";
	echo "		<td align='right'>Alternate (leave empty if none): </td>";
	echo "		<td><input class='form-control' type='text' name='alternate_name' ";
	echo "			value = '$edit_alternate_name'></td>";
	echo "	  </tr> ";

	echo "    <tr>";
    echo "          <td align='right'>Email: </td>";
    echo "          <td><input class='form-control' type='text' name='email' ";
    echo "                  value = '$edit_email'></td>";
    echo "    </tr> ";
	echo "    <tr>";
/*		$sql = "select * from TEAMS WHERE TEAM_ID = $team_id";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		if($row['TEST_TEAM']=='1'){
			$isChecked="checked";
		}
		else{
			$isChecked="";
		}*/
    echo "          <td align='right'>Test Team: </td>";
    echo "          <td><input type='checkbox' name='test_team' value=1";

    echo "                  value = '$edit_test_team'></td>";
    echo "    </tr> ";


	echo "	<tr><td colspan=2 align='center'><button type=\"submit\" class=\"btn btn-default\" name=\"submit\">Submit</button>";
	
	echo "</td></tr>";
	echo "</table>";
	echo "</div>"; //close column
	echo "</div>"; //close responsive


	echo "<div class=\"col-md-5\">"; //open col

	echo " <div class=\"table-responsive\">"; //open responsive
	echo " <table class=\"table\" align=\"left\"  width=100%>";

	echo "<tr><td align='center' colspan=4><h3>Edit Current Teams</h3></td></tr>";

	echo "	  <tr>";
	echo "		<td align='center'><h4>Test Team?</h4></td>";
	echo "		<td align='center'><h4>Name</h4></td>";
	echo "		<td align='center'><h4>Edit</h4></td>";
	echo "		<td align='center'><h4>Delete</h4></td>";
	echo "	  </tr> ";


	echo "<tr>";
	echo $cur_teams;
	echo "</tr>";
	echo "</table>";
	echo "</div>"; //close responsive

	if($error_msg)
	{
		echo "$error_msg";
	}

	echo "</div>"; //close col


	
	echo "</form>"; // close form
	echo "</div>"; //close container
	include("lib/footer.inc");
?>
