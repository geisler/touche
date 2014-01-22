<?
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2003, 2004 Jonathan Geisler
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: judge/lib/header.inc
#
	include_once ("session.inc");
	include_once ("config.inc");
	include_once ("judge.inc");

function judge_header($refresh_time){
global $contest_name, $contest_host, $page_bg_color, $title_bg_color, 
$contest_freeze_ts, $site_start_offset, $contest_end_ts, $num_queued, $num_clarifications;
	#some sql stuff we'll use a bit later
	$sql = "select count(*) from QUEUED_SUBMISSIONS";
	$result = mysql_query($sql);
	## Checking added. -sb 2006-10-05
	if( !$result )  {
	    print "Sorry.  Database request ($sql) failed with " . mysql_error();
	    exit;
	}

	$row = mysql_fetch_row($result);
	$num_queued = $row[0];
	
	$sql = "select count(*) from CLARIFICATION_REQUESTS where RESPONSE = ''";
	$result = mysql_query($sql);
	if( !$result )  {
	    print "Sorry.  Database request ($sql) failed with " . mysql_error();
	    exit;
	}

	$row = mysql_fetch_row($result);
	$num_clarifications = $row[0];
?>


<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Judging</title>
<?
	if($refresh_time != 0)
	{
		echo "<meta http-equiv='refresh' content='$refresh_time'>";	
	}
?>

<style>
	<?php include_once("../styles/css/bootstrap.css"); ?>
</style>
<script src="https://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

</head>

<body>
<!-- START TOP BAR-->
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
            	<h3>JUDGE</h3>
        	</div>
        </div>
</div>
<!-- END TOP BAR -->

<!-- START OF NAVIGATION -->
<div class="container">
	<div class="text-center">
					<?
		if (!$contest_name)
		{?>
			<b>No contest exists yet
		<?}
		else { ?>
			<h5><?=$contest_name?> - <?=$contest_host?> - <?print date("H:i:s"); ?></h5>
		<?}?>
	</div>
  <ul class="nav nav-tabs nav-justified">

<?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?> 

    <li class=<?php if (strpos($actual_link,'judge.php') !== false) {
  				echo "'active'";
  			}
  		?>><a href="judge.php">Judge Submissions</a></li>


    <li class=<?php if (strpos($actual_link,'review') !== false) {
  				echo "'active'";
  			}
  		?>><a href="review.php">Review Judgements</a></li>


    <li class=<?php if (strpos($actual_link,'problems') !== false) {
  				echo "'active'";
  			}
  		?>><a href="problems.php">Problems</a></li>


    <li class=<?php if (strpos($actual_link,'clarifications') !== false) {
  				echo "'active'";
  			}
  		?>><a href="clarifications.php">Clarifications</a></li>



    <li class=<?php if (strpos($actual_link,'standings') !== false) {
  				echo "'active'";
  			}
  		?>><a href="standings.php">Standings</a></li>


  </ul>
</div>
</div>

<!-- END OF NAVIGATION -->

<!-- START OF PANEL -->
<div class="container">
<div class="text-center">
								    
	<div class="panel panel-default">
	  		<div class="panel-body">
	  			<div class="table-responsive">
	  			<table class="table" align='center'>
	  				<tr>
	  				<?
						$freeze_diff = $contest_freeze_ts + $site_start_offset - time();
						$time_till_freeze_starts = gmdate('H:i:s', $freeze_diff);
						$time_till_contest_end = gmdate("H:i:s", $contest_end_ts - time());
						$end_diff = $contest_end_ts + $site_start_offset - time();

						if ($freeze_diff > 0) {
					                            print "<td align='right'><div class=\"tablehelp\"><h4>Freeze begins in:</h4></div></td> ";
					                            print "<td align='left'><h3>$time_till_freeze_starts</h3></td>";
					                    }
						else {
							print"<td align='right' colspan=2><div class='tablehelp'><h4>Standings Frozen</h4></div></td>";
						}
					?>
						<td align='right'><div class="tablehelp"><h4>Queued Submissions:</h4></div></td>
	  					<td align='left'><h3><?=$num_queued?></h3></td>
	  					
	  				</tr>






	  				<tr>
	  					<?php
	  					if ($end_diff > 0) {

	  						print "<td align='right'><div class=\"tablehelp\"><h4>Contest Ends in:</h4></div></td>";
	  						print "<td align='left'><h3>$time_till_contest_end</h3></td>";
	  					}

	  					else {
	  						print "<td align='right' colspan=2><div class='tablehelp'><h4>Contest is Over</h4></div></td>";

	  					}

	  					?>

	  					
	  					








	  					<td align='right'><div class="tablehelp"><h4>Unanswered Clarifications:</h4></div></td>
	  					<td align='left'><h3><?=$num_clarifications?></h3></td>
	  				</tr>

				</table>
			</div>
	 		</div>
	</div>
<!-- END OF PANEL -->

</div>
</div>
<?
	}
?>
