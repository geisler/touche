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
				$error_msg = "Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
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
				$error_msg = "Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
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
				$error_msg .= "Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql";
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
						$error_msg .= "Error: " . mysql_error();
						$error_msg .= "<br>SQL: $sql";
					}
				}
			}
		}
		if(!isset($error_msg))
		{
			$error_msg .= "Header changed successfully";
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
	$cur_headers = "<font size=+1>&nbsp</font><br>";
	$cur_headers .= "<br><table>";
	$cur_headers .= "<tr><td><font size=+1><b>Edit Current Headers</b></font></td></tr>";
	while($row = mysql_fetch_assoc($result)){
		$cur_headers .= "<tr><td>" . $row['LANGUAGE_NAME']; 
		$cur_headers .= " </td><td><font size=-1>";
		$cur_headers .= "<a href=setup_headers.php?lang_id=" . $row['LANGUAGE_ID'] . ">Edit</a>";
		$cur_headers .= "</font>";
		$cur_headers .= "</td></tr>";
	}
	$cur_headers .= "</table>";
}
else
{
	$cur_headers = "No current headers";
}

//must be a http GET
	echo " <table align=center bgcoloer=#ffffff cellpadding=0 cellspacing=0 border=0 width=100%>";
	echo " <tr><td width=30% valign='top'>";
	echo $cur_headers;
	echo " </td>";
	echo " <td width=50%>";
	echo " <form action=setup_headers.php method=post>";
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
	echo "				<b>Edit Headers</b></font>";
	echo "		</td>";
	echo "	  </tr>";
	echo "	  <tr bgcolor=$hd_bg_color2>";
	echo "		<td align='center' colspan=2><font color='$hd_txt_color2'>";
	echo "		<b>$action</b></font></td>";
	echo "	  </tr> ";
	//in this case, we don't add new things, so we need to check to see if we
	//are editing something
	if(isset($edit_headers))
	{
		$sub_headers = split(":", $edit_headers);
		echo "	  <tr bgcolor=\"$data_bg_color1\">";
		echo "		<td valign=top>Headers </td>";
		echo "		<td><textarea rows=10 name='edit_headers'>";
		foreach($sub_headers as $header)
		{
			echo "$header\n";
		}
		echo "			</textarea></td>";
		echo "	  </tr> ";
		echo "	<tr><td><input name=submit type=submit value='Submit'></td></tr>";
	}
	echo "</form>";
	echo "</td></tr>";
	echo "</table>";
	echo "	</td><td width=20%></td></tr>";
	echo "</table>";
	include("lib/footer.inc");
?>
