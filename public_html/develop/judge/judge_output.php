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
#
?>
<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">
<html>
<body bgcolor="<?=$page_bg_color?>" link="0000cc" alink="000066" vlink="0000cc">
<table width="90%" align="center" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
    <tr><td>
    	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	    <tr bgcolor="<?=$title_bg_color?>">
		<td>
		    <font color="#ffffff">
		    <b><?=$contest_name?></b> - <small><?=$contest_host?></small>
		    </font>
		</td>
		<td align="right">
		    <font color="#ffffff">
		    <b>JUDGE</b>
		    </font>
		</td>
	    </tr>
	    <tr>
		<td bgcolor="#dcdcdc" align="left"><b>
		</td>
		<td align="right" bgcolor="#dcdcdc">
		    <b>Official Time: <?=date("H:i:s")?></b>
		</td>
	    </tr>
	    <tr>
	    <td bgcolor="#ffffff" colspan="2">
	    <table align=center bgcolor=#ffffff cellpadding=0
		cellspacing=0 border=0><tr><td>
	    <table width=100% cellpadding=5
	            cellspacing=1 border=0>
	    <tr><td colspan=2 bgcolor="<?=$hd_bg_color1?>">
	    <center><b><font color="<?=$hd_txt_color1?>">
	    Comparing Output Files for Problem <?=$_GET['problem']?>
	    </font></center></td></tr>
	    <?
	    $format = $_GET['format'];
	    
	    if($format == 2 || $format == 3) {
	    ?>
                <td bgcolor="<?=$hd_bg_color2?>"><center><b>
                <font color="<?=$hd_txt_color2?>">Submitted Source 
                </font></b></center></td></tr>
                <tr><td bgcolor="<?=$data_bg_color1?>">
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
		<tr><td bgcolor="<?=$hd_bg_color2?>"><center><b>
		<font color="<?=$hd_txt_color2?>">Judge's Output
		</font></b></center></td>
		<td bgcolor="<?=$hd_bg_color2?>"><center><b>
		<font color="<?=$hd_txt_color2?>">Submitted Output
		</font></b></center></td></tr>
		<tr><td bgcolor="<?=$data_bg_color1?>">
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
                    echo "<td bgcolor=$data_bg_color1>";
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
	    ?>
	    </table></tr></table></tr></table></tr></table></tr></table>
	    </html>
