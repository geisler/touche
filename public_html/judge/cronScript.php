<?
#
# Copyright (C) 2005 Steve Overton
# Copyright (C) 2005 David Crim
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/judge.php
#
    include_once("lib/judge.inc");
#
# Set up default directories
$problem_handle['queue_dir'] = "$base_dir/queue/";
$problem_handle['judged_dir'] = "$base_dir/judged/";
$problem_handle['data_dir'] = "$base_dir/data/";

#make sure that concurent cron jobs do not run
$script = $base_dir . "/lockFile.lock";
//echo "\n$script\n\n";
$fp = fopen($script, "w+");

if (flock($fp, LOCK_EX+LOCK_NB)){


#This needs to be moved to the database - added to contest_config
#or some other appropriate table, then added to the dbcreate.sql
#as well as the judge.inc files
$USE_CHROOT = 1;

# Look for a new submission
$sql  = "SELECT * ";
$sql .= "FROM QUEUED_SUBMISSIONS, CONTEST_CONFIG WHERE TS < (START_TS + CONTEST_END_DELAY) ORDER BY TS";
$submits_result = mysql_query($sql);
if(!$submits_result){
    sql_error($sql);
}
# If there is a new submission, judge it
while($submits = mysql_fetch_assoc($submits_result)) {
    # Set variables from the submission
    $id = $submits['QUEUE_ID'];
    $team_id = $submits['TEAM_ID'];
    $problem_id = $submits['PROBLEM_ID'];
    $ts = $submits['TS'];
    $attempt = $submits['ATTEMPT'];
    $source_file = $submits['SOURCE_FILE'];
    $submitted_source = $problem_handle['queue_dir'] . $source_file;
    # Move submission from queue to judged table
    $judged_id = new_submission($id,$team_id, $problem_id,$ts,$attempt,$source_file);

    # Read in the submitted file and save it to the judged dir
    $original_source_content = read_entire_file($submitted_source);
    $judged_source = $problem_handle['judged_dir'] . $source_file;
    save_file($judged_source,$original_source_content);
	#unlink($submitted_source);

    $auto_response_number = 0;

    # Get the base and extension of the file name
    $tmp = explode(".", $source_file);
    $file_name = $tmp[0];
    $file_extension = mysql_escape_string($tmp[1]);

    $sql  = "SELECT * ";
    $sql .= "FROM FILE_EXTENSIONS, LANGUAGE_FILE_EXTENSIONS ";
    $sql .= "WHERE EXT = '$file_extension' AND FILE_EXTENSIONS.EXT_ID = LANGUAGE_FILE_EXTENSIONS.EXT_ID";
    $sql_result = mysql_query($sql);
    if(!$sql_result){
		sql_error($sql);
	}
    # Invalid submission file type
    if(mysql_num_rows($sql_result) == 0) {
		$auto_response_number = EFILETYPE;
		$submission_output = "File name: $source_file";
		update_submission($judged_id, $auto_response_number, $source_file);
    }
    else {
		$row = mysql_fetch_assoc($sql_result);
		$lang_id = $row['LANGUAGE_ID'];

		$sql  = "SELECT * ";
		$sql .= "FROM LANGUAGE ";
		$sql .= "WHERE LANGUAGE_ID = $lang_id ";
		$sql_result = mysql_query($sql);
		$row = mysql_fetch_assoc($sql_result);
	
		$lang_name = $row['LANGUAGE_NAME'];
		$max_cpu_time = $row['MAX_CPU_TIME'];
		$safe_max_cpu_time = intval(1.1 * $max_cpu_time);
		$chroot_directory = $row['CHROOT_DIRECTORY'];
		#make the chroot_directory fully qualified
		$chroot_directory = $base_dir . "/" . $chroot_directory;
		$replace_headers = $row['REPLACE_HEADERS'];
		$check_bad_words = $row['CHECK_BAD_WORDS'];
		
		echo "\nlang_id: $lang_id\nlang_name: $lang_name\n";	
		# The contents of the file in the judged directory
		$problem_handle['judged_source'] = 
			read_entire_file($judged_source);
		$problem_handle['file_name'] = $file_name;
		$problem_handle['file_extension'] = $file_extension;
	
		$submission_output = "";

		# Include the specific language file
		echo "\nright before lang include: $lang_name\n";
		echo "\ninclude_once('Lang/$lang_name.inc');\n";
		include_once("Lang/$lang_name.inc");
		$init_name = $lang_name . "_init";
		$init_name(&$problem_handle);

		$use_proc_fs = $problem_handle['use_proc_fs'];
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
			save_file($judged_source, $problem_handle['judged_source']);
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
				    if(preg_match("/(.*$row[0].*)/",
					    $pre_proc_array[sizeof($pre_proc_array)-1], 
					    $context)) {
					    $auto_response_number = EFORBIDDEN;
					    $submission_output .= "Found word: $row[0]    ";
					    $submission_output .= "($context[0])\n";
				    }
			    }
			}

			if($auto_response_number == EFORBIDDEN){
				update_submission($judged_id, $auto_response_number, $submission_output);
			}
		}
	
		# Compile
		if($auto_response_number == ENONE) {
			$sys_command = $problem_handle['compile']();
			echo "\n sys 179: $sys_command\n";
			$tmp = system($sys_command,$result);
			if($result == 127) {
				$auto_response_number = EUNKNOWN;
			}
			else if($result) {
				$auto_response_number = ECOMPILE;
				$submission_output .= 
					read_entire_file($problem_handle['judged_dir'] . 
					$problem_handle['file_name'] . ".err");
				update_submission($judged_id, $auto_response_number, $problem_handle['file_name'] . ".err");
    			}
		}
	
		# Post_process
		if($auto_response_number == ENONE) {
			$sys_command = $problem_handle['post_process']();
			$tmp = system($sys_command,$result);
			echo "\n\nsys 197: $sys_command\n\n";
			if($result == 127) {
				$auto_response_number = EUNKNOWN;
			}
			else if($result) {
			$auto_response_number = ECOMPILE;
			$submission_output = "Contents of: " . 
			$problem_handle['judged_dir'] . 
			$problem_handle['file_name'] . ".err\n";
			$submission_output = 
					read_entire_file($problem_handle['judged_dir'] . 
					$problem_handle['file_name'] . ".err");
			}
		}
		if($auto_response_number == ENONE) {

		
			$infiles = glob($problem_handle['data_dir'] . 
					$problem_id . "*.in");
			#echo "<br>Number of input files: " . count($infiles);
			# Execute
			foreach($infiles as $cur_input) {
//BUG
//need to include [-\w] to allow fot hyphens in the input / output files
				preg_match("/(\w*)\.in/", $cur_input, $tmp);
				$cur_input = $tmp[0];
				$problem_name = $tmp[1];
				echo "\ncur_input: $cur_input\n";
				$auto_response_number = ENONE;
			//      the program now runs through all data sets regardless of results
			//	if($auto_response_number == ENONE 
			//			|| $auto_response_number == EFORMAT
			//			|| $auto_response_number == ECORRECT) 
		

					$problem_handle['output']  = $problem_handle['judged_dir'];
					$problem_handle['output'] .= $problem_handle['file_name'];
					$problem_handle['output'] .= "_";
					$problem_handle['output'] .= $problem_name;
					$problem_handle['output'] .= ".out";
										
					$sys_command = $problem_handle['execute']();
					$start_times = posix_times();
					# Fork and exec
					$pid = pcntl_fork();
					if($pid != 0) {
						#parent
						$start_time = time();
					} else {
						$child_log = fopen("$base_dir/child.log", "w");
						# Child
						if($USE_CHROOT){
							#prepend the chroot directory where necessary
							#this necissitates a mirroring of the contest
							#structure inside of the chroot environment
							$arg_cmd = "$base_dir/chroot_wrapper.exe" . 
								" " .
								$use_proc_fs . " " . 
								$chroot_directory . " " .
								$sys_command . " " . 
								$problem_handle['data_dir'] . $cur_input . " " .
								$problem_handle['output'];
#make use of bash'es built in ulimit capabilities
							$args = array("-c","ulimit -t $safe_max_cpu_time;$arg_cmd");
							#before we execute, 
							#we have to move the executable
							$tmp_cmd = $problem_handle['copy_cmd']();
							system($tmp_cmd, $result);
							echo "\n\nsys 265: $tmp_cmd\n\n";
							if(!result)
							{
								print "Something went wrong with copying the executable to the chroot jail<br>";
								print "Cmd: $tmp_cmd<br>";
								print "Result: $result<br>";
							}
							#copy the data files
							$tmp_cmd = "cp ";
							$tmp_cmd .= $problem_handle['data_dir'];
							$tmp_cmd .= $cur_input;
							$tmp_cmd .= " ";
							$tmp_cmd .= $chroot_directory;
							$tmp_cmd .= $problem_handle['data_dir'];
							$tmp_cmd .= $cur_input;
							system($tmp_cmd, $result);
							echo "\n\nsys 281: $tmp_cmd\n\n";
							fwrite($child_log,
									"result of cp sys call: $result");
							fwrite($child_log, 
									"tmp_cmd: $tmp_cmd\n");
							fwrite($child_log, 
									"proc_fs: $use_proc_fs\n");
							fwrite($child_log, 
									"chroot dir: $chroot_directory\n");
							fwrite($child_log, 
									"sys_command: $sys_command\n");
							foreach($args as $arg){
								fwrite($child_log, "Args: $arg\n");
							}
							fclose($child_log);
							umask(0);
							pcntl_exec("/bin/sh", $args);
						}
						else{
							#
							# JGG
							#
							# This needs to be able to ignore stderr
							#
							$sys_command .= " < ";
							$sys_command .= $problem_handle['data_dir'];
							$sys_command .= "$input &> ";
							$sys_command .= $problem_handle['output'];
										
							$args = array("-c", $sys_command);
							pcntl_exec("/bin/sh", $args);
							fwrite($child_log,
									"not using chroot environment");
							fclose($child_log);
						}
					}
		
					# Wait for child process toexit
					# Kill it if it takes too long
					$result = 0;
					#$child_run_time = $child_start_ts;
					#while((($child_run_time - $child_start_ts) < $max_cpu_time) &&
#				while($start_time + $max_cpu_time >= time() && 
					while(!$result) {
						sleep(1);
						$result = pcntl_waitpid($pid, 
								$child_status, 
								WNOHANG);
					}
					$times = posix_times();

					echo "start time: $start_times[cutime]\n";
					echo "end time: $times[cutime]\n";
					echo "time taken: " . ($times['cutime'] - $start_times['cutime']) . "/" . $max_cpu_time*100 . "\n";
					if(($times['cutime'] - $start_times['cutime']) >= $max_cpu_time *100) {
						$submission_output = read_entire_file(
						$problem_handle['judged_dir'] . 
						$problem_handle['file_name'] . 
								"_" . $problem_name . ".out");
						$auto_response_number = ERUNLENGTH;
						
					}

					if (pcntl_wifexited($child_status)) {
						echo "child exited with status: $child_status\n";
						$run_time_errorno = pcntl_wexitstatus($child_status);
					} else if (pcntl_wifsignaled($child_status)) {
						echo "child signaled with status: $child_status\n";
						$run_time_errorno = 1000 + pcntl_wtermsig($child_status);
					} else {
						echo "child ended weirdly with status: $child_status\n";
						$run_time_errorno = 2000;
					}

					if($run_time_errorno && $auto_response_number != ERUNLENGTH) {
						$submission_output = read_entire_file(
						$problem_handle['judged_dir'] . 
						$problem_handle['file_name'] . 
								"_" . $problem_name . ".out");
						$auto_response_number = ERUNTIME;
					}
					elseif (!pcntl_waitpid($pid, $child_status, WNOHANG)){
						posix_kill($pid+1, SIGKILL);
						posix_kill($pid,  SIGKILL);
						pcntl_waitpid($pid, $child_status, WNOHANG);
						$auto_response_number = ERUNLENGTH;
						break;
					}
				
					#move the executable back out
#					$tmp_cmd = "mv ";
					$tmp_cmd = "rm -f ";
					$tmp_cmd .= $chroot_directory;
					$tmp_cmd .= $problem_handle['judged_dir'];
					$tmp_cmd .= $problem_handle['file_name'];
#					$tmp_cmd .= " ";
#					$tmp_cmd .= $problem_handle['judged_dir'];
#					$tmp_cmd .= $problem_handle['file_name'];
#					system($tmp_cmd, $result);
					#move the output files back
					$tmp_cmd = "mv ";
					$tmp_cmd .= $chroot_directory;
					$tmp_cmd .= $problem_handle['judged_dir'];
					$tmp_cmd .= "*.out";
					$tmp_cmd .= " ";
					$tmp_cmd .= $problem_handle['judged_dir'];
#					system($tmp_cmd, $result);
					#not all data sets are run regardless of results
					//if($auto_response_number == ENONE 
					//		|| $auto_response_number == EFORMAT
					//		|| $auto_response_number == ECORRECT)
						
						#ok, now we need to move the outfile back
						$chroot_filename = $chroot_directory;
						$chroot_filename .= $problem_handle['judged_dir'];
						$chroot_filename .= $problem_handle['file_name'];
						$chroot_filename .= "_$problem_name.out";
						$tmp_cmd = "cp -f $chroot_filename";
						$tmp_cmd .= " ";
						$tmp_cmd .= $problem_handle['judged_dir'];
						$tmp_cmd .= $problem_handle['file_name'];
						$tmp_cmd .= "_$problem_name.out";
						#this is somewhat of a hack to get around
						#umask issues - the default seems to be 144
						#which leave the judging user unable to read
						#the file
						chmod($chroot_filename, 0600);
						system($tmp_cmd, $result);
						echo "\n\nsys 395: $tmp_cmd\n\n";
						#Perform the diffs
						$tmp = explode(".", $cur_input);
						$outfile = $tmp[0] . ".out";
						$judge_out_file = $problem_handle['data_dir'];
						$judge_out_file .= $outfile;
						$team_out_file = $problem_handle['judged_dir'];
						$team_out_file .= $problem_handle['file_name'];
						$team_out_file .= "_$outfile";
						$diff_out_file = $problem_handle['judged_dir'];
						$diff_out_file .= $problem_handle['file_name'];
						$diff_out_file .= "_$outfile.diff";
						$cur_error_eformat = false;
						system("diff -u $judge_out_file $team_out_file > $diff_out_file", $result);
						echo "\n\nsys 409: ";
						echo "diff -u $judge_out_file $team_out_file > $diff_out_file\n\n\n";
					  	if($auto_response_number != ERUNTIME && $auto_response_number != ERUNLENGTH){ 	
							if(filesize($diff_out_file) != 0 || $result != 0){
							
								#we need to do a non-white space diff now
								$diff_no_ws_out_file = 
									$diff_out_file . ".no_ws";
								system("diff -b -B $judge_out_file $team_out_file > $diff_no_ws_out_file", $result);
								if(filesize($diff_no_ws_out_file)!= 0 || 
										$result != 0){
									$auto_response_number = EINCORRECT;
									$submission_output = 
									read_entire_file($diff_out_file);
								}
								else if($auto_response_number == ENONE
										|| $auto_response_number == EFORMAT
										|| $auto_response_number == ECORRECT) {
									$auto_response_number = EFORMAT;
								#this var holds weather we got an EFORMAT error on this paticular test.  Since this is non-fatal, we go on with any future problem sets
								#however, we want to be able to see if we have had an eformat error in the past, or was it this time around when we print out correct / incorrect solutions statements
									$cur_error_eformat = true;
								}

							}
						}
                                                if($auto_response_number == ENONE
                                                                || $auto_response_number == ECORRECT
                                                                || ($auto_response_number == EFORMAT
                                                                        && $cur_error_eformat == false)){
                                                        //correct solution
                                                        if($auto_response_number != EFORMAT){
                                                                $auto_response_number = ECORRECT;
                                                        }
                                                }
                                                else {
                                                        $cur_error_eformat = false;
                                                }

                                                $sub_source =
                                                        $problem_handle['file_name'] . "_" . $outfile;
				echo "\nauto_resopnse_number: $auto_response_number\n";	        
    				if($auto_response_number != RUNTIME){
					echo "\nerror: $run_time_errorno\n";
					update_submission($judged_id,$auto_response_number, $cur_input, $run_time_errorno);
				}
				else{
					update_submission($judged_id,$auto_response_number, $cur_input);
				}
						
			}
		}
		
    }
 
    # Unknown error has occured
    if($auto_response_number == ENONE){
	    $auto_response_number = EUNKNOWN;
	    $submission_output = read_entire_file($diff_out_file);
	    update_submission($judged_id, $auto_response_number, $cur_input);
    }
}

