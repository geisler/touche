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
</head>
<body bgcolor="<?=$page_bg_color?>" link="0000cc" alink="000066" vlink="0000cc">
<table width="90%" align="center" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
	<tr><td>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr bgcolor="<?=$title_bg_color?>">	
				<td>
                                <!-- Beautification hack. 2006-09-25 -sb -->
                                <img src="../images/favicon.ico">
  				<font color="#ffffff">
				<?
				if (!$contest_name)
				{?>
  				<b>No contest exists yet<!-- <?=$contest_name?></b> - <small><?=$contest_host?></small> -->
				<?}
				else { ?>
  				<b><?=$contest_name?></b> - <small><?=$contest_host?></small>
				<?}?>
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
				<a href="start.php">Start Contest</a> &nbsp;
				<a href="judge.php">Judge Submissions</a> &nbsp;
				<a href="review.php">Review Judgements</a> &nbsp;
				<a href="problems.php">Problems</a> &nbsp;
				<a href="clarifications.php">Clarifications</a> &nbsp;
				<a href="standings.php">Standings</a> &nbsp;
<?
    				    $sql  = "SELECT * ";
				    $sql .= "FROM CLARIFICATION_REQUESTS ";
				    $sql .= "WHERE ";
				    $sql .= "    RESPONSE='' ";
				    $result = mysql_query($sql);
					$num_clarifications = mysql_num_rows($result);
				    if($row = mysql_fetch_row($result)) {
					echo "<font color='#ff0000'><b>";
					echo "Clarification Requested";
					echo "</b></font>";
				    }
?>
				    
				</td>
				<td align="right" bgcolor="#dcdcdc">
				<b>Official Time: <?=date("H:i:s")?></b><br/>
<?
				$freeze_diff = $contest_freeze_ts + $site_start_offset - time();
				$time_till_freeze_starts = gmdate('H:i:s', $freeze_diff);

				if ($freeze_diff > 0) {
                                        print "Time left until freeze begins: ";
                                        print $time_till_freeze_starts;
                                        print "<br />";
                                }
				else {
					print"Standings Frozen<br>";
				}
?>
				Time Till Contest End: <?=gmdate("H:i:s", $contest_end_ts - time())?><br>
				</td>
			</tr>
			<tr>
				<td bgcolor="#dcdcdc"/>
				<td align="right" bgcolor="#dcdcdc">
				Queued Submissions: <?=$num_queued?><br>
				Unanswered Clarifications: <?=$num_clarifications?>
				</td>
			</tr>
			<tr>
				<td bgcolor="#ffffff" colspan="2">
<?
	}
?>
