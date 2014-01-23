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

$user = `whoami`;
$user = str_replace("\n","",$user);
$user = str_replace(" ","",$user);		
		
if ($_POST) {
        $ext_hour = $_POST['ext_hour'];
        $ext_minute = $_POST['ext_minute'];
        $ext_second = $_POST['ext_second'];


        $sql = mysql_query("SELECT * FROM SITE WHERE SITE_ID = 1");
	$row = mysql_fetch_assoc($sql);	
	$contest_np = $row['CONTEST_NAME'];
	$freeze_hour = (int)($row['FREEZE_DELAY']/3600);
        $freeze_minute = ((int)($row['FREEZE_DELAY']/60))%60;
	$freeze_second = $row['FREEZE_DELAY'] - ($freeze_hour*3600 + $freeze_minute*60);
	$end_hour = (int)($row['CONTEST_END_DELAY']/3600);
        $end_minute = ((int)($row['CONTEST_END_DELAY']/60))%60;
        $end_second = $row['CONTEST_END_DELAY'] - ($freeze_hour*3600 + $freeze_minute*60);
	

        $freeze_delay = ($freeze_hour + $ext_hour)*3600 + ($freeze_minute + $ext_minute)*60 + ($freeze_second + $ext_second);
        $contest_delay =($end_hour + $ext_hour)*3600 + ($end_minute + $ext_minute)*60 + ($end_second + $ext_second);

if($_POST['B1']) {
	$exist = $ext_hour + $ext_minute + $ext_second;
	if($exist > 0) {
		$sql = "UPDATE SITE ";
		$sql .= "SET FREEZE_DELAY = '$freeze_delay',";
		$sql .= "CONTEST_END_DELAY = '$contest_delay' ";
		$sql .= "WHERE CONTEST_NAME = '$contest_np'";
		$good = mysql_query($sql);
		if(!$good) {
			echo "<div class='error'><br>There was an error and the contest was not extended!!!</div>";
		}
		else {
			echo "<div class='success'><br>Contest Extended Successfully.</div>";
		}
    	}
}
elseif($_POST['B2']) {
#$delete = mysql_query("UPDATE SITE WHERE SITE_ID = 1 SET FREEZE_DELAY = '0', CONTEST_END_DELAY = '0', START_TS = '0', HAS_STARTED = '0' WHERE CONTEST_NAME = '$contest_name'");
$delete = mysql_query("UPDATE SITE WHERE SITE_ID = 1 SET START_TS = '0', HAS_STARTED = '0' WHERE CONTEST_NAME = '$contest_name'");
if(!$delete) {
   echo "<div class='error'><br>Error! could not clear the info!!!</div>";
}
$delete = mysql_query("DELETE FROM CLARIFICATION_REQUESTS");
if(!$delete) {
   echo "<div class='error'><br>Error! could not clear the info!!!</div>";
}
$delete = mysql_query("DELETE FROM JUDGED_SUBMISSIONS");
if(!$delete) {
   echo "<div class='error'><br>Error! could not clear the info!!!</div>";
}
$delete = mysql_query("DELETE FROM QUEUED_SUBMISSIONS");
if(!$delete) {
   echo "<div class='error'><br>Error! could not clear the info!!!</div>";
}
$delete = mysql_query("UPDATE SITE SET START_TS = '0', HAS_STARTED = '0'");
if(!$delete) {
   echo "<div class='error'><br>Error! could not clear the info!!!</div>";
} else {
   echo "<div class='success'><br>Contest Cleared Successfully!</div>";
  }
}
elseif($_POST['B3']) {
#Clone the contest here
$contest_clone_name = $_POST['clone_name'];
$db_clone_name = preg_replace("/ /", "_", $contest_clone_name);
$contest_clone_es = preg_replace("/ /", "\ ", $contest_clone_name);
$contest_dir = "../../../$contest_clone_es";

echo "Creating clone folder . . . ";
   $cmd = "cp -pr ~$user/$db_name ";
   $cmd .= $contest_dir;
   system($cmd, $result);
echo"Finished.</p>";

echo "<p>Clearing folders . . . ";
   $cmd2 = "rm -rf ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/data/*";
   system($cmd2, $result);
   $cmd2 = "rm -rf ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/judged/*";
   system($cmd2, $result);
   $cmd2 = "rm -rf ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/queue/*";
   system($cmd2, $result);
   $cmd2 = "rm -rf ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/test_compile/*";
   system($cmd2, $result);
   $cmd2 = "rm -rf ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/c_jail/home/$user/*";
   system($cmd2, $result);
   $cmd2 = "rm -rf ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/cpp_jail/home/$user/*";
   system($cmd2, $result);
   $cmd2 = "rm -rf ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/java_jail/home/$user/*";
   system($cmd2, $result);
echo"Finished.</p>";

echo "<p>Making Directories . . . ";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/c_jail/home";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/c_jail/home/$user";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/c_jail/home/$user/";
   $cmd2 .= $user;
   $cmd2 .= "/judged";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/c_jail/home/$user/";
   $cmd2 .= $user;
   $cmd2 .= "/data";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/cpp_jail/home";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/cpp_jail/home/$user";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/cpp_jail/home/$user/";
   $cmd2 .= $user;
   $cmd2 .= "/judged";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/cpp_jail/home/$user/";
   $cmd2 .= $user;
   $cmd2 .= "/data";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/java_jail/home";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/java_jail/home/$user";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/java_jail/home/$user/";
   $cmd2 .= $user;
   $cmd2 .= "/judged";
   system($cmd2, $result);
   echo "Result: $result \n";
   $cmd2 = "mkdir -p ";
   $cmd2 .= $contest_dir;
   $cmd2 .= "/java_jail/home/$user/";
   $cmd2 .= $user;
   $cmd2 .= "/data";
   system($cmd2, $result);
   echo "Result: $result \n";
echo"Finished.</p>\n";

echo "<p> Adding in jail files. . . ";
//cpp_jail
	$user = str_replace("\n","",$user);
	$cmd = "cp -R /home/$user/jails/cpp_jail/lib64 /home/$user/$contest_clone_name/cpp_jail/lib64";
	system($cmd,$result);
	echo "Result: $result \n";
	$cmd = "cp -R /home/$user/jails/cpp_jail/usr /home/$user/$contest_clone_name/cpp_jail/usr";
	system($cmd,$result);
	echo "Result: $result \n";
	$cmd = "cp -R /home/$user/jails/cpp_jail/lib /home/$user/$contest_clone_name/cpp_jail/lib";
	system($cmd,$result);
	echo "Result: $result \n";
	$cmd = "cp -R /home/$user/jails/cpp_jail/bin /home/$user/$contest_clone_name/cpp_jail/bin";
	system($cmd,$result);
	echo "Result: $result \n";

//c_jail	
	$cmd = "cp -R /home/$user/jails/c_jail/lib64 /home/$user/$contest_clone_name/c_jail/lib64";
	system($cmd,$result);
	echo "Result: $result | dir = $contest_clone_name \n";
	$cmd = "cp -R /home/$user/jails/c_jail/usr /home/$user/$contest_clone_name/c_jail/usr";
	system($cmd,$result);
	echo "Result: $result \n";
	$cmd = "cp -R /home/$user/jails/c_jail/lib /home/$user/$contest_clone_name/c_jail/lib";
	system($cmd,$result);
	echo "Result: $result \n";
	$cmd = "cp -R /home/$user/jails/c_jail/bin /home/$user/$contest_clone_name/c_jail/bin";
	system($cmd,$result);
	echo "Result: $result \n";

//java_jail	
	$cmd = "cp -R /home/$user/jails/java_jail/lib64 /home/$user/$contest_clone_name/java_jail/lib64";
	system($cmd,$result);
	echo "Result: $result | dir = $contest_clone_name \n";
	$cmd = "cp -R /home/$user/jails/java_jail/usr /home/$user/$contest_clone_name/java_jail/usr";
	system($cmd,$result);
	$cmd = "sudo cp -R /home/$user/jails/java_jail/proc /home/$user/$contest_clone_name/java_jail/proc";
	system($cmd,$result);
	echo "Result: $result \n";
	$cmd = "cp -R /home/$user/jails/java_jail/lib /home/$user/$contest_clone_name/java_jail/lib";
	system($cmd,$result);
	echo "Result: $result \n";
	$cmd = "cp -R /home/$user/jails/java_jail/bin /home/$user/$contest_clone_name/java_jail/bin";
	system($cmd,$result);
	echo "Result: $result \n";
	
	$test = "chmod -R 0755 /home/$user/$contest_clone_name";
	system($test);
	echo "Finished</p>";
	
echo "<p>Creating Database . . . </div>";
   $cmd3 = "mysqldump --password=$db_pass -u root $db_name > $db_clone_name.sql";
   system($cmd3, $result);
   $cmd3 = "mysqladmin --password=$db_pass -u root create $db_clone_name";
   system($cmd3, $result);
   $cmd3 = "mysql --password=$db_pass -u root $db_clone_name < $db_clone_name.sql";
   system($cmd3, $result);
   
$link = mysql_connect($db_host, $db_user, $db_pass);
if (!$link) {
    print "Sorry.  Database connect failed.";
    exit;
}

$connect_good = mysql_select_db($contest_clone_name);
if (!$connect_good) {
    print "Sorry.  Database selection failed.";
    exit;
}

$base_dir = "/home/$user/$contest_clone_name";
$contest_info = mysql_query("UPDATE SITE SET CONTEST_NAME = \"$contest_clone_name\", CONTEST_DATE = '0000-00-00', START_TIME='00:00:00', FREEZE_DELAY='14400', CONTEST_END_DELAY='18000', BASE_DIRECTORY=\"$base_dir\", START_TS='0', HAS_STARTED='0'");
if (!$contest_info) {
    print "<div class='error'><br>Sorry.  Database request (UPDATE) failed 2.</div>";
    exit;
}
$contest_info = mysql_query("UPDATE SITE SET START_TIME='00:00:00', START_TS='0', HAS_STARTED='0'");
if (!$contest_info) {
    print "<div class='error'><br>Sorry.  Database request (UPDATE) failed 3.</div>";
    exit;
}
echo"Finished.</p>";

#need to clean out some information------------------------------------
   $cmd4 = "cp -r ~$user/public_html/$db_name ~$user/public_html/";
   $cmd4 .= $contest_clone_name;
   system($cmd4, $result);
echo"<div class='success'><br>Finished.</p>";

#-----------editing database.inc----------------------------------
echo "<p>Editing Settings . . .</div> ";
$fhdl = fopen("../../$contest_clone_name/lib/database.inc", "r") OR die("Error with opening file 1");
$file = fread($fhdl, filesize("../../$contest_clone_name/lib/database.inc"));
$file = preg_replace("/$db_name/", "$db_clone_name", $file);
fclose($fhdl);
$fhdl = fopen("../../$contest_clone_name/lib/database.inc", "w") OR die("Error with opening file 2");
$chk = fwrite($fhdl, $file);
fclose($fhdl);

#---------editing chroot_wrapper.c--------------------------------
$fhdl = fopen("../../../$contest_clone_name/chroot_wrapper.c", "r") OR die("Error with opening file 3");
$file = fread($fhdl, filesize("../../../$contest_clone_name/chroot_wrapper.c"));
$file = preg_replace("/$db_name/", "$contest_clone_name", $file);
fclose($fhdl);
$fhdl = fopen("../../../$contest_clone_name/chroot_wrapper.c", "w") OR die("Error with opening file 4");
$chk = fwrite($fhdl, $file);
fclose($fhdl);
$cmd5 = "gcc -o ../../../$contest_clone_name/chroot_wrapper.exe ../../../$contest_clone_name/chroot_wrapper.c";
system($cmd5, $result);
$cmd5 = "sudo chown root:root ../../../$contest_clone_name/chroot_wrapper.exe";
system($cmd5, $result);
$cmd5 = "sudo chmod +s ../../../$contest_clone_name/chroot_wrapper.exe";
system($cmd5, $result);
echo "<div class='success'><br>Finished.</div></p>";

echo "<a href=\"http://touche.cse.taylor.edu/~$user/$contest_clone_name/admin/index.php\">Click to go to setup for clone</a>";


}
elseif($_POST['B4']) {
	$sql = mysql_query("SELECT * FROM TEAMS ORDER BY TEAM_ID");
	if(!$sql) {
		print "<div class='error'><br>Error! could not find any team information</div>";
		exit;
	}
	else {
		
		$path = "../../../$db_name/judged/";
		$data_path = "../../../$db_name/data/";
		//export database
		$cmd = "mysqldump -u root -ppassword --databases $db_name > ../../../$db_name/data/data.sql";
		system($cmd, $result);
		if($result){
			echo "Error exporting the database";
		}
		else{
			//end export database
			if($_POST['admin_email']) {
				$cmd = "tar -cf - $data_path* $path* | gzip -c > $path";
				$cmd .= $command .= "$contest_name.tar.gz";
				system($cmd, $result);
				if(!$result) {
					$email = $_POST['admin_email'];
					$cmd = "echo | mutt -s \"Programming Contest Files\" ";
					$cmd .= "$email -a $path/$contest_name.tar.gz < email_body.txt";
					system($cmd, $result);
					if(!$result) {
							echo "<div class='success'><br>Files sent to Administrator<br></div>";
					}
					else {
							echo "<div class='error'><br>File could not be sent to Administrator!<br></div>";
					}
				}
				else {
						echo "<div class='error'><br>Could not gather contest files for administrator!<br></div>";
				}
			}
			#$path = "../../../develop/judged/";
			$num_teams = mysql_num_rows($sql);
			while($row = mysql_fetch_assoc($sql)) {
				$team_id = $row['TEAM_ID'];
				$command = "tar -cf - $path$team_id-*.cpp $path$team_id-*.c $path$team_id-*.java $data_path* | gzip -c > $path";
				$command .= "Team$team_id.tar.gz";
				#echo "$command<br>";
				system($command, $result);
				if(!$result) {
					#print "Files Zipped!";
					#email to teams
					if($row['EMAIL']) {
						$email = $row['EMAIL'];
						$cmd = "echo | mutt -s \"Programming Contest Files\" ";
						$cmd .= "$email -a $path/Team$team_id.tar.gz < email_body.txt";
						system($cmd, $result);
						if(!$result) {
							$team_name_send = $row['TEAM_NAME'];
							echo "<div class='success'><br>Files sent to Team $team_name_send<br></div>";
						}
						else {
							$team_name_send = $row['TEAM_NAME'];
							echo "<div class='error'>File could not be sent to Team $team_name_send!<br></div>";
						}
					}
				}
				else {
					$team_name_send = $row['TEAM_NAME'];
					echo "<div class='error'><br>Could not gather team files for Team $team_name_send !<br></div>";
				}
			}
		}
	}
}

}
/*******************************************************
End of POST section
*******************************************************/
	include("lib/header.inc");
	        $link = mysql_connect($db_host, $db_user, $db_pass);
        if(!$link){
                print "<div class='error'><br>Sorry.  Database connect failed.  Check your internet connection.</div>";
                exit;
        }
        $connect_good = mysql_select_db($db_name);
        if (!$connect_good) {
                print "<div class='error'><br>Sorry.  Couldn't select the database name $db_name. Exiting...</div>";
                exit;
        }

        $sql = mysql_query("SELECT * FROM SITE WHERE SITE_ID = 1");
        if (!$sql) {
                print "<div class='error'><br>Could not tell if a contest has been created.  bailing out.</div>";
                exit;
                #die or break
        }
        if (mysql_num_rows($sql) > 0) {
        //a contest is already set up!
                $contest=true;
                $row = mysql_fetch_assoc($sql);
                echo "<center>\n";

                # Print out any errors
                if(isset($error)) {
                    echo "<br>";
                    foreach($error as $er) {
                        echo "<b>$er</b>";
                    }
                }

                echo "</center>";
                echo "<p>";
                echo " <div class=\"table-responsive\">";
                echo " <table class=\"table\" align=\"center\" width=100%>";
                echo "  <tr>\n";
                echo "<form method=POST action=misc.php>\n";
                echo "          <td colspan=\"2\" align='center'><h3>Misc Contest Actions</h3></td>\n";
                echo "  </tr>";
                $host = $row['CONTEST_HOST'];
                $contest_name = $row['CONTEST_NAME'];
                //calculating the number of seconds since January 1 1970 at midnight
                //for our particular freeze/contest end values in seconds
                $freeze_hour = gmdate('H', $contest_freeze_time);
                $freeze_minute = gmdate('i', $contest_freeze_time);
                $freeze_second = gmdate('s', $contest_freeze_time);
                $end_hour = gmdate('H', $contest_end_time);
                $end_minute = gmdate('i', $contest_end_time);
                $end_second = gmdate('s', $contest_end_time);
                $ext_hour = gmdate('H', $contest_ext_time);
                $ext_minute = gmdate('i', $contest_ext_time);
                $ext_second = gmdate('s', $contest_ext_time);
        }
	else {
		$ext_hour = "00";
		$ext_minute = "00";
		$ext_second = "00";
	}
        echo "          <tr>";
        echo "                  <td align='right'>Extend Contest By (HH:mm:ss)</td> ";
        echo "                  <td><input type=\"text\" name=\"ext_hour\" size=\"2\"";
        echo "                          maxlength=2 value=\"$ext_hour\"></input>:";
        echo "                  <input type=\"text\" name=\"ext_minute\" size=\"2\"";
        echo "                          maxlength=2 value=\"$ext_minute\"></input>:";
        echo "                  <input type=\"text\" name=\"ext_second\" size=\"2\"";
        echo "                          maxlength=2 value=\"$ext_second\"></input>";

        echo "                  <br><br><button class=\"btn btn-default\" type=\"submit\" value=\"Extend Contest\" name=\"B1\">Extend Contest</button></td> ";
        echo "          </tr>";





        echo "          <tr>";
        echo "                  <td align='right'>Problems, Teams, Categories, etc. will be kept.</td>";
        echo "                  <td><button class=\"btn btn-default\" type=\"submit\" value=\"Clear Contest\" name=\"B2\">Clear Contest</button></td>";
	      echo "		      </tr>";

        echo "          <tr>";
        echo "                  <td align='right'>Clone Contest:</td>";
        echo "                  <td><input class='form-control' type=\"text\" name=\"clone_name\" placeholder=\"Clone Name\" size=\"17\"></input>";
        echo "                  <br><button class=\"btn btn-default\" type=\"submit\" value=\"Clone Contest\" name=\"B3\">Clone Contest</button></td> ";
        echo "          </tr>";


        echo "          <tr>";
        echo "                  <td align='right'>Admin Email (Send all contest files to):</td>";
        echo "                  <td><input class='form-control' type=\"text\" name=\"admin_email\" size=\"17\"></input>";

        echo "                  <br>";
        echo "                  <button class=\"btn btn-default\" type=\"submit\" value=\"Send Zip Files\" name=\"B4\">Send Zip Files</button></td> ";
        echo "          </tr>";


        echo "  </form>";

        if(!mysql_num_rows( mysql_query("SHOW TABLES LIKE 'JUDGED_SUBMISSIONS_COPY'"))){
                echo "  <form action='rejudge.php' method='POST'>\n";
                echo "          <tr>";
                echo "                  <td align='right'>calculate new auto responses for each submission</td> ";
                echo "                  <td><button class=\"btn btn-default\" type=\"submit\" value='recalculate responses' onClick='return confirmSubmit()'>Recalculate responses</button></td> ";
                echo "          </tr>";

                echo "  </form></table>";
        }
        else{


                echo "          <tr>";
                echo "                  <td align='right'>It has Already been recalculated</td> ";
                echo "                  <td><a href='review.php'>review new judgements</a></td> ";
                echo "          </tr>";


                echo " </table>";

        }

echo " <script LANGUAGE='JavaScript'>
                        <!--
                        // Nannette Thacker http://www.shiningstar.net
                        function confirmSubmit()
                        {
                                var agree=confirm('Warning!  This process takes a considerable amount, and change the database and file system so that current standings will be lost!!');
                                if (agree)
                                        return true ;
                                else
                                        return false ;
                                }
                        // -->
                </script> ";

    include("lib/footer.inc");
?>	
