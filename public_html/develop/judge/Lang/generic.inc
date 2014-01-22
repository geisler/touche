<?

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

function generic_check_forbidden($compiler, $replace) {
    global $problem_handle;

    $flag = "START_PREPROC_HERE";

    $check_filename  = $problem_handle['judged_dir'];
    $check_filename .= $problem_handle['file_name'];
    $check_filename .= "_check.";
    $check_filename .= $problem_handle['file_extension'];

    $output_filename  = $problem_handle['judged_dir'];
    $output_filename .= $problem_handle['file_name'];
    $output_filename .= "_check.processed";

    $original_source = $problem_handle['judged_source'];
    $replaced_source = preg_replace("/$replace/", "$flag\n", $original_source);

    $check_file = fopen($check_filename, "w");
    if (!$check_file) {
        echo "Could not open the check forbidden words file!";
    }
    fwrite($check_file, "THIS_IS_TO_SEPARATE\n\n");
    fwrite($check_file, "$flag\n");
    fwrite($check_file, $replaced_source);
    fclose($check_file);

    $command = "$compiler -E -P $check_filename > $output_filename";

    system($command, $result);

    if (!$result) {
        $check = fopen($output_filename, "r") or die("Error opening preprocessed program");
	$contents = fread($check, filesize($output_filename));
	$preproc_array = preg_split("/$flag/", $contents);
    } else {
	echo "Could not check Forbidden words after preprocessing.<br />\n";
    }

    return $preproc_array;
}
?>
