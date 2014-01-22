<?
include_once("generic.inc");

function C_init($problem_handle) {
	$problem_handle['preprocess'] = "c_preprocess";
	$problem_handle['compile'] = "c_compile";
	$problem_handle['execute'] = "generic_execute";
	$problem_handle['post_process'] = "generic_post_process";
	$problem_handle['copy_cmd'] = "generic_copy";
	$problem_handle['process_errors'] = "generic_process_errors";
	$problem_handle['use_proc_fs'] = 0;
	$problem_handle['check_forbidden'] = "c_check_forbidden";
}

function c_preprocess($replace_headers) {
    global $problem_handle;
    $replace_text = "";
    foreach($replace_headers as $header) {
        $replace_text .= "#include <$header>\n";
    }

    $pattern = "/#include\s*[<\"](\s|\w|\.)+[\">]/";
    $replace = "";
    $replace_text.= preg_replace($pattern,
        $replace,
        $problem_handle['judged_source']);
    $problem_handle['judged_source'] = $replace_text;
}

function c_compile() {
    global $problem_handle;
    $command  = "gcc ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".";
    $command .= $problem_handle['file_extension'];
    $command .= " -lm -o ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= " &> ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".err";
    return $command;
}

function c_check_forbidden() {
    return generic_check_forbidden("gcc", "#include");
}
?>
