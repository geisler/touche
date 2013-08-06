<?
#
# Copyright (C) 2002 David Whittington
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: main.php
#
    include_once("lib/header.inc");
    
    if (file_exists("lib/motd.inc")) {
	    include_once("lib/motd.inc");
    } else {
	    echo "<br><b>No welcome message defined.</b><br><br>\n";
    }
    include_once("lib/footer.inc");
?>
