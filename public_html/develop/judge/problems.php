<?
#
# Copyright (C) 2003 David Whittington
# Copyright (C) 2003 Jonathan Geisler
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/problems.php
#
    include_once("lib/config.inc");
    include_once("lib/judge.inc");
    include_once("lib/header.inc");
    include_once("lib/database.inc");
    include_once("lib/session.inc");
    include_once("../lib/data.inc");

judge_header(0);
echo "<div class='container'>";
echo "<div class='innerglow'>";
echo "<div class='table-responsive'>";
echo "<table class='table' align=center width=100%>\n";
echo "<tr><td colspan=5 align=center>\n";
echo "<h3>Problems Listing</h3></td></tr>\n";


#echo "<table width=400 align=center>\n";
echo "<tr><td>Problem Name</td>";
echo "<td align=center>HTML</td>";
#echo "<td bgcolor=$hd_bg_color2 align=center>PS</td>";
echo "<td align=center>PDF</td></tr>";
#echo "<tr><td bgcolor=$data_bg_color1>All problems</td>";
#echo "<td bgcolor=$data_bg_color1 align=center>";
#echo "<a href='../$problem_url/problems.html'>HTML</td>";
//echo "<td bgcolor=$data_bg_color1 align=center>";
//echo "<a href='$problem_url/problems.ps'>PS</td>";
#echo "<td bgcolor=$data_bg_color1 align=center>";
#echo "<a href='../$problem_url/problems.pdf'>PDF</td>";
#echo "</tr>";

foreach ($problems as $problem) {
    echo "<tr><td>$problem[id] - $problem[name]</td>";
    echo "<td align=center>";
    echo "<a href='../$problem_url/$problem[loc]/$problem[name].html' target=\"_blank\">HTML</a></td>";
  //  echo "<td bgcolor=$data_bg_color1 align=center>";
  //  echo "<a href='$problem_url/$problem[loc]/problem.ps'>PS</a></td>";
    echo "<td align=center>";
    echo "<a href='../$problem_url/$problem[loc]/$problem[name].pdf' target=\"_blank\">PDF</a></td>";
    echo "</tr>";
}
echo "</table>\n";
echo "</div>";
echo "</div>";
echo "</div>";

    include("lib/footer.inc");
?>

