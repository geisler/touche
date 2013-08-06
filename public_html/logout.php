<?php
#
# Copyright (C) 2002 David Whittington
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: logout.php
#
	include_once('lib/database.inc');

	session_name("TOUCHE-$db_name");
	session_start();
	session_destroy();
	header("Location: index.php");
	exit(0);
?>
