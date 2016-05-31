<?php
/*
* Author : Ajay Halthor
* Title  : Backend of adding/deleting open hours on creating a business.

* Description : Takes the Time, Converts it into a 24 hour format and :
	ADDITION:
		1 .Checks if begin shift time is less than end shift time
		2. If there are overlapping enteries by the user
		3. If no errors, inserts it into the database.
	DELETION:
		1. Deletes the corrusponding entry
		2. selects the remaining hours in the database and spits it back on the screen

*/
include_once('php_includes/db_conx.php');
/*
PERSONAL NOTES : 
1. Add a table to the database called 'business_timing'

2. Query for making all the fields unique(Primary) - 
	ALTER TABLE business_timing
	ADD PRIMARY KEY (bizName,day,start_hour,end_hour).

*/

$bizName = $_POST['bizName'];
$operation = $_POST['operation'];


if(strcasecmp($operation, 'display') != 0){

	$day = $_POST['day'];
	$start_shift = $_POST['start_shift'];
	$end_shift = $_POST['end_shift'];

	$start_shift =  date("G:i", strtotime($start_shift));
	$end_shift =  date("G:i", strtotime($end_shift));


	if(strcasecmp($operation, 'addition') == 0){

			if(strtotime($start_shift)  >= strtotime($end_shift)){
				$error_message = array('success' => 'false', 'message' => 'Invalid Shift Entry');
				echo json_encode($error_message);
				return;
			}

			$sql = "SELECT * 
					FROM business_timing
					WHERE bizName = '$bizName'
					AND day = '$day'";

			$query = mysqli_query($db_conx,$sql);

			while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
				if((strtotime($row['end_hour']) > strtotime($start_shift)) && (strtotime($row['start_hour']) < strtotime($end_shift))){
					$error_message = array('success' => 'false', 'message' => 'Overlapping Times Given');
					echo json_encode($error_message);
					return;
				}
			}

			$sql = "INSERT INTO business_timing (bizName,day,start_hour,end_hour)
				VALUES ('$bizName','$day','$start_shift','$end_shift')";

			mysqli_query($db_conx,$sql);

			$sql = "SELECT * 
					FROM business_timing
					WHERE bizName = '$bizName'";

			$query = mysqli_query($db_conx,$sql);

			$result = array();
			while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
				array_push($result, $row);
			}

			$success_message = array('success' => 'true', 'message' => 'The shift was added', 'result' => $result);
			echo json_encode($success_message);

	//mysqli_close($db_conx);
	}else{


			$sql = "DELETE FROM business_timing
				WHERE bizName = '$bizName'
				AND day = '$day'
				AND start_hour = '$start_shift'
				AND end_hour = '$end_shift'"; 

			mysqli_query($db_conx,$sql);



			$sql = "SELECT * 
					FROM business_timing
					WHERE bizName = '$bizName'";

			$query = mysqli_query($db_conx,$sql);

			$result = array();
			while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
				array_push($result, $row);
			}

			$success_message = array('success' => 'true', 'message' => 'The shift was deleted', 'result' => $result);
			echo json_encode($success_message);

	}
}else{

		$sql = "SELECT * 
			FROM business_timing
			WHERE bizName = '$bizName'";

		$query = mysqli_query($db_conx,$sql);

		$result = array();
		while($row = mysqli_fetch_array($query,MYSQLI_ASSOC)){
			array_push($result, $row);
		}

		$success_message = array('success' => 'true', 'message' => 'The shifts were displayed', 'result' => $result);
		echo json_encode($success_message);
	
}
?>