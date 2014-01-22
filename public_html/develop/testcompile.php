<?
# TEST COMPILE
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: submissions.php
#
	include_once("lib/header.inc");
	include_once("lib/config.inc");
	include_once("lib/data.inc");

	$state = $_GET['state'];
	$errors = $_SESSION['compile_errors'];
	unset($_SESSION['compile_errors']);
    if ($state == 1) {
	    echo "<center><font color=\"#ee0000\">No file selected for submission!</font><br><br></center>\n";
    }
    if ($state == 2) {
        echo "<center><font color=\"#00aa00\">Submission successful. No compile errors.</b></font><br><br></center>\n";
    }
    if ($state == 3){
        echo "<center><font color=\"#ee0000\">Invalid file type.</b></font><br><br></center>\n";
    }
    if ($state == 4){
        echo "<center><font color=\"#ee0000\">Error receiving file.</b></font><br><br></center>\n";
    }
    if ($state == 5){
        echo "<center><font color=\"#ee0000\">Compile Errors</b></font><br><br></center>\n";
	$errors = str_replace("\n", "<br />", $errors);
	echo "<div class = 'error'><br>$errors</div>";
    }

	
	echo "<b><center>Test Compile</center></b><br>\n";
	echo "<table align=\"center\" bgcolor=\"#000000\" width=\"90%\" ";
	echo "cellpadding=\"0\"  cellspacing=\"0\" border=\"0\"><tr><td>\n";
	echo "<table align=\"center\" width=\"100%\" cellpadding=\"5\" ";
	echo "cellspacing=\"1\" border=\"0\"><form method=\"post\" enctype=\"multipart/form-data\" action=\"submit_test.php\">\n";
	echo "<tr><td align=\"center\" bgcolor=\"$data_bg_color1\" colspan=\"3\">\n";
	echo "<table>";
	echo "<tr><td align=\"right\">";
	echo "Source File (C, C++, or Java) &nbsp\n";
	echo "</td><td>";
	echo "<input type=\"file\" name=\"source_file\">\n";
	echo "</td></tr><tr><td align=\"right\">";
	echo "</td></tr><tr><td colspan=\"2\" align=\"center\">\n";
	echo "<p>\n";
	echo "<input type=\"submit\" value=\"Submit Solution\">\n";
	echo "<input type=\"reset\" value=\"Cancel\">\n";
	echo "</td></tr></table>\n";
	echo "</td></tr>\n";
	echo "</form></table>\n";
	echo "</td></tr></table>\n";
	
	reset($problems);
	mysql_close ($link);
	include_once("lib/footer.inc");
?>
