<?

$problem_handle['use_proc_fs'] = 0;
function generic_copy() {
	global $problem_handle;
	global $chroot_directory;
	$tmp_cmd = "cp ";
	$tmp_cmd .= $problem_handle['judged_dir'];
	$tmp_cmd .= $problem_handle['file_name'];
	$tmp_cmd .= " ";
	$tmp_cmd .= $chroot_directory;
	$tmp_cmd .= $problem_handle['judged_dir'];
	$tmp_cmd .= $problem_handle['file_name'];
	return $tmp_cmd;
}
function generic_execute() {
	global $problem_handle;
    $command  = $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    return $command;
}
function generic_post_process() {
    global $problem_handle;
    $command  = "strip --strip-all ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= " &> ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".err";
    return $command;
}
function generic_process_errors($errors, $orig_file_name) {
        return preg_replace("/^[^:]+:/m", "$orig_file_name:", $errors);
}
?>
