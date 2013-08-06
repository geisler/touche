<?
include_once("generic.inc");

function JAVA_init($problem_handle) {
	$problem_handle['preprocess'] = "java_preprocess";
	$problem_handle['compile'] = "java_compile";
	$problem_handle['execute'] = "java_execute";
	$problem_handle['post_process'] = "java_post_process";
	$problem_handle['copy_cmd'] = "java_copy";
	$problem_handle['use_proc_fs'] = 1; 
	$problem_handle['process_errors'] = "generic_process_errors"; 
	$problem_handle['check_forbidden'] = "java_check_forbidden";
}

function java_copy(){
	global $problem_handle;
	global $chroot_directory;
	$tmp_cmd = "cp -pr ";
	$tmp_cmd .= $problem_handle['judged_dir'];
	$tmp_cmd .= $problem_handle['file_name'];
	$tmp_cmd .= " ";
	$tmp_cmd .= $chroot_directory;
	$tmp_cmd .= $problem_handle['judged_dir'];
	$tmp_cmd .= $problem_handle['file_name'];
	return $tmp_cmd;
}

function java_preprocess($replace_headers) {
    global $problem_handle;
    $pattern = "/import\s+\S+\s*/";
    $replace = "";
    $replace_text = "";
    foreach($replace_headers as $header) {
	$replace_text .= "import $header;\n";
    }

    $replace_text .= preg_replace($pattern,
	$replace,
	$problem_handle['judged_source']);
    $problem_handle['judged_source'] = $replace_text;
}

function java_compile() {
    global $problem_handle;
    mkdir($problem_handle['judged_dir'] . $problem_handle['file_name'], 0700);

// Embedded version assumption! -- Needs to be "parameterized" through a setting in some config file. -sb
#    $command  = "/usr/java/jdk1.5.0_02/bin/javac ";
    $command  = "/opt/sun-jdk-1.6.0.15/bin/javac ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".java -d ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= " &> ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".err";
    return $command;
}

function java_execute() {
    global $problem_handle;
    $mem_size = "128M";
// Embedded version assumption! -- Needs to be "parameterized" through a setting in some config file. -sb
    $command  = "\"/opt/sun-jdk-1.6.0.15/bin/java -Xms$mem_size -Xmx$mem_size -jar ";
    $command .= $problem_handle['jar_filename'] . "\"";
    return $command;
}

function java_post_process() {
    global $problem_handle;
    $manifest_filename  = $problem_handle['judged_dir'];
    $manifest_filename .= $problem_handle['file_name'];
    $manifest_filename .= "/manifest.mf";

    # JGG
    #
    # This assumes two critical things:
    #   1. The first class name contains the main method.  We don't
    #      know where to look otherwise.
    #   2. The same first class is not declared as public.  The
    #      compiler will not let us compile a public class without
    #      using the same name as the class for the filename!!!
    #
    #preg_match("/class\s+(\w+)/",
    #	$problem_handle['judged_source'],
    #	$classes);
    #$main_class = $classes[1];
    $main_class = "Main";
    echo "<br>PostProcess:: main_class = $main_class<br>\n";
    $output = fopen($manifest_filename,"w+");
    if(!$output) {
	echo "Error: Could not open file";
	die("Error creating the manifest list file");
    }
    fwrite($output, "Manifest-Version: 1.0\n");
    fwrite($output, "Main-Class: $main_class\n");
    fclose($output);
	
    $problem_handle['jar_filename']  = $problem_handle['judged_dir'];
    $problem_handle['jar_filename'] .= $problem_handle['file_name'];
    $problem_handle['jar_filename'] .= "/$main_class.jar";

    $command  = "jar cfm ";
    $command .= $problem_handle['jar_filename'];
    $command .= " $manifest_filename -C ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= "/ . &> ";
    $command .= $problem_handle['judged_dir'];
    $command .= $problem_handle['file_name'];
    $command .= ".err";
    return $command;
}

function java_check_forbidden() {
    return array();
}
?>
