<?php

$ini = parse_ini_file("rfk.ini");

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;

if ($dbcon->connect_errno){

	$errors = 1;
	echo json_encode(array("UName" => "Error","PCName" => "Connecting","Role" => "To","Tools" => 'Database'));
}

if( !$errors ){

	if( !$stmt=$dbcon->prepare("SELECT a.WebUser,a.Name,b.Authgrp FROM Players a LEFT JOIN app_auth b ON a.WebUser = b.Name WHERE a.WebUser IS NOT NULL") ){

		$errors = 1;
		echo json_encode(array("UName" => "Error","PCName" => "Communicating","Role" => "With","Tools" => 'Database'));
	}

	if ( !$errors ){

		$stmt->execute();
		$stmt->store_result();

		if ( $stmt->num_rows == 0 ){

			$errors = 1;
			echo json_encode(array("UName" => "Error","PCName" => "No","Role" => "Users","Tools" => 'Found'));

		}

		if ( !$errors ){
			
			$results = array();
			$stmt->bind_result($uname,$pcname,$role);
			while ($stmt->fetch()){
				
				$tools = '<span data-placement="bottom" data-toggle="tooltip" title="Make Staff"><button type="button" class="btn btn-default" id="' . $uname . '" name="makeStaff"><span class="glyphicon glyphicon-sunglasses"></span></button></span><span data-placement="bottom" data-toggle="tooltip" title="Send Temporary Password"><button type="button" class="btn btn-default" id="' . $uname . '" name="tempPass"><span class="glyphicon glyphicon-send"></span></button></span>';
				$temp = array("UName" => $uname, "PCName" => $pcname, "Role" => ($role == 100? 'Staff' : 'Player'), "Tools" => $tools);
				array_push($results,$temp);

			}

			$stmt->close();

		}

	}

$dbcon->close();

}

echo json_encode($results);

?>