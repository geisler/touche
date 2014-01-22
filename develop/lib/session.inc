<?
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: lib/session.inc
#
    include_once('database.inc');

    session_name("TOUCHE-$db_name");
    session_start();
	header ("expires:Tuesday, 25 Apr 1995 09:30:00 -0700");
    $team_id = $_SESSION['team_id'];
    $team_name = $_SESSION['team_name'];
    if(!isset($_SESSION['contestant_user']) || !isset($_SESSION['contestant_pass'])){
	header ("Location: index.php?state=2");
	exit(0);
    }
		    
?>
