<?
#
# Copyright (C) 2002 David Whittington
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/lib/session.inc
#
    include_once("admin_config.inc");
#
    session_name("TOUCHE-$db_name");
    session_start();
     	header ("expires:Tuesday, 25 Apr 1995 09:30:00 -0700");
    if(   $_SESSION['admin_user'] != $admin_user 
       || $_SESSION['admin_pass'] != $admin_pass)
    {
	header ("Location: index.php?state=2");
	exit(0);
    }
?>
