<?php
include_once("generic.inc");

function Python3_init(&$problem_handle) {
	$problem_handle['preprocess'] = "python3_do_nothing";
	$problem_handle['compile'] = "python3_do_nothing";
	$problem_handle['execute'] = "python3_execute";
	$problem_handle['post_process'] = "python3_do_nothing";
	$problem_handle['copy_cmd'] = "python3_copy";
	$problem_handle['process_errors'] = "generic_process_errors"; 
	$problem_handle['check_forbidden'] = "python3_check_forbidden";
	$problem_handle['use_proc_fs'] = 2;
}

function python3_do_nothing() {
	return "/bin/true";
}

function python3_copy() {
	global $problem_handle;
	global $chroot_directory;
	$tmp_cmd = "cp ";
	$tmp_cmd .= $problem_handle['judged_dir'];
	$tmp_cmd .= $problem_handle['file_name'];
	$tmp_cmd .= ".py ";
	$tmp_cmd .= $chroot_directory;
	$tmp_cmd .= $problem_handle['judged_dir'];
	$tmp_cmd .= $problem_handle['file_name'];
	$tmp_cmd .= ".py";
	return $tmp_cmd;
}

function python3_execute() {
    global $problem_handle;
// Embedded version assumption! -- Needs to be "parameterized" through a setting in some config file. -sb

    $command  = '"/usr/bin/python3.4m ';
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= '.py"';

    return $command;
}

function python3_check_forbidden() {
	return array();
}

?>
