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
#	include_once ("../lib/data.inc");
	include_once ("lib/session.inc");
	$result = mysql_query("SELECT * FROM CONTEST_CONFIG");
	if (mysql_num_rows($result) > 0) {
		include_once ("lib/contest_info.inc");
	}
?>

<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">
<html>
<body bgcolor="<?=$page_bg_color?>" link="0000cc" alink="000066" vlink="0000cc">
<table width="90%" align="center" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
	<tr><td>
		<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr bgcolor="<?=$title_bg_color?>">	
				<td>
				<!-- Beautification hack. 2006-09-25 -sb -->
                <img src="../images/favicon.ico">

  				<font color="#ffffff">
				<?if (!$contest_name)
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
 					 <b>JUDGE/ADMIN</b>
 					 </font>
				</td>
			</tr>
			<tr>	
				<td bgcolor="#dcdcdc" align="left"><b>
				<a href="setup_contest.php">Edit contest details</a> &nbsp;
  				<a href="setup_problems.php">Problems</a> &nbsp;
				<a href="setup_data_sets.php">Data Sets</a> &nbsp;
  				<a href="setup_site.php">Sites</a> &nbsp; 
  				<a href="setup_teams.php">Teams</a> &nbsp;  
  				<a href="setup_categories.php">Categories</a> &nbsp;
				<a href="setup_team_category.php">Team Category</a> &nbsp;
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
				<a href="setup_headers.php">Headers</a> &nbsp;
				<?}
				if ($contest_stuff['bad_words'] > 0) { ?>
				<a href="setup_forbidden.php">Forbidden</a> &nbsp;
				<?} ?>
				<a href="misc.php">Misc</a> &nbsp;
				</b>
				
				</td>
				<td align="right" bgcolor="#dcdcdc">
				<b>Official Time: <?=date("H:i:s")?></b>
				</td>
			</tr>
			<tr>
				<td bgcolor="#ffffff" colspan="2">
