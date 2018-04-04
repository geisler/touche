<?php
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
	include_once("lib/session.inc");
	include_once("lib/data.inc");
	include_once("judge/lib/responses.inc");

	# Set up default directories
	$problem_handle['judged_dir'] = "$base_dir/test_compile/";
	
    // check to see if a file is actually being submitted
    if ($_FILES['source_file']['name'] == false) {
		header("location: testcompile.php?state=1");
        exit(0);
    }
	$orig_file_name = $_FILES['source_file']['name'];
        preg_match("/\.(.*)$/", $orig_file_name, $matches);
	$extension = $matches[1];

	$ts = time();
        $uploadfile = "$team_id-$ts";
		
	$auto_response_number = ENONE;
	$sql  = "SELECT * ";
	$sql .= "FROM FILE_EXTENSIONS, LANGUAGE_FILE_EXTENSIONS ";
	$sql .= "WHERE EXT = '" . mysql_real_escape_string($extension) . "' ";
	$sql .= "  AND FILE_EXTENSIONS.EXT_ID = LANGUAGE_FILE_EXTENSIONS.EXT_ID";
	$sql_result = mysql_query($sql);
	if(!$sql_result){
        	sql_error($sql);
        }

	//If file extension exists in File_Extensions table of DB, move file for judging
	if(!mysql_num_rows($sql_result)) {
		header("location: testcompile.php?state=3");
		exit(0);
	}
	$temp_file = "$problem_handle[judged_dir]$uploadfile.$extension";

	if(move_uploaded_file($_FILES['source_file']['tmp_name'],$temp_file)) {
		chmod($temp_file, 0644);
		#Save Original File
	        $temp_store = read_entire_file($temp_file);
		save_file("$temp_file-orig",$temp_store);
	}
	else {
		header("location: testcompile.php?state=4");
		exit(0);
	}

	//Get Language of extension
	$row = mysql_fetch_assoc($sql_result);
        $lang_id = $row['LANGUAGE_ID'];

        $sql  = "SELECT * ";
        $sql .= "FROM LANGUAGE ";
        $sql .= "WHERE LANGUAGE_ID = $lang_id ";
        $sql_result = mysql_query($sql);
        $row = mysql_fetch_assoc($sql_result);

        $lang_name = $row['LANGUAGE_NAME'];
        $replace_headers = $row['REPLACE_HEADERS'];
        $check_bad_words = $row['CHECK_BAD_WORDS'];

        # The contents of the file in the judged directory
        $problem_handle['judged_source'] = $temp_store;
	
        $problem_handle['file_name'] = $uploadfile;
        $problem_handle['file_extension'] = $extension;
        $submission_output = "";

	# Include the specific language file
        include_once("judge/Lang/$lang_name.inc");
	$init_name = $lang_name . "_init";
	$init_name($problem_handle);

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
                $problem_handle['preprocess']($headers);
                save_file($temp_file, $problem_handle['judged_source']);
	}

        # Check for forbidden words
        if($auto_response_number == ENONE && $check_bad_words) {
		$pre_proc_array = $problem_handle['check_forbidden']();

		if (sizeof($pre_proc_array) > 0) {
		    $sql  = "SELECT WORD ";
                    $sql .= "FROM FORBIDDEN_WORDS ";
                    $sql .= "WHERE LANGUAGE_ID = $lang_id ";
                    $sql_result = mysql_query($sql);
                    if(!$sql_result) {
                	sql_error($sql);
                    }
                    while($row = mysql_fetch_row($sql_result)) {
                	if(preg_match("/(^.*$row[0].*$)/m",
				      $pre_proc_array[sizeof($pre_proc_array)-1],
				      $context))
		        {
                                $auto_response_number = EFORBIDDEN;
                      		$submission_output .= "Found word: <b>$row[0]</b>    ";
                                $submission_output .= "<pre>$context[1]</pre><br />\n";
                        }
		    }

		    if ($auto_response_number == EFORBIDDEN) {
			$_SESSION['compile_errors'] = $submission_output;
			header("location: testcompile.php?state=6");
			exit(0);
		    }
                }
	}
        # Compile
        if($auto_response_number == ENONE) {
  	      $sys_command = $problem_handle['compile']();
              $tmp = system($sys_command,$result);
              if($result == 127) {
          	    $auto_response_number = EUNKNOWN;
              	    $submission_output .= "**Unknown Error**";
		    $_SESSION['compile_errors'] = $problem_handle['process_errors']($submission_output, $orig_file_name);
		    header("location: testcompile.php?state=5");
		    exit(0);
              }
              else if($result) {
		  $auto_response_number = ECOMPILE;
              	  $submission_output .=
		      read_entire_file($problem_handle['judged_dir'] . $problem_handle['file_name'] . ".err");
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
                        $submission_output .= "Error: Unable to write to the file!";
                        fclose($handle);
                }
        }
    else{
                $submission_output .= "Error: Unable to open the file!";
        }
}

?>
