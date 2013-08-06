<?
#
# Copyright (C) 2002 David Whittington
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/index.php
#
    include("lib/admin_config.inc");
    include("lib/data.inc");

session_name("TOUCHE-$db_name");
session_start();
$_SESSION = array();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = $_POST['user'];
    $password = $_POST['password'];

    if($user == $admin_user && $password == $admin_pass) {
	$_SESSION['admin_user'] = $user;
	$_SESSION['admin_pass'] = $password;
	if($user == "admin" && $password == "admin") {
		header ("Location: changepw.php");
	} else {
		header ("Location: setup_contest.php");
	}
	exit(0);
    }
    else {
    	header ("Location: index.php?state=1");
	exit(0);
    }
}

else if($_SERVER['REQUEST_METHOD'] == 'GET'){

?>
<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">
<html>
<head>
<script language="javascript">
    function set_focus() {
	if (document.f.user.value) {
	    document.f.password.focus();
	} else {
	    document.f.user.focus();
	}
    }
</script>
</head>
<body bgcolor=<?=$page_bg_color?> onLoad="set_focus()">

<form name="f" method="post" action="index.php">
<table align="center" height="100%" border="0"><tr><td>
<table cellpadding="1" cellspacing="0" border="0" bgcolor="#000000"><tr><td>
<table cellpadding="5" cellspacing="0" border="0" bgcolor="<?=$title_bg_color?>"><tr><td>
<font color="#ffffff">
<b><?=$contest_name?></b><br>
<small><?=$contest_host?></small>
</font>
</td></tr><tr><td bgcolor="#ffffff">
<?

    if (isset($state) && $state == 1) {
	echo "<center><font color=#cc0000><b>";
	echo "Login or Password Invalid</b></font></center>\n";
    }
    else if (isset($state) && $state == 2) {
	echo "<center><font color=#cc0000><b>";
	    echo "You are not yet logged in</b></font></center>\n";
    }

?><table cellpadding="5" cellspacing="0" border="0">
<tr><td>Login:</td><td><input type="text" name="user" size="20">
</td></tr>
<tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="  OK  ">
<input type="reset" name="submit" value=" Cancel "></td></tr>
</table>
</td></tr></table>
</td></tr></table>
</td></tr></table>
</form>

</body>
</html>
<?php
}
?>
