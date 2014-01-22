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
        </div>
</div>
<div class="container">
<h3>Please change your Login Information</h3>
<form class="form-horizontal" name="f" method="post" action="changepw.php">
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
  <div class="form-group">
    <div class="col-xs-4">
    <div class="col-sm-10">
      <input type="text" class="form-control" name="user" placeholder="Username">
    </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-4">
    <div class="col-sm-10">
      <input type="password" class="form-control" name="password" placeholder="Password">
    </div>
</div>
  </div>
  <div class="form-group">
    <div class="col-xs-4">
    <div class="col-sm-10">
      <input type="password" class="form-control" name="password2"  placeholder="Retype Password">
    </div>
</div>
  </div>
  <div class="form-group">
    <div class="col-xs-4">
    <div class="col-sm-10">
      <input type="submit" class="btn btn-default" name="submit" value="Change">
    </div>
    </div>
  </div>
</form>
</div>
</body>
</html>
<?
}
?>
