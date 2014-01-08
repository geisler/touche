<?
    include_once("create.inc");

    session_name("TOUCHE-CREATE");
    session_start();
        header ("expires:Tuesday, 25 Apr 1995 09:30:00 -0700");
    if(   $_SESSION['admin_user'] != $create_user
       || $_SESSION['admin_pass'] != $create_pass)
    {
        header ("Location: index.php?state=2");
        exit(0);
    }
?>

