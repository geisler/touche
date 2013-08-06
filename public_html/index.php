<?
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: index.php
#
    include_once("lib/config.inc");
    include_once("lib/data.inc");

session_name("TOUCHE-$db_name");
session_start();
$_SESSION = array();

if($_SERVER['REQUEST_METHOD'] == 'GET') {

?>
<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">
<html>
<head>
<link rel="shortcut icon" href="images/favicon.ico" />
<script language="javascript">
    function set_focus() {
	if (document.f.user.value) {
	    document.f.password.focus();
	} else {
	document.f.user.focus();
	}
    }
    function check_input() {
    	if(document.f.user.value.length == 0 || document.f.password.value.length == 0)
	{
		alert("Please fill out all fields.");
		return false;
	}
	return true;
    }
</script>
</head>
<body bgcolor=<?=$page_bg_color?> onLoad="set_focus()">

<form name="f" method="post" action="index.php">
<table align="center" height="100%" border="0"><tr><td>
<table cellpadding="1" cellspacing="0" border="0" bgcolor="#000000"><tr><td>
<table cellpadding="5" cellspacing="0" border="0" bgcolor="<?=$title_bg_color?>"><tr><td>
<font color="#ffffff">
<b><?php echo $contest_name ?></b><br>
<small><?php echo $contest_host ?></small>
</font>
</td></tr><tr><td bgcolor="#ffffff">

<?php
    if(isset($state) && $state == 1) {
	echo "<center><font color=#cc0000><b>";
	echo "Login or Password Invalid</b></font></center>\n";
    } else if(isset($state) && $state == 2) {
	echo "<center><font color=#cc0000><b>";
	echo "You are not yet logged in</b></font></center>\n";
    }

?>

<table cellpadding="5" cellspacing="0" border="0">
<tr><td>Login:</td><td><input type="text" name="user" id="user" size="20">
</td></tr>
<tr><td>Password:</td><td><input type="password" name="password" id="password" size="20"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="  OK  " onclick="return check_input()">
<input type="reset" name="submit" value=" Cancel "></td></tr>
</table>
</td></tr></table>
</td></tr></table>
</td></tr></table>
</form>
</body>
</html>
<?
}
else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = $_POST['user'];
    $password = $_POST['password'];
   
    if($password == $users[$user]['password']) {
	$_SESSION['contestant_user'] = $user;
	$_SESSION['contestant_pass'] = $password;
	$_SESSION['team_id'] = $users[$user]['team_id'];
	$_SESSION['team_name'] = $users[$user]['team_name'];
	header ("Location: main.php");
    }
    else {
	header ("Location: index.php?state=1");
    }
}

?>
