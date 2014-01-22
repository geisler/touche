<?
	ob_start();
	include_once("lib/config.inc");
    include_once("lib/data.inc");
	include_once("lib/header2.inc");
	if($_SERVER['REQUEST_METHOD'] == 'GET'){
	?>

		<!DOCTYPE HTML PUBLIC "-//W3C/DTD HTML 4.0 Transitional//EN">	
		<html>
		<body>
		<?php
			$team_id = $_SESSION['team_id'];
			$sql = "SELECT * from TEAMS WHERE TEAM_ID = $team_id";
			$result = mysql_query($sql);
			if (!$result) {
				echo '<div class = "error"><br>Could not run query: ' . mysql_error().'</div>';
				exit;
			}
			$row = mysql_fetch_assoc($result);
			$p1 = $row['CONTESTANT_1_NAME'];
			$p2 = $row['CONTESTANT_2_NAME'];
			$p3 = $row['CONTESTANT_3_NAME'];
			$alt = $row['ALTERNATE_NAME'];
			
			echo "<h3>Please indicate which team member will NOT be participating:</h3><br>";
			echo "<table class='table' align='center' width=100%><tr><td>";
			echo '<form name="f" action=non_participant.php method=post>';
			echo '	<input type="radio" name="radio" value="CONTESTANT_1_NAME">'.$p1.'</br>';
			echo '	<input type="radio" name="radio" value="CONTESTANT_2_NAME">'.$p2.'</br>';
			echo '	<input type="radio" name="radio" value="CONTESTANT_3_NAME">'.$p3.'</br>';
			echo '	<input type="radio" name="radio" value="ALTERNATE_NAME" checked>'.$alt.'</br>';
			echo '	<input name=submit type=submit value="Submit">';
			echo'</form>';
			echo "</td></tr></table>";
		?>
		</body>
		</html>
		<?php
	}
	else if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$db_col = $_POST['radio'];
		$team_id = $_SESSION['team_id'];
		$sql = "SELECT ".$db_col." FROM TEAMS WHERE TEAM_ID = ".$team_id;
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$value = $row[$db_col];
		$sql = "UPDATE TEAMS SET NON_PARTICIPANT = '".$value."' WHERE TEAM_ID = ".$team_id;
		mysql_query($sql);
		header ("Location: problems.php");
	}
?>

