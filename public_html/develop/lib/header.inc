<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2003 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: lib/header.inc
#
    include_once ("lib/session.inc");
    include_once ("lib/config.inc");
    include_once ("lib/data.inc");

?>

<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">
<!DOCTYPE html>
<html>
<head>
<style>
	<?php include_once("styles/css/bootstrap.css"); ?>
</style>
</head>
<body>


<!-- This is the start of the top bar -->
<div class="page-header">
        <div class="container">
        <div class="img-responsive2">
            <?php 
            $path =  "http://$_SERVER[HTTP_HOST]/images/ToucheLogo.png";
            header("Content-Type: image/png");
            echo "<img src='$path' alt='Logo'>";
            ?>
        </div>
         	<div class="text-right">
            	<h3><?=$team_name?></h3>
        	</div>
        </div>
</div>

<!-- START OF NAVIGATION -->

<?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?> 

<div class="container">
    <div class="text-center">

        <h5><?=$contest_name?> - <?=$contest_host?> - <?print date("H:i:s"); ?></h5>
    </div>

  <ul class="nav nav-tabs nav-justified">
    <!--<li><a href="#">&laquo;</a></li> -->
    <li class=<?php if (strpos($actual_link,'problems.php') !== false) {
                echo "'active'";
            }
        ?>><a href="problems.php">Problems</a></li>
    <li class=<?php if (strpos($actual_link,'submissions.php?state=0') !== false) {
                echo "'active'";
            }
        ?>><a href="submissions.php?state=0">Submissions</a></li>
    <li class=<?php if (strpos($actual_link,'standings.php') !== false) {
                echo "'active'";
            }
        ?>><a href="standings.php">Standings</a></li>
    <li class=<?php if (strpos($actual_link,'clarifications.php?problem_id=-1') !== false) {
                echo "'active'";
            }
        ?>><a href="clarifications.php?problem_id=-1">Clarifications</a></li>
    <li class=<?php if (strpos($actual_link,'readme.php') !== false) {
                echo "'active'";
            }
        ?>><a href="readme.php">ReadMe</a></li>

    <!--<li><a href="#">&raquo;</a></li> -->
  </ul>


				<?
				/*
				 * the following lines find out how far
				 * away the freezing of the standings is
				 * and how many seconds the end of the contest is.
				 * Then, it stores these values for use below.
				 * Date was used previously but needed to be adjusted
				 * for the time zone.  gmdate does this automatically;
				 * therefore, a much better solution.
				 */
				$end_diff = $contest_end_ts + $site_start_offset - time();
				$freeze_diff = $contest_freeze_ts + $site_start_offset - time();
	            $time_till_freeze_starts = gmdate('H:i:s', $freeze_diff);
				if(!$site_started)
				{
					print "</td></tr><tr><td colspan=2 bgcolor=red><center><h2>Contest has not yet started</h2></center></td></tr>";
					include_once "footer.inc";
					die;
				}
				if($end_diff <0)
				{
					print "</td></tr><tr><td colspan=2 bgcolor=green><center><h2>Contest is over</h2></center></td></tr>";
					include_once "footer.inc";
					die;
				}
				?>


				<div class="panel panel-default">
        			<div class="panel-body">
            		<table class="table" align='center'>
				                <tr>
	                <?
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
				
<!-- 
				print "<b>Official Time: " . date("H:i:s") . "</b>"; KEEP THIS
 -->
