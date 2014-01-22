<?
# TEST COMPILE
# Copyright (C) 2002, 2003 David Whittington
# Copyright (C) 2005 Jonathan Geisler
# Copyright (C) 2005 Victor Replogle
# Copyright (C) 2005 Steve Overton
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: submissions.php

	include_once("lib/config.inc");
	include_once("lib/data.inc");

	# Set up default directories
	$problem_handle['comp_dir'] = "$base_dir/test_compile/";
	
    // check to see if a file is actually being submitted
    if ($_FILES[source_file][name] == false) {
		header("location: testcompile.php?state=1");
        exit(0);
    }
	$orig_file_name = $_FILES[source_file][name];
        $extension="";
        for ($i=strlen($_FILES[source_file][name])-1; $i>=0; $i--) {
                if ($_FILES[source_file][name][$i]==".") {
                        break;
                }
                $extension = $_FILES[source_file][name][$i].$extension;
        }
	$ts = time();
        $uploadfile = "$team_id-$ts";
		
	$query = "SELECT Ext FROM FILE_EXTENSIONS";
	$result = mysql_query($query);
   	$numrows = mysql_num_rows($result);
	$valid = false;

	for($i = 0; $i < $numrows; $i = $i + 1) {
		$row=mysql_fetch_array($result);
		if($row['Ext'] == $extension) {
			$valid = true;
		}
	}
	//If file extension exits in File_Extensions table of DB, move file for judging
	if(!$valid) {
		header("location: testcompile.php?state=3");
		exit(0);
	}
	$temp_file = $problem_handle['comp_dir'] . $uploadfile . "." . $extension;

	if(move_uploaded_file($_FILES[source_file][tmp_name],$temp_file)) {
		chmod("$base_dir/test_compile/$uploadfile.$extension", 0644);
		#Save Original File
	        $temp_store = read_entire_file($problem_handle['comp_dir'].$uploadfile.".".$extension);
		save_file($problem_handle['comp_dir'].$uploadfile.".".$extension."-orig",$temp_store);
	}
	else {
		header("location: testcompile.php?state=4");
		exit(0);
	}

	$auto_response_number = ENONE;
	$sql  = "SELECT * ";
	$sql .= "FROM FILE_EXTENSIONS ";
	$sql .= "WHERE EXT = '$extension' ";
	$sql_result = mysql_query($sql);
	if(!$sql_result){
		sql_error($sql);
    }

	//Get Language of extension
	$row = mysql_fetch_assoc($sql_result);
        $ext_id = $row['EXT_ID'];
        $sql  = "SELECT * ";
        $sql .= "FROM LANGUAGE_FILE_EXTENSIONS ";
        $sql .= "WHERE EXT_ID = $ext_id ";
        $sql_result = mysql_query($sql);
        $row = mysql_fetch_assoc($sql_result);
        $lang_id = $row['LANGUAGE_ID'];

        $sql  = "SELECT * ";
        $sql .= "FROM LANGUAGE ";
        $sql .= "WHERE LANGUAGE_ID = $lang_id ";
        $sql_result = mysql_query($sql);
        $row = mysql_fetch_assoc($sql_result);

        $lang_name = $row['LANGUAGE_NAME'];
        $max_cpu_time = $row['MAX_CPU_TIME'];

        $replace_headers = $row['REPLACE_HEADERS'];
        $check_bad_words = $row['CHECK_BAD_WORDS'];

        # The contents of the file in the judged directory
        $problem_handle['judged_source'] = read_entire_file($problem_handle['comp_dir'].$uploadfile.".".$extension);
	
        $problem_handle['file_name'] = $uploadfile;
        $problem_handle['file_extension'] = $extension;
        $submission_output = "";

	# Include the specific language file
        include_once("Lang/$lang_name.inc");

        $use_proc_fs = $problem_handle['use_proc_fs'];
        # Check for forbidden words
        if($auto_response_number == ENONE && $check_bad_words) {
		$sql  = "SELECT WORD ";
                $sql .= "FROM FORBIDDEN_WORDS ";
                $sql .= "WHERE LANGUAGE_ID = $lang_id ";
                $sql_result = mysql_query($sql);
                if(!$sql_result) {
                	sql_error($sql);
                }
                while($row = mysql_fetch_row($sql_result)) {
                	if(preg_match("/(.*$row[0].*)/", $problem_handle['judged_source'], $context)) {
                                $auto_response_number = EFORBIDDEN;
                      		$submission_output .= "Found word: $row[0]    ";
                                $submission_output .= "($context[0])\n";
                        }
                }
	}
	# Replace headers
        if($auto_response_number == ENONE && $replace_headers) {
        	$sql  = "SELECT HEADER ";
                $sql .= "FROM HEADERS ";
                $sql .= "WHERE LANGUAGE_ID = $lang_id ";
                $sql_result = mysql_query($sql);
                if(!$sql_result){
                	sql_error($sql);
                }
                $headers = array();
                while($row = mysql_fetch_row($sql_result)) {
                	array_push($headers, $row[0]);
                }
## Major boo-boo here. Blows up. Needs to be fixed. E.g.
##-----
## Fatal error: Call to undefined function: () in /home/contest/public_html/SBtest4/submit_test.php on line 136
##-----
echo "<div class = 'error'><br>Test compilation currently disabled due to problem. Sorry.<div>";
		exit(0);
#                $problem_handle['preprocess']($headers);
                save_file($problem_handle['comp_dir'].$uploadfile.".".$extension,$problem_handle['judged_source']);
	}
        # Compile
        if($auto_response_number == ENONE) {
	      $problem_handle['judged_dir'] = $problem_handle['comp_dir'];
  	      $sys_command = $problem_handle['compile']();
              $tmp = system($sys_command,$result);
              if($result == 127) {
          	    $auto_response_number = EUNKNOWN;
              	    $submission_output .= "<div class = 'error'><br>Unknown Error</div>";
		$_SESSION['compile_errors'] = $problem_handle['process_errors']($submission_output, $orig_file_name);
		header("location: testcompile.php?state=5");
		exit(0);
              }
              else if($result) {
				$auto_response_number = ECOMPILE;
              	$submission_output .= "<div class = 'error'><br>Compile Error</div>"
                read_entire_file($problem_handle['comp_dir'] . $problem_handle['file_name'] . ".err");
		$_SESSION['compile_errors'] = $problem_handle['process_errors']($submission_output, $orig_file_name);
		header("location: testcompile.php?state=5");
		exit(0);
              }
	}
	header("location: testcompile.php?state=2");
	exit(0);


# Read the entire file into a string
# Input: $filename - file path to read
function read_entire_file($filename) {
    if(file_exists($filename)){
                return file_get_contents($filename);
        }
        else{
                return "";
        }
}

# Write the submission file to the judged directory
# Input: $filename - file path to write to
#        $file - string to write to the file
function save_file($filename,$file) {
    if($handle = fopen($filename,"w+")) {
                if($file && !fwrite($handle,$file)){
                        $submission_output .= "<div class = 'error'><br>Error: Unable to write to the file!</div>";
                        fclose($handle);
                }
        }
    else{
                $submission_output .= "<div class = 'error'><br>Error: Unable to open the file!</div>";
        }
}

?>
