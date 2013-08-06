<?php

include_once("lib/session.inc");
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = $_POST['user'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
	echo "pw: $password, pw2: $password2, usr: $user";
if($user && $password && $password2) {
    if($password == $password2) {
	#change the user and password file
	$fhdl = fopen("lib/admin_config.inc", "r") OR dir("Error opening admin_config");
	$file = fread($fhdl, filesize("lib/admin_config.inc"));
	$file = preg_replace("/user = \"admin\"/", "user = \"$user\"", $file);
	$file = preg_replace("/pass = \"admin\"/", "pass = \"$password\"", $file);
	fclose($fhdl);
	$fhdl = fopen("lib/admin_config.inc", "w") OR die("Error opening admin_config");
	$chk = fwrite($fhdl, $file);
	fclose($fhdl);
	$_SESSION['admin_user'] = $user;
        $_SESSION['admin_pass'] = $password;
	echo "<script type=\"text/javascript\"> window.location = 'setup_contest.php'  </script>";
        exit(0);
    } else {
	echo "<script type=\"text/javascript\"> window.location = 'changepw.php?state=1'  </script>";
        exit(0);
      }
}
else {
	echo "<script type=\"text/javascript\"> window.location = 'changepw.php?state=1'  </script>";
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
        } else if (document.f.password.value) {
            document.f.password2.focus();
        } else {
	    document.f.user.focus();
	}
    }
</script>
</head>
<body bgcolor=<?=$page_bg_color?> onLoad="set_focus()">

<form name="f" method="post" action="changepw.php">
<table align="center" height="100%" border="0"><tr><td>
<table cellpadding="1" cellspacing="0" border="0" bgcolor="#000000"><tr><td>
<table cellpadding="5" cellspacing="0" border="0" bgcolor="<?=$title_bg_color?>"><tr><td>
<font color="#ffffff">
<b>Please Change Login Info</b><br>
<small></small>
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
<tr><td>Retype:</td><td><input type="password" name="password2" size="20"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="  OK  ">
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
?>
