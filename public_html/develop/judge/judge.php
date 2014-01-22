<?
#
# Copyright (C) 2005 Steve Overton
# Copyright (C) 2005 David Crim
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/judge.php
#
    include_once("lib/config.inc");
    include_once("lib/judge.inc");
    include_once("lib/header.inc");
#

judge_header(10);
# Set up default directories
$problem_handle['queue_dir'] = "$base_dir/queue/";
$problem_handle['judged_dir'] = "$base_dir/judged/";
$problem_handle['data_dir'] = "$base_dir/data/";

$page = "judge.php";
include_once("lib/submissions.inc")

?>
