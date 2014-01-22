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
# arch-tag: admin/setup_headers.php
#
include_once("lib/admin_config.inc");
include_once("lib/data.inc");
include_once("lib/session.inc");
include_once("lib/header.inc");
if ($_GET)
{
	if(isset($_GET['lang_id']))
	{
		$lang_id = $_GET['lang_id'];
		$_SESSION['edit_language'] = $lang_id;
		if($lang_id != -1)
		{
			$sql = "select * from LANGUAGE WHERE LANGUAGE_ID = '$lang_id'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
			else
			{
				$row = mysql_fetch_assoc($result);
				$language_name = $row['LANGUAGE_NAME'];
			}
			$sql = "select * from HEADERS WHERE LANGUAGE_ID = '$lang_id'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
			else
			{
				$edit_headers = "";
				while($row = mysql_fetch_assoc($result))
				{
					$edit_headers .= $row['HEADER'] . ":";
				}
					
			}
		}
		$action = "Editing headers for language: $language_name";
	}
}
else if($_POST)
{
	if($_POST['submit'])
	{
		if(isset($_SESSION['edit_language']))
		{
			//parse the headers string
			$build_headers = split("\n", $_POST['edit_headers']);
			unset($error_msg);
			//clear out the DB
			$sql = "delete from HEADERS where LANGUAGE_ID = '";
			$sql .= $_SESSION['edit_language'] . "'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg .= "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
			foreach($build_headers as $header)
			{
				//clean out blank lines composed of only whitespace
				$header = trim($header);
				if(strlen($header) > 0)
				{
					$sql = "insert into HEADERS values('" . $_SESSION['edit_language'];
					$sql .= "', '$header')";
					$result = mysql_query($sql);
					if(!$result)
					{
						$error_msg .= "<div class='error'><br>Error: " . mysql_error();
						$error_msg .= "<br>SQL: $sql</div>";
					}
				}
			}
		}
		if(!isset($error_msg))
		{
			$error_msg .= "<div class='success'><br>Header changed successfully</div>";
		}
	}
}
/*******************************************************
End of POST section
*******************************************************/
//build some http strings we'll need later
if(!$action)
{
	$action = "Please select a language to edit";
}
$cur_headers = "";
//get all the current categories
$sql = "select * from LANGUAGE";
$result = mysql_query($sql);
if(mysql_num_rows($result) > 0) {
	$cur_headers .= "<tr><td colspan=2><h3>Edit Current Headers</h3></td></tr>";
	while($row = mysql_fetch_assoc($result)){
		$cur_headers .= "<tr><td>" . $row['LANGUAGE_NAME']; 
		$cur_headers .= " </td><td>";
		$cur_headers .= "<a href=setup_headers.php?lang_id=" . $row['LANGUAGE_ID'] . ">Edit</a>";
		$cur_headers .= "</td></tr>";
	}
}
else
{
	$cur_headers = "No current headers";
}

//must be a http GET
	echo " <div class=\"container\">";

	//Select Language Column
	echo "<div class=\"col-md-5\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo " <form action=setup_headers.php method=post>";
	echo $cur_headers;
	echo "</table>";
	echo "</div>";

	if($error_msg)
	{
		echo "$error_msg";
	}

	echo "</div>";



	echo "<div class=\"col-md-6\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo "	  <tr>";
	echo "		<td colspan=2>";
	echo "		<h3>$action</h3></td>";
	echo "	  </tr> ";
	//in this case, we don't add new things, so we need to check to see if we
	//are editing something
	if(isset($edit_headers))
	{
		$sub_headers = split(":", $edit_headers);
		echo "		<td><textarea rows=10 name='edit_headers'>";
		foreach($sub_headers as $header)
		{
			echo "$header\n";
		}
		echo "			</textarea></td>";
		echo "	  </tr> ";
		echo "	<tr><td><button class=\"btn btn-default\" name=submit type=submit value='Submit'>Make Changes</button></td></tr>";
	}
	echo "</form>";
	echo "</td></tr>";
	echo "</table>";
	echo "</div>";
	echo "</div>";
	echo "</div>";
	include("lib/footer.inc");
?>
