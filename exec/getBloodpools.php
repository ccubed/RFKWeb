<?php

$ini = parse_ini_file("rfk.ini");

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;

if ($dbcon->connect_errno){

	$errors = 1;
	echo json_encode(array("LocName" => "Error","Tools" => "Connecting to Database"));
}

if( !$errors ){

	if( !$stmt=$dbcon->prepare("SELECT dbref,loc_name,fmod_casanova,fmod_mugger,fmod_sandman From bloodpool") ){

		$errors = 1;
		echo json_encode(array("LocName" => "Error","Tools" => "Getting Information from Database"));
	}

	if ( !$errors ){

		$stmt->execute();
		$stmt->store_result();

		if ( $stmt->num_rows == 0 ){

			$errors = 1;
			echo json_encode(array("LocName" => "Error","Tools" => "Getting Information from Database"));

		}

		if ( !$errors ){
			
			$results = array();
			$stmt->bind_result($dbref,$locname,$casmod,$mugmod,$sandmod);
			while ($stmt->fetch()){
				
				$tools = '<span data-placement="bottom" data-toggle="tooltip" title="View Edit Modifiers"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $dbref . 'mods"><span class="glyphicon glyphicon-pencil"></span></button></span></td>';
				$modal = '<div class="modal fade" id="' . $dbref . 'mods" tabindex="-1" role="dialog" aria-labelledby="Survey Details"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Survey Details</h4></div><div class="modal-body"><div class="form-group"><label for="fmodcas">Casanova Modifier</label><input type="text" class="form-control" id="fmodcas' . $dbref . '" value="' . $casmod . '" /></div><div class="form-group"><label for="fmodmug">Mugger Modifier</label><input type="text" class="form-control" id="fmodmug' . $dbref . '" value="' . $mugmod . '" /></div><div class="form-group"><label for="fmodsand">Sandman Modifier</label><input type="text" class="form-control" id="fmodsand' . $dbref . '" value="' . $sandmod . '" /></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" class="btn btn-primary" name="modsaves" id="' . $dbref . '">Save Changes</button></div></div></div></div>';
				$temp = array("LocName" => $locname, "Tools" => $tools, "modal" => $modal);
				array_push($results,$temp);

			}

			$stmt->close();

		}

	}

$dbcon->close();

}

echo json_encode($results);

?>