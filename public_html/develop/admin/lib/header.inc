<?
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2003, 2004 Jonathan Geisler
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/lib/header.inc
#

include_once("admin_config.inc");
include_once ("session.inc");
include_once("data.inc");


$link = mysql_connect($db_host, $db_user, $db_pass);
if (!$link) {
    print "Sorry.  Database connect failed.  Retrying...";
    exit;
}

$connect_good = mysql_select_db($db_name);
if (!$connect_good) {
    print "Sorry.  Database selection failed.  Retrying...";
    exit;
}
	include_once ("../lib/config.inc");
	include_once ("../lib/data.inc");
	include_once ("lib/session.inc");
	$result = mysql_query("SELECT * FROM SITE WHERE SITE_ID = 1");
	if (mysql_num_rows($result) > 0) {
		include_once ("lib/contest_info.inc");
	}

?>

<!DOCTYPE html>
<html>
<head>
<style>
	<?php include_once("../styles/css/bootstrap.css"); ?>
</style>
</head>
<body>

<!-- This is the start of the top bar -->

<div class="page-header">
        <div class="container">
        <div class="img-responsive2">
            <?php 
            $path =  "http://$_SERVER[HTTP_HOST]/images/ToucheLogo.png";
            /*header("Content-Type: image/png");*/
            echo "<img src='$path' alt='Logo'>";
            ?>
        </div>
         	<div class="text-right">
            	<h3>ADMIN</h3>
        	</div>
        </div>
</div>

<!-- END -->

<!-- START OF NAVIGATION -->

<?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?> 

<div class="container">
    <div class="text-center">

        <h5><?=$contest_name?> - <?=$contest_host?> - <?print date("H:i:s"); ?></h5>
    </div>

  <ul class="nav nav-tabs nav-justified">
    <!--<li><a href="#">&laquo;</a></li> -->
    <li class=<?php if (strpos($actual_link,'setup_contest.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_contest.php">Details</a></li>
    <li class=<?php if (strpos($actual_link,'setup_problems.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_problems.php">Problems</a></li>
    <li class=<?php if (strpos($actual_link,'setup_data_sets.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_data_sets.php">Data</a></li>
    <li class=<?php if (strpos($actual_link,'setup_site.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_site.php">Sites</a></li>
    <li class=<?php if (strpos($actual_link,'setup_teams.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_teams.php">Teams</a></li>
    <li class=<?php if (strpos($actual_link,'setup_categories.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_categories.php">Categories</a></li>
    <?$lang_sql = "SELECT 	sum(REPLACE_HEADERS) AS heads,";
		$lang_sql  .= "		sum(CHECK_BAD_WORDS) AS bad_words ";
		$lang_sql  .= "FROM LANGUAGE";
		$sel_sql = mysql_query($lang_sql);
		if (!$sel_sql) {
			echo "Error!  Could not create the header ";
			echo "correctly.<br /> Please contact an ";
			echo "administrator.";
		}
		$contest_stuff = mysql_fetch_assoc($sel_sql);
		if ($contest_stuff['heads'] > 0) { ?>
	<li class=<?php if (strpos($actual_link,'setup_headers.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_headers.php">Headers</a></li>
	<?}
		if ($contest_stuff['bad_words'] > 0) { ?>
    <li class=<?php if (strpos($actual_link,'setup_forbidden.php') !== false) {
                echo "'active'";
            }
        ?>><a href="setup_forbidden.php">Forbidden</a></li>
    <?} ?>
    <li class=<?php if (strpos($actual_link,'misc.php') !== false) {
                echo "'active'";
            }
        ?>><a href="misc.php">Misc</a></li>

         <li class=<?php if (strpos($actual_link,'start.php') !== false) {
                echo "'active'";
            }
        ?>><a href="start.php">Start</a></li>

    <!--<li><a href="#">&raquo;</a></li> -->
  </ul>

<!-- END OF NAVIGATION -->
                <?php


                        $freeze_diff = $contest_freeze_ts + $site_start_offset - time();
                        $time_till_freeze_starts = gmdate('H:i:s', $freeze_diff);
                        $time_till_contest_end = gmdate("H:i:s", $contest_end_ts - time());
                        $end_diff = $contest_end_ts + $site_start_offset - time();

                ?>

                <div class="panel panel-default">
                    <div class="panel-body">
                    <table class="table" align='center'>
                                <tr>
                    <?php
                        //1200 = 20 minutes left
                        //600 = 10 minutes left
                        //300 = 5 min






                        if ($freeze_diff > 1200) {
                            
                            print "<td align='right'><div class=\"tablehelp\"><h4>Freeze begins in:</h4></div></td> ";
                            print "<td align='left'><h3>$time_till_freeze_starts</h3></td>";
                        }
                        elseif ($freeze_diff > 600) {
                            
                            print "<td class='active' align='right'><div class=\"tablehelp\"><h4>Freeze begins in:</h4></div></td> ";
                            print "<td class='active' align='left'><h3>$time_till_freeze_starts</h3></td>";
                        }
                        elseif ($freeze_diff > 300) {
                            
                            print "<td class='warning' align='right'><div class=\"tablehelp\"><h4>Freeze begins in:</h4></div></td> ";
                            print "<td class='warning' align='left'><h3>$time_till_freeze_starts</h3></td>";
                        }
                        elseif ($freeze_diff > 0) {
                            
                            print "<td class='danger'><div class=\"tablehelp\"><h4>Freeze begins in:</h4></div></td> ";
                            print "<td class='danger' align='left'><h3>$time_till_freeze_starts</h3></td>";
                        }
                        
                        else {
                            print"<td align='center' colspan=2><div class='tablehelp'><h4>Standings Are Now Frozen</h4></div></td>";
                        }
                    ?>
                            </tr>



                            <tr>
                    <?php


                        if ($end_diff > 1200){
                            
                    ?>
                            <td class='active' align='right'><div class="tablehelp"><h4>Contest Ends in:</h4></div></td>
                            <td class='active' align='left'><h3><?=gmdate("H:i:s", $contest_end_ts - time())?></h3></td>
                     
                    <?php
                        }
                        elseif ($end_diff > 600){
                    ?>
                            <td class='warning' align='right'><div class="tablehelp"><h4>Contest Ends in:</h4></div></td>
                            <td class='warning' align='left'><h3><?=gmdate("H:i:s", $contest_end_ts - time())?></h3></td>
                    <?php       
                        }

                        elseif ($end_diff > 0){
                    ?>
                            <td class='danger' align='right'><div class="tablehelp"><h4>Contest Ends in:</h4></div></td>
                            <td class='danger' align='left'><h3><?=gmdate("H:i:s", $contest_end_ts - time())?></h3></td>
                     <?php 
                        }

                        else {

                    ?>
                            <td align='center' colspan=2><div class='tablehelp'><h4>Contest is Now Over</h4></div></td>
                    <?php
                        }
                

                    ?>
                            </tr>
                                    

                    </table>
                    </div>
                </div>

</div>



<div class='container'>
<div class='innerglow'>
 					 

				

