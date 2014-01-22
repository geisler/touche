<?php
#
# Copyright (C) 2002 David Whittington
# Copyright (C) 2004 Jonathan Geisler
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: lib/config.inc
#
	include_once("database.inc");

	$page_bg_color  = "#cccccc";
	$body_bg_color  = "#ffffff";
	$title_bg_color = "#3480ba";
	$hd_bg_color1   = "#3480ba";
	$hd_bg_color2   = "#cccc99";
	$data_bg_color1 = "#dddddd";
	$data_bg_color2 = "#cccc99";

	$page_txt_color  = "#000000";
	$body_txt_color  = "#ffffff";
	$title_txt_color = "#ffffff";
	$hd_txt_color1   = "#ffffff";
	$hd_txt_color2   = "#000000";
	$data_txt_color1 = "#000000";
	$data_txt_color2 = "#000000";

	$judge_user = "judge";
	$judge_pass = "tdz7q7rj";

#
# $base_dir must be updated for the machine it is installed on.
# Intentionally commenting it out in "official" sources to force
# others to put in the right location for their archive.
#
#	$base_dir = "/home/jgeisler/ProgContest/judge/contest";
?>
