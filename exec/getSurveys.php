<?php

$ini = parse_ini_file("rfk.ini");

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;

if ($dbcon->connect_errno){

	$errors = 1;
	echo json_encode(array("Name" => "Error","Title" => "Some Error Occurred","Type" => "Bug","Tools" => '<span data-placement="bottom" data-toggle="tooltip" title="Read Survey"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#1survey"><span class="glyphicon glyphicon-book"></span></button></span>',"modal" => '<div class="modal fade" id="1survey" tabindex="-1" role="dialog" aria-labelledby="Survey Details"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Survey Details</h4></div><div class="modal-body">Error connecting to Database.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>'));
}

if( !$errors ){

	if( !$stmt=$dbcon->prepare("SELECT ID,Name,FBText,IF(Title>'',Title,'Untitled'),Type From Feedback WHERE Archived=0") ){

		$errors = 1;
		echo json_encode(array("Name" => "Error","Title" => "Some Error Occurred","Type" => "Bug","Tools" => '<span data-placement="bottom" data-toggle="tooltip" title="Read Survey"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#1survey"><span class="glyphicon glyphicon-book"></span></button></span>',"modal" => '<div class="modal fade" id="1survey" tabindex="-1" role="dialog" aria-labelledby="Survey Details"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Survey Details</h4></div><div class="modal-body">General SQL Error.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>'));
	}

	if ( !$errors ){

		$stmt->execute();
		$stmt->store_result();

		if ( $stmt->num_rows == 0 ){

			$errors = 1;
			echo json_encode(array("Name" => "Error","Title" => "Some Error Occurred","Type" => "Bug","Tools" => '<span data-placement="bottom" data-toggle="tooltip" title="Read Survey"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#1survey"><span class="glyphicon glyphicon-book"></span></button></span>',"modal" => '<div class="modal fade" id="1survey" tabindex="-1" role="dialog" aria-labelledby="Survey Details"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Survey Details</h4></div><div class="modal-body">General SQL Error.</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>'));

		}

		if ( !$errors ){
			
			$results = array();
			$stmt->bind_result($id,$name,$fbtext,$subject,$fbtype);
			while ($stmt->fetch()){
				
				$tools = '<span data-placement="bottom" data-toggle="tooltip" title="Read Survey"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $id . 'survey"><span class="glyphicon glyphicon-book"></span></button></span><span data-placement="bottom" data-toggle="tooltip" title="Archive"><button type="button" class="btn btn-default" name="archive" id="' . $id . '"><span class="glyphicon glyphicon-ok-circle"></span></button></span></td>';
				$modal = '<div class="modal fade" id="' . $id . 'survey" tabindex="-1" role="dialog" aria-labelledby="Survey Details"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Survey Details</h4></div><div class="modal-body">' . nl2br($fbtext) . '</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>';
				$temp = array("Name" => $name, "Title" => $subject, "Type" => $fbtype, "Tools" => $tools, "modal" => $modal);
				array_push($results,$temp);

			}

			$stmt->close();

		}

	}

$dbcon->close();

}

echo json_encode($results);

?>