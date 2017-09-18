<?php
include_once("generic.inc");

function Python_init(&$problem_handle) {
	$problem_handle['preprocess'] = "python_do_nothing";
	$problem_handle['compile'] = "python_do_nothing";
	$problem_handle['execute'] = "python_execute";
	$problem_handle['post_process'] = "python_do_nothing";
	$problem_handle['copy_cmd'] = "python_copy";
	$problem_handle['process_errors'] = "generic_process_errors"; 
	$problem_handle['check_forbidden'] = "python_check_forbidden";
	$problem_handle['use_proc_fs'] = 0;
}

function python_do_nothing() {
	return "/bin/true";
}

function python_copy() {
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

function python_execute() {
    global $problem_handle;
// Embedded version assumption! -- Needs to be "parameterized" through a setting in some config file. -sb

    $command  = '"/usr/bin/python2.7 ';
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= '.py"';

    return $command;
}

function python_check_forbidden() {
	return array();
}

?>
