<?
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/lib/session.inc
#
    include_once("judge.inc");
#
    session_name("TOUCHE-$db_name");
    session_start();
	header ("expires:Tuesday, 25 Apr 1995 09:30:00 -0700");
	if(   $_SESSION['judge_username'] != $judge_user 
	   || $_SESSION['judge_password'] != $judge_pass)
	{
	    header ("Location: index.php?state=2");
	    exit(0);
	}
?>
