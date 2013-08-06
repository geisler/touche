<?
#
# See the file "COPYING" for further information about the copyright
# and warranty status of this work.
#
# arch-tag: admin/misc.php
#
include("lib/admin_config.inc");
include("lib/data.inc");
include("lib/session.inc");
include("lib/header.inc");

if($_SERVER['REQUEST_METHOD'] == 'POST' && !$_POST['undo']) {
	echo "<center><h3>processing submissions<br/>this will take a minute</h3></center>\n";

#copy the judge table and the response table
	system("mysql --password=pc2bgone -u root $db_name < rejudge.sql", $result);
	if ($result){
		echo "<p>mysql --password=pc2bgone -u root $db_name < rejudge.sql</p><p>error code: $result</p>";
	}

#populate copy tables	
	$sql = "INSERT INTO AUTO_RESPONSES_COPY ";
	$sql .= "SELECT * FROM AUTO_RESPONSES";
	$insert_result = mysql_query($sql);
        if(!$insert_result) {
        	sql_error($sql);
        }

	$sql = "INSERT INTO JUDGED_SUBMISSIONS_COPY ";
	$sql .= "SELECT * FROM JUDGED_SUBMISSIONS";
        $insert_result = mysql_query($sql);
        if(!$insert_result) {
                sql_error($sql);
        }

	$sql = "DELETE FROM JUDGED_SUBMISSIONS";
	$delete_result = mysql_query($sql);
	if(!$delete_result){
                sql_error($sql);
        }

	$sql = "DELETE FROM AUTO_RESPONSES";
        $delete_result = mysql_query($sql);
        if(!$delete_result){
                sql_error($sql);
        }

#move the judged dirs, and make a new one
	$sys_cmd = "mv $base_dir/judged $base_dir/judged_copy";
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);

	$sys_cmd = "mv $base_dir/cpp_jail$base_dir/judged $base_dir/cpp_jail$base_dir/judged_copy";
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);
	
	$sys_cmd = "mv $base_dir/c_jail$base_dir/judged $base_dir/c_jail$base_dir/judged_copy";
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);
	
	$sys_cmd = "mv $base_dir/java_jail$base_dir/judged $base_dir/java_jail$base_dir/judged_copy";
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);
	
	$sys_cmd = "mkdir $base_dir/judged"; 
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

	$sys_cmd = "mkdir $base_dir/cpp_jail$base_dir/judged"; 
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

	$sys_cmd = "mkdir $base_dir/c_jail$base_dir/judged"; 
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

	$sys_cmd = "mkdir $base_dir/java_jail$base_dir/judged"; 
	//echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

#stop cron
	//$sys_cmd

#repopulate the queue table	
	$sql = "INSERT INTO `QUEUED_SUBMISSIONS` (`TEAM_ID`, `PROBLEM_ID`, `TS`, ";
	$sql .= "`ATTEMPT`, `SOURCE_FILE`) ";
	$sql .= "SELECT TEAM_ID, PROBLEM_ID, TS, ATTEMPT, SOURCE_FILE ";
	$sql .= "FROM JUDGED_SUBMISSIONS_COPY";
	$insert_result = mysql_query($sql);
        if(!$insert_result) {
                sql_error($sql);
        }

#run cronscript
	$rejudge = read_entire_file ("$base_dir/start_contest.crontab");
	$rejudge = preg_replace("/\*/", "", $rejudge);
	$rejudge = preg_replace("/>.*/", "", $rejudge);
	echo "<br/>rejudge: $rejudge <br/>";
	system($rejudge, $result);
	echo"<center>finished</center>";
	echo "<a href='review.php'>Go to review page</a>";
	
}
elseif($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['undo']){

#copy back from copy tables and drop them
#this calls a script to do the sql
#it was done from php at first, but this didn't work	
	system("mysql --password=pc2bgone -u root $db_name < undo.sql", $result);
	$sql = "DELETE FROM JUDGED_SUBMISSIONS";
	echo "$sql;<br/>";
//        $sql_result = mysql_query($sql);
//        if(!$insert_result) {
//                sql_error($sql);
//        }
	
	$sql = "INSERT INTO JUDGED_SUBMISSIONS 
		SELECT * FROM JUDGED_SUBMISSIONS_COPY";
	echo "$sql;<br/>";
//        $sql_result = mysql_query($sql);
//        if(!$insert_result) {
//                sql_error($sql);
//        }

	$sql = "DROP TABLE `JUDGED_SUBMISSIONS_COPY`";
	echo "$sql;<br/>";
//        $sql_result = mysql_query($sql);
//        if(!$insert_result) {
//                sql_error($sql);
//        }

	$sql = "DELETE FROM AUTO_RESPONSES";
	echo "$sql;<br/>";
//        $sql_result = mysql_query($sql);
//        if(!$insert_result) {
//               sql_error($sql);
//        }

	$sql = "INSERT INTO AUTO_RESPONSES
		SELECT * FROM AUTO_RESPONSES_COPY";
	echo "$sql;<br/>";
//        $sql_result = mysql_query($sql);
//        if(!$insert_result) {
//                sql_error($sql);
//        }
	
	$sql = "DROP TABLE `AUTO_RESPONSES_COPY`";
	echo "$sql;<br/>";
//        $sql_result = mysql_query($sql);
//        if(!$insert_result) {
//                sql_error($sql);
//        }

#move all directorys back, and delete copies
	echo "these system commands should be executed just fine";
	$sys_cmd = "rm -r $base_dir/judged";
	echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);

	$sys_cmd = "mv $base_dir/judged_copy $base_dir/judged"; 
	echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

	$sys_cmd = "rm -r $base_dir/cpp_jail$base_dir/judged"; 
	echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

	$sys_cmd = "rm -r $base_dir/c_jail$base_dir/judged"; 
	echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

	$sys_cmd = "rm -r $base_dir/java_jail$base_dir/judged"; 
	echo "<br/>sys_cmd: $sys_cmd <br/>";
        system($sys_cmd, $result);

	$sys_cmd = "mv $base_dir/cpp_jail$base_dir/judged_copy $base_dir/cpp_jail$base_dir/judged";
	echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);
	
	$sys_cmd = "mv $base_dir/c_jail$base_dir/judged_copy $base_dir/c_jail$base_dir/judged";
	echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);
	
	$sys_cmd = "mv $base_dir/java_jail$base_dir/judged_copy $base_dir/java_jail$base_dir/judged";
	echo "<br/>sys_cmd: $sys_cmd <br/>";
	system($sys_cmd, $result);
	
}
include("lib/footer.inc");

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



# SQL ERROR
# Input: $sql - the query with the error
function sql_error($sql) {
    echo "<br>Error in SQL command: $sql";
    die;
}

?>

