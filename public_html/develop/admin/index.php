<?php
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


<!DOCTYPE html> <!-- START HTML -->

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

<style>
<?php include_once("../styles/css/bootstrap.css"); ?>
</style>

</head>
<body onLoad="set_focus()">
    <div class="page-header">
        <div class="container">
        <div class="img-responsive2">
        	<?php 
        	$path =  "http://$_SERVER[HTTP_HOST]/images/ToucheLogo.png";
        	header("Content-Type: image/png");
        	echo "<img src='$path' alt='Logo'>";
        	?>

        </div>
        <div class="text-right">
	        <form class="form-inline" name="f" method="post" action="index.php">
        		<div class="form-group">
				    <input type="text" class="form-control" name="user" size="30" placeholder="Username">
				</div>
				<div class="form-group">
				    <input type="password" class="form-control" name="password" size="30" placeholder="Password">
				</div>
				<div class="form-group">
				  <button type="submit" class="btn btn-default" name="submit">Sign in</button>
				</div>
				<?

			    if (isset($state) && $state == 1) {
				echo "<center><font color=#cc0000><b>";
				echo "Login or Password Invalid</b></font></center>\n";
			    }
			    else if (isset($state) && $state == 2) {
				echo "<center><font color=#cc0000><b>";
				    echo "You are not yet logged in</b></font></center>\n";
			    }

				?>
			</form>
		</div>
        </div>
    </div>
	<div class="container">
		<div class="row">
			<h3>Admin Login for: <?=$contest_name?></h3>
		</div>
		<div class="row">
			<h4><?=$contest_host?></h4>
		</div>	
	</div>

</body>
</html>
<?php
}
?>
