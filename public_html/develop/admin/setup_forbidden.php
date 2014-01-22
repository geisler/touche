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
# arch-tag: admin/setup_forbidden.php
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
			$sql = "select * from FORBIDDEN_WORDS WHERE LANGUAGE_ID = '$lang_id'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg = "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
			else
			{
				$edit_forbidden_words = "";
				while($row = mysql_fetch_assoc($result))
				{
					$edit_forbidden_words .= $row['WORD'] . ":";
				}
					
			}
		}
		$action = "Editing forbidden words for language: $language_name";
	}
}
else if($_POST)
{
	if($_POST['submit'])
	{
		if(isset($_SESSION['edit_language']))
		{
			//parse the headers string
			$build_forbidden_words = split("\n", $_POST['edit_forbidden_words']);
			unset($error_msg);
			//clear out the DB
			$sql = "delete from FORBIDDEN_WORDS where LANGUAGE_ID = '";
			$sql .= $_SESSION['edit_language'] . "'";
			$result = mysql_query($sql);
			if(!$result)
			{
				$error_msg .= "<div class='error'><br>Error: " . mysql_error();
				$error_msg .= "<br>SQL: $sql</div>";
			}
			foreach($build_forbidden_words as $forbidden_word)
			{
				//clean out blank lines composed of only whitespace
				$forbidden_word = trim($forbidden_word);
				if(strlen($forbidden_word) > 0)
				{
					$sql = "insert into FORBIDDEN_WORDS values('" . $_SESSION['edit_language'];
					$sql .= "', '$forbidden_word')";
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
			$error_msg .= "<div class='success'><br>Forbidden Word changed successfully</div>";
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

	$cur_headers .= "<tr><td colspan=3><h3>Edit Current Forbidden Words</b=h3></td></tr>";
	while($row = mysql_fetch_assoc($result)){
		$cur_headers .= "<tr><td>" . $row['LANGUAGE_NAME']; 
		$cur_headers .= " </td><td>";
		$cur_headers .= "<a href=setup_forbidden.php?lang_id=" . $row['LANGUAGE_ID'] . ">Edit</a>";
		$cur_headers .= "</td></tr>";
	}
}
else
{
	$cur_headers = "No current languages";
}

//must be a http GET
	echo " <div class=\"container\">";



	echo "<div class=\"col-md-5\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo $cur_headers;
	echo " </table>";
	echo "</div>";

	if($error_msg)
	{
		echo "$error_msg";
	}


	echo "</div>";


	echo "<div class=\"col-md-6\">";
	echo " <div class=\"table-responsive\">";
	echo " <table class=\"table\" align=\"left\" width=100%>";
	echo " <form action=setup_forbidden.php method=post>";



	echo "	  <tr>";
	echo "		<td colspan=2>";
	echo "		<h3>$action</h3></td>";
	echo "	  </tr> ";
	//in this case, we don't add new things, so we need to check to see if we
	//are editing something
	if(isset($edit_forbidden_words))
	{
		$sub_headers = split(":", $edit_forbidden_words);
		echo "	  <tr>";
		echo "		<td><textarea rows=10 name='edit_forbidden_words'>";
		foreach($sub_headers as $forbidden_word)
		{
			echo "$forbidden_word\n";
		}
		echo "			</textarea></td>";
		echo "	  </tr> ";
		echo "	<tr><td><button class=\"btn btn-default\" name=submit type=submit value='Submit'>Make Changes</button></td></tr>";
	}
	echo "</form>";
	echo "</table>";
	echo "</div>";
	echo "</div>";
	include("lib/footer.inc");
?>
