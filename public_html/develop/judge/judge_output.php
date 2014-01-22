<?
#
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/judge_output.php
#
    include_once("lib/config.inc");
    include_once("lib/judge.inc");
    include_once("lib/session.inc");
    include_once("lib/header.inc");
#
?>
<div class='container'>
<div class='innerglow'>
<div class='table-responsive'>
<table class='table' align='center'>
	    <tr><td colspan='3' align='center'><h3>Comparing Output Files for Problem <?=$_GET['problem']?></h3></td></tr>
	    <?
	    $format = $_GET['format'];
	    
	    if($format == 2 || $format == 3) {
	    ?>
                <td><center><b>Submitted Source</b></center></td></tr>
                <tr><td>
                <?
		if($format == 2) {
		    $dir = $base_dir . "/judged/" . $_GET['sub_source'];
		}
		else {
		    $dir = $_GET['sub_source'];
		}
		$out = file($dir);
		if(count($out) == 0) {
		    $out[0] = "The diff file is empty";
		}
		echo "<center><textarea rows=30 cols=75 readonly>";
		foreach($out as $line){
		    echo $line;
		}
                echo "</textarea></center></td>";
	    }
	    else {
	    ?>
		<tr><td><center><b>Judge's Output</b></center></td>
		<td><center><b>Submitted Output</b></center></td></tr>
		<tr><td>
		<?
		$dir = $base_dir . "/data/" . $_GET['judge_source'];
                    $out = file($dir);
                    $dir = $base_dir . "/judged/" . $_GET['sub_source'];
                    $out2 = file($dir);
                    echo "<div style=\"overflow:scroll; height:500px; width:390px; color:black; border:thin solid;\">\n";
                    echo "<font face=\"Courier\" size=\"2\">";
                    $x = 1;
                    foreach($out as $line){
                        $line = preg_replace("/ /", "<font color=\"red\">~</font>", $line);
                        $line = preg_replace("/\t/", "<font color=\"red\">________</font>", $line);
                        echo "$x: $line";
                        $x++;
                    }
                    echo "</font></div></td>";
                    echo "<td>";
                    echo "<div style=\"overflow:scroll; height:500px; width:390px; color:black; border:thin solid;\">\n";
                    echo "<font face=\"Courier\" size=\"2\">";
                    $x = 1;
                    foreach($out2 as $line){
                        $line = preg_replace("/ /", "<font color=\"red\">~</font>", $line);
                        $line = preg_replace("/\t/", "<font color=\"red\">________</font>", $line);
                        echo "$x: $line";
                        $x++;
                    }
                    echo "</font></div></td></tr>";

                }
         echo "</table>";
         echo "</div>";
         echo "</div>";
         echo "</div>";
         include_once("lib/footer.inc");
	    ?>
	    </table></tr></table></tr></table></tr></table></tr></table>
	    </html>
