<?
include_once("lib/config.inc");
include_once("lib/judge.inc");
include_once("lib/header.inc");
include_once("lib/database.inc");
judge_header(0);
$problem_handle['queue_dir'] = "$base_dir/queue/";
$problem_handle['judged_dir'] = "$base_dir/judged/";
$problem_handle['data_dir'] = "$base_dir/data/";

#
#
if($_GET){
   
    $sql = "SELECT * ";
    $sql .= "FROM JUDGED_SUBMISSIONS ";
    $sql .= "WHERE JUDGED_ID = " . $_GET['judged_id'];

    $sql_result = mysql_query($sql);

    if(!$sql_result)
    	sql_error($sql);

    $row = mysql_fetch_assoc($sql_result);
    $source_file = $row['SOURCE_FILE'];
    $viewed = $row['VIEWED'];
    $viewed++;
    
    $sql  = "UPDATE JUDGED_SUBMISSIONS ";
    $sql .= "SET VIEWED = $viewed ";
    $sql .= "WHERE JUDGED_ID = $_GET[judged_id] ";
    $result = mysql_query($sql);
    if(!$result) {
        sql_error($sql);
    }



    $sql = "SELECT * ";
    $sql .= "FROM PROBLEMS ";
    $sql .= "WHERE PROBLEM_ID = '" . $_GET['problem'] . "'";

    $sql_result = mysql_query($sql);
    
    if(!$sql_result)
    	sql_error($sql);

    $row = mysql_fetch_assoc($sql_result);

    $problem_name = $row['PROBLEM_NAME'];
    $problem_id = $row['PROBLEM_ID'];

    if(!$sql_result)
        sql_error($sql);

    $row = mysql_fetch_assoc($sql_result);

    $auto_response = $row['RESPONSE'];
    
    echo "<table align=center bgcolor=#ffffff cellpadding=0
                        cellspacing=0 border=0><tr><td>\n";
    echo "<table width=100% cellpadding=5
                        cellspacing=1 border=0>\n";
    echo "<tr><td bgcolor=$hd_bg_color1 align=center colspan=2>\n";
    echo "<font color=$hd_txt_color1><b>\n";
    echo "Judging Submission\n";
    echo "</b></td></tr></table>\n";
    echo "<table width=100% border=0>\n";
    echo "<tr><td bgcolor=$data_bg_color1>Submission ID:</td>\n";
    echo "<td bgcolor=$data_bg_color1>" . $_GET['judged_id'] . "</td></tr>\n";
    echo "<tr><td bgcolor=$data_bg_color1>Team:</td>\n";
    echo "<td bgcolor=$data_bg_color1>" . $_GET['team_id'] . "</td></tr>\n";
    echo "<tr><td bgcolor=$data_bg_color1>Problem:</td>\n";
    echo "<td bgcolor=$data_bg_color1>$problem_name</td></tr>\n";
    echo "<tr><td bgcolor=$data_bg_color1>Attempt:</td>\n";
    echo "<td bgcolor=$data_bg_color1>" . $_GET['attempt'] . "</td></tr>\n";
    echo "<tr><td bgcolor=$data_bg_color1>Source:</td>\n";
    echo "<td  bgcolor=$data_bg_color1><a href='judge_output.php?problem=$problem_id&sub_source=$source_file&format=2' target='blank'> $source_file </a></td></tr>";
    echo "<table width = 100% border=0 cellpadding=5><tr>";
    echo "<td bgcolor=$hd_bg_color2><center><b>";
    echo "<font color=$hd_txt_color2>Problem Notes:</font>";
    echo "</b></center></td></tr></table>\n";
    echo "<table><tr><td><textarea rows=4 cols=62 readonly>";
    echo $row['PROBLEM_NOTE'] . "</textarea></table>";
    
    $sql = "SELECT * ";
    $sql .= "FROM AUTO_RESPONSES AR INNER JOIN RESPONSES R ON R.RESPONSE_ID = AR.AUTO_RESPONSE ";
    $sql .= "WHERE AR.JUDGED_ID = $_GET[judged_id]";

    $sql_result = mysql_query($sql);
    if(!$sql_result)
        sql_error($sql);

    $auto_response_id = 10;

    while($row = mysql_fetch_assoc($sql_result)){
	if($auto_response_id > $row['AUTO_RESPONSE'])
		$auto_response_id = $row['AUTO_RESPONSE'];
	switch($row['AUTO_RESPONSE']){
		case EFORBIDDEN:
			echo "<table border=0 width=100% cellpadding=5>\n";
                	echo "<tr cellpadding=5 bgcolor=$hd_bg_color2>\n";
                	echo "<td align=center colspan=2>\n";
	                echo "<font color=$hd_txt_color2>\n";
	                echo "<b>Forbidden Word in Source</b>";
	                echo "</td></tr>\n";
			echo "<tr><td><textarea rows=15 cols=62 readonly>$row[IN_FILE]</textarea>";
			echo "</td></tr></table>\n";
			break;
		 
		case ECOMPILE:
                        echo "<table border=0 width=100% cellpadding=5>\n";
                        echo "<tr cellpadding=5 bgcolor=$hd_bg_color2>\n";
                        echo "<td align=center colspan=2>\n";
                        echo "<font color=$hd_txt_color2>\n";
                        echo "<b>Compile Error</b>";
                        echo "</td></tr>\n";
                        echo "<tr><td><textarea rows=15 cols=62 readonly>" . read_entire_file($problem_handle['judged_dir'] . $row['IN_FILE']);
                        echo "</textarea></td></tr></table>\n";
                        break;
		
		case EFILETYPE:
			echo "<table border=0 width=100% cellpadding=5>\n";
                	echo "<tr cellpadding=5 bgcolor=$hd_bg_color2>\n";
                	echo "<td align=center colspan=2>\n";
	                echo "<font color=$hd_txt_color2>\n";
	                echo "<b>Undefined File Type</b>";
	                echo "</td></tr></table>\n";
			echo "<table border=0 width=100%>\n";
                        echo "<tr><td bgcolor=$data_bg_color1>";
			echo "<font color=$data_txt_color4>";
                        echo "File Name: $row[IN_FILE]</font></td></tr></table>\n";
			break;
		case ERUNTIME:
			$know_output = preg_replace("/\.in/", ".out", $row['IN_FILE']);
			echo "<table border=0 width=100% cellpadding=5>\n";
                        echo "<tr cellpadding=5 bgcolor=$hd_bg_color2>\n";
                        echo "<td align=center colspan=2>\n";
                        echo "<font color=$hd_txt_color2>\n";
                        echo "<b>Data Set: $know_output</b>";
                        echo "</td></tr></table>\n";
                        echo "<table border=0 width=100%>\n";
                        echo "<tr><td bgcolor=$data_bg_color1>";
			echo "<font color=$data_txt_color4>";
                        echo "Runtime Error Number: $row[ERROR_NO]</font></td></tr></table>\n";
			break;
	
		case ERUNLENGTH:
			$know_output = preg_replace("/\.in/", ".out", $row['IN_FILE']);
                        echo "<table border=0 width=100% cellpadding=5>\n";
                        echo "<tr cellpadding=5 bgcolor=$hd_bg_color2>\n";
                        echo "<td align=center colspan=2>\n";
                        echo "<font color=$hd_txt_color2>\n";
                        echo "<b>Data Set: $know_output</b>";
                        echo "</td></tr></table>\n";
                        echo "<table border=0 width=100%>\n";
                        echo "<tr><td bgcolor=$data_bg_color1>";
			echo "<font color=$data_txt_color4>";
                        echo "The time limit was exceeded</font></td></tr></table>\n";
			break;
	
		default:
			$know_output = preg_replace("/\.in/", ".out", $row['IN_FILE']);
			$source_file = preg_replace("/\.(cc|cpp|c|java)/", "", $source_file);	
			$program_output = $problem_handle['judged_dir']	.  $source_file . "_" . $know_output;
			$diff_out_file = $program_output . ".diff";
			$diff_no_ws_out_file = $diff_out_file . ".no_ws";
	
			echo "<table border=0 width=100% cellpadding=5>\n";
		        echo "<tr cellpadding=5 bgcolor=$hd_bg_color2>\n";
		        echo "<td align=center colspan=2>\n";
		        echo "<font color=$hd_txt_color2>\n";
		        echo "<b>Comparing Data Set: $know_output</b>";
		        echo "</td></tr></table>\n";
	
			echo "<table border=0 width=100%>\n";
	
			if(filesize($diff_out_file) != 0){
				echo "<tr><td bgcolor=$data_bg_color1>";
		                echo "<font color=$data_txt_color4>";
		                if(file_exists($diff_no_ws_out_file)){ 
					echo "Standard diff failed</font></td></tr>\n";
					echo "<tr><td><textarea rows=15 cols=62 readonly>";
					echo read_entire_file($diff_out_file);
					echo "</textarea></td></tr>";
			
					if(filesize($diff_no_ws_out_file)!= 0){
		 			 	 echo "<tr><td bgcolor=$data_bg_color1>";
			                         echo "<font color=$data_txt_color4>";
			                         echo "White space diff failed";
		        	                 echo "</font></td></tr>";
						 echo "<tr><td bgcolor=$data_bg_color1>";
                                                 echo "<font color=$data_txt_color4>";
                                                 echo "<b>Incorrect Output</b>";
                                                 echo "</font></td></tr>";

					}
					else{
						echo "<tr><td bgcolor=$data_bg_color1>";
			                        echo "<font color=$data_txt_color3>";
		        	                echo "White space diff succedded";
		                	        echo "</font></td></tr>";
						echo "<tr><td bgcolor=$data_bg_color1>";
						echo "<font color=$data_txt_color4>";
		        	                echo "<b>Format Error</b>";
		                	        echo "</font></td></tr>";
						$auto_response_id = EFORMAT;		
					}
				}
			}
			else{
				echo "<tr><td bgcolor=$data_bg_color1>\n";
		                echo "<b><font color=$data_txt_color3>";
		                echo "Correct Solution</font></b></td></tr>\n";
			}
			echo "<tr><td bgcolor=$data_bg_color1>";
		        echo "<a href='judge_output.php?problem=$problem_name&judge_source=$know_output&sub_source=$source_file" . "_" . "$know_output&format=1'target='blank'>Output Files</a>";
		        echo "</td></tr></table>";
	}	
    }
    echo "<table width=100% cellpadding=5 border=0>\n";
    echo "<tr><td align=center bgcolor=$hd_bg_color2>\n";
    echo "<font color=$hd_txt_color2><b>";
    echo "Overall Result of the Attempt</b></font></td></tr>\n";
    echo "</table><table border=0 width=100%>\n";
    echo "<tr><td bgcolor=$data_bg_color1>Suggested Result: ";

    if($auto_response_id == 1)
	echo "<font color=$data_txt_color3><b>";
    else
	echo "<font color=$data_txt_color4><b>";

    echo $auto_response;
    echo "</b></font></td></tr>";
    echo "</b></font></td></tr>";
    echo "<form method='POST' name='testing' action='$_GET[page]'>\n";
    echo "<tr><td bgcolor=$data_bg_color1>Final Result: ";
    echo "<select name='result'>";

    $sql = "SELECT * ";
    $sql .= "FROM RESPONSES ";

    $sql_result = mysql_query($sql);

    if(!$sql_result)
        sql_error($sql);
 
    while($row = mysql_fetch_assoc($sql_result)){
	echo "<option value=" . $row['RESPONSE_ID'];
        if($auto_response_id == $row['RESPONSE_ID']) {
        	echo " selected ";
        }
        echo ">" . $row['RESPONSE'] . "</option>";
    }
    echo "</select>";
    echo "</td></tr></table>";
    //this now goes after each data set
    /*if($auto_response_id != 1){
	echo "<table><tr><td><textarea rows=15 cols=62 readonly>";
        echo $error_output;
        if($auto_response_id == 8) {
        	echo "Runtime errorno: put number in db";
        }
        echo "</textarea></td></tr></table>\n";
    }*/
   
    echo "<table border=0 width=100%>\n";
    echo "<tr><td><center>";
    echo "<input type='hidden' name='judged_id' value=" . $_GET['judged_id'] . ">";
    echo "<input type='submit' name='submit' value='Submit Results'>";
    echo "</td></tr></center></form></table>\n";

					    

}
else
	echo "No submission was selected";

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
