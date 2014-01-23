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
# arch-tag: admin/setup_data_sets.php
#
include_once("lib/admin_config.inc");
include_once("lib/data.inc");
include_once("lib/session.inc");
include_once("lib/contest_info.inc");

$data_dir = $base_dir . "/data/";

if($_POST)
{
	if($_POST['submit'])
	{
		$problem_id = $_POST['problem_id'];
		$_SESSION['edit_problem'] = $problem_id;
		$error_msg = "<div class='success'><br>Successful: New data set created</div>";
		$sql = "select * from PROBLEMS WHERE PROBLEM_ID = '$problem_id'";
		$result = mysql_query($sql);
		if(!$result)
		{
			$error_msg = "<div class='error'><br>Error: " . mysql_error();
			$error_msg .= "<br>SQL: $sql</div>";
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
				$edit_problem_name = $row['PROBLEM_NAME'];
			}
		}
		$action = "Adding data set for problem: $edit_problem_name";
		//adding a new data set
		


		//print "Filename: " . $_FILES['data_set_in']['name'];

		if(!preg_match("/\.in$/", $_FILES['data_set_in']['name']))
		{
			//print "Adding in suffix";
			$in_suffix = ".in";
			$out_file_name = $_FILES['data_set_in']['name']. ".out";
		}
		else
		{
			//print "No suffix added";
			$in_suffix = "";
			$out_file_name = preg_replace("/\.in$/", "", $_FILES['data_set_in']['name']) . ".out";
		}
		
		$result = move_uploaded_file($_FILES['data_set_in']['tmp_name'], 
				$data_dir . $_POST['problem_id'] . "_" . $_FILES['data_set_in']['name'] . $in_suffix);
		if(!$result)
		{
			//print "Failed to upload in file";
			$error_msg = "<div class='error'><br>Failed to upload 'in' file</div>";
		} else {
			//dos2unix to fix any issues that Microsoft(bleh) could have caused. -TG
			//echo "dos2unix on " . $data_dir . $_POST['problem_id'] . "_" . $_FILES['data_set_in']['name'] . $in_suffix;
			$cmd = "dos2unix " . $data_dir . $_POST['problem_id'] . "_" . $_FILES['data_set_in']['name'] . $in_suffix;
			system($cmd,$result);
			//echo $result . "<br/>";
		}
		
		//copy over the destination out file name so when we glob the directory later
		//we can search for pars of input/output files with the same name
		
		$result = move_uploaded_file($_FILES['data_set_out']['tmp_name'], 
				$data_dir . $_POST['problem_id'] . "_" . $out_file_name);
		if(!$result)
		{
			//print "Failed to upload out file";
			$error_msg = "<div class='error'><br>Failed to upload 'out' file</div>";
		} else {
			//dos2unix to fix any issues that Microsoft(bleh) could have caused. -TG
			//echo "dos2unix on " . $data_dir . $_POST['problem_id'] . "_" . $out_file_name;
			$cmd = "dos2unix " . $data_dir . $_POST['problem_id'] . "_" . $out_file_name;
			system($cmd,$result);
			//echo $result . "<br/>";
		}
	}

}
else if ($_GET)
{
	if(isset($_GET['problem_id']))
	{
		$problem_id = $_GET['problem_id'];
		$_SESSION['edit_problem'] = $problem_id;
		if($problem_id != -1)
		{
			$sql = "select * from PROBLEMS WHERE PROBLEM_ID = '$problem_id'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
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
					$edit_problem_name = $row['PROBLEM_NAME'];
				}
			}
		}
		$action = "<h3>$edit_problem_name</h3>";
	}
	else if(isset($_GET['remove_ds_name']))
	{
		//delete the problem 
		$remove_ds_name = $_GET['remove_ds_name'];
		$remove_ds_name = $data_dir . $remove_ds_name;
		$result = unlink($remove_ds_name . ".in") && unlink($remove_ds_name . ".out");
		if(!$result)
		{
			$error_msg = "<div class='error'><br>Error deleting data set $remove_ds_name</div>";
		}
		else
		{
			$error_msg = "<div class='success'><br>Data set deleted successfully</div>";
		}
	}
}

/*******************************************************
End of POST section
*******************************************************/
//build some http strings we'll need later

include_once("lib/header.inc");


if(!$action)
{
	$action = "Add a new data set";
}
$cur_data_sets = "";
//get all the current data sets
$sql = "select * from PROBLEMS ORDER BY 'PROBLEM_ID'";
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	$cur_data_sets .= "<tr><td align='center' colspan='2'><h3>Edit Current Data Sets</h3></td></tr>";
	while($row = mysql_fetch_assoc($result)){
		$cur_data_sets .= "<tr><td align='center'>" . $row['PROBLEM_NAME']; 
		$cur_data_sets .= "</td><td align='center'><a href=setup_data_sets.php?problem_id";
		$cur_data_sets .= "=" . $row['PROBLEM_ID'] . ">Add new data set</a></td></tr>";
		$fs_parse = glob($data_dir . $row['PROBLEM_ID'] . "*in");
		foreach ($fs_parse as $file)
		{
			$file_names = split("/", $file);
			$file_name = $file_names[count($file_names)-1];
			$data_set_name = preg_replace("/\.in$/", "",$file_name);
			$cur_data_sets .= "<tr><td align='right'> $data_set_name</td>";
			$cur_data_sets .= "<td align='center'><a href=setup_data_sets.php";
			$cur_data_sets .= "?remove_ds_name=$data_set_name>Delete</a></td></tr>";
		}
		$cur_data_sets .="</td></tr>";
	}
}
else
{
	$cur_data_sets = "No current data sets";
}

if(isset($_GET['problem_id']) || isset($_POST['problem_id']))
{
	$problem_id = $_GET['problem_id'];
	$http_form .=  "	  <tr>";
	$http_form .=  "		<td align='center'>Input File: </td>";
	$http_form .=  "		<td align='center'><input type='file' name='data_set_in'</td>";
	$http_form .=  "	  </tr> ";
	$http_form .=  "	  <tr>";
	$http_form .=  "		<td align='center'>Output File: </td>";
	$http_form .=  "		<td align='center'><input type='file' name='data_set_out'</td>";
	$http_form .=  "	  </tr> ";
	$http_form .=  "	<tr><td colspan='2'><input name=submit type=submit value='Submit'></td></tr>";
}
else
{
	$action = "<h4>Select a problem to the left to add a data set</h4>";
}


//must be a http GET

	echo " <div class=\"container\">";



	echo "<div class=\"col-md-5\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo " <form action=setup_data_sets.php enctype='multipart/form-data' method=post>";
	echo $cur_data_sets;
	echo " <input type=hidden name=problem_id value=$problem_id>";
	echo "</table>";
	echo "</div>";

	if($error_msg)
	{
		echo "$error_msg";
	}

	echo "</div>";


	//Table for inputing a new input and output data set.
	echo "<div class=\"col-md-6\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align='left' width=100%>";
	echo "<tr><td align='center' colspan='2'><h3>$action</h3></td></tr>";
	echo $http_form;
	echo "</table>";
	echo "</div>";
	echo "</div>";




	//End the Form
	echo "</form>";
	echo "</div>";
	
	include("lib/footer.inc");
?>
