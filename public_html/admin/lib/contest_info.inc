<?
#
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2003, 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: lib/contest_info.inc
#

$contest_info = mysql_query("SELECT * FROM CONTEST_CONFIG");
if (!$contest_info) {
    print "Sorry.  Database request (CONTEST_CONFIG) failed.  Retrying...";
    exit;
}

$row = mysql_fetch_assoc($contest_info);
    if (mysql_num_rows($contest_info) < 1) {
        print "Sorry.  No contest configuration information exists.<br /> ";
        print "Please contact a system administrator.";
        #exit;
    }

    $contest_host = $row['HOST'];
    $contest_name = $row['CONTEST_NAME'];
    $contest_date = $row['CONTEST_DATE'];
    list ($contest_year, $contest_month, $contest_day)
        = split('-', $contest_date);

    $contest_start_time = $row['START_TIME'];
    list ($contest_start_hour, $contest_start_minute)
        = split(':', $contest_start_time);
    $contest_freeze_time = $row['FREEZE_DELAY'];
    $contest_end_time = $row['CONTEST_END_DELAY'];
    $base_dir = $row['BASE_DIRECTORY'];

    $contest_start_ts = mktime($contest_start_hour,
                               $contest_start_minute,
                               0,
                               $contest_month,
                               $contest_day,
                               $contest_year);
    $contest_freeze_ts = $contest_start_ts + $contest_freeze_time;
    $contest_end_ts = $contest_start_ts + $contest_end_time;
?>