flock($fp, LOCK_UN);
}
fclose($fp);
# A new submission was found
# Input: $id - queue id
#        $team_id - team id number
#        $problem_id - problem id number
#        $ts - time stamp
#        $attempt - the attempt for the team and problem 
#        $source_file - the filename of the submission
function new_submission($id,
		$team_id,
		$problem_id,
		$ts,
		$attempt,
		$source_file) {
    ### Stalking the elusive bug. Put in some parameter validation. -sb 2006-10-05
    if( !($id>0) || !($team_id>0) || !($problem_id>0) ){
	echo "new_submission WARNING: id=$id, team_id=$team_id, problem_id=$problem_id<br />\n";
    }

    # Copy the row from QUEUED_SUBMISSIONS into JUDGED_SUBMISSIONS
    $sql  = "INSERT ";
    $sql .= "INTO JUDGED_SUBMISSIONS ";
    $sql .= "    (TEAM_ID,PROBLEM_ID,TS,ATTEMPT,SOURCE_FILE) ";
    $sql .= "VALUES ";
    $sql .= "    ('$team_id','$problem_id'";
    $sql .= ",'$ts','$attempt','$source_file') ";
    $result = mysql_query($sql);
    if(!$result){
	sql_error($sql);
    }

    $new_id = mysql_insert_id();

    # Delete the row from QUEUED_SUBMISSIONS
    $sql  = "DELETE ";
    $sql .= "FROM QUEUED_SUBMISSIONS ";
    $sql .= "WHERE QUEUE_ID = '$id' ";
    $result = mysql_query($sql);
    if(!$result){
	sql_error($sql);
    }

    return $new_id;
}

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
			echo "Error: Unable to write to the file!";
			fclose($handle);
		}
	}
    else{
		echo "Error: Unable to open the file!";
	}
}

# Update the judged submission
# Input: $judged_id - submission id from the judged table
#        $auto_response_number - response number
function update_submission($judged_id,$auto_response_number,$in_file, $error_no = NULL) {
    if($error_no){
	    echo "\nerrorno: $errorno\n";
	    $sql = "INSERT INTO AUTO_RESPONSES (JUDGED_ID, IN_FILE, AUTO_RESPONSE, ERROR_NO) ";
	    $sql .= "VALUES ($judged_id, '$in_file', $auto_response_number, $error_no)";
    }
    else{
    	$sql = "INSERT INTO AUTO_RESPONSES (JUDGED_ID, IN_FILE, AUTO_RESPONSE) ";
    	$sql .= "VALUES ($judged_id, '$in_file', $auto_response_number)";
    }
    $result = mysql_query($sql);
    if(!$result)
	sql_error($sql);
}

# SQL ERROR
# Input: $sql - the query with the error
function sql_error($sql) {
    echo "<br>Error in SQL command: $sql";
    $fp = fopen("errorLog.txt", "a");
    fwrite($fp, "Error in SQL command: \"$sql\" on " . date('D, F j, Y -- G:i:s') . "\n");
    fclose($fp);
    die;
}



?>
