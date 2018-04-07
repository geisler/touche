<?php
include_once("generic.inc");

function CXX_init(&$problem_handle) {
	$problem_handle['preprocess'] = "cxx_preprocess";
	$problem_handle['compile'] = "cxx_compile";
	$problem_handle['execute'] = "generic_execute";
	$problem_handle['post_process'] = "generic_post_process";
	$problem_handle['copy_cmd'] = "generic_copy";
	$problem_handle['process_errors'] = "generic_process_errors";
	$problem_handle['use_proc_fs'] = 0;
	$problem_handle['check_forbidden'] = "cxx_check_forbidden";
	$problem_handle['gcc_path'] = "/usr/x86_64-pc-linux-gnu/gcc-bin/6.4.0";
}

function cxx_preprocess($replace_headers) {
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

function cxx_compile() {
	global $problem_handle;
    $command  = "$problem_handle[gcc_path]/g++ ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".";
    $command .= $problem_handle['file_extension'];
    $command .= " -std=c++14 ";
    $command .= " -O3 ";
    $command .= " -fno-asm -lm -w -o ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= " &> ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".err";
    return $command;
}

function cxx_check_forbidden() {
    return generic_check_forbidden("g++", "#include");
}
?>
