<?php

$ini = parse_ini_file("rfk.ini");

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;

if ($dbcon->connect_errno){

	$errors = 1;
	echo json_encode(array('LocName' => "Error", 'Kingpin' => "Connecting", 'Attitude' => "To", 'Tools' => "Database"));
}

if( !$errors ){

	if( !$stmt=$dbcon->prepare("select dbref, name, (SELECT Name from Players WHERE dbref=kingpin_dbref) as kingpin, attitude, access, safety, information, awareness, prestige, stability, feeding_pool FROM territory ORDER BY name") ){

		$errors = 1;
		echo json_encode(array('LocName' => "Error", 'Kingpin' => "Getting", 'Attitude' => "Information", 'Tools' => "From Database"));
	}

	if ( !$errors ){

		$stmt->execute();
		$stmt->store_result();

		if ( $stmt->num_rows == 0 ){

			$errors = 1;
			echo json_encode(array('LocName' => "Error", 'Kingpin' => "Getting", 'Attitude' => "Information", 'Tools' => "From Database"));
		}

		if ( !$errors ){
			
			$results = array();
			$stmt->bind_result($dbref,$name,$kingpin,$attitude,$access,$safety,$information,$awareness,$prestige,$stability,$fpool);
			while ($stmt->fetch()){
				
				$tools = '<span data-placement="bottom" data-toggle="tooltip" title="Commit Changes"><button type="button" class="btn btn-default" name="saveterrow" id="' . $dbref . '-terrow"><span class="glyphicon glyphicon-check"></span></button></span><span data-placement="bottom" data-toggle="tooltip" title="View Edit Traits"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#' . $dbref . 'traits"><span class="glyphicon glyphicon-book"></span></button></span></td>';
				$modal = '<div class="modal fade" id="' . $dbref . 'traits" tabindex="-1" role="dialog" aria-labelledby="Territory Traits"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="myModalLabel">Territory Traits</h4></div><div class="modal-body"><div class="form-group"><label for="atrait">Access</label><input type="text" class="form-control" id="atrait' . $dbref . '" value="' . $access . '" /></div><div class="form-group"><label for="strait">Safety</label><input type="text" class="form-control" id="strait' . $dbref . '" value="' . $safety . '" /></div><div class="form-group"><label for="itrait">Information</label><input type="text" class="form-control" id="itrait' . $dbref . '" value="' . $information . '" /></div><div class="form-group"><label for="awtrait">Awareness</label><input type="text" class="form-control" id="awtrait' . $dbref . '" value="' . $awareness . '" /></div><div class="form-group"><label for="ptrait">Prestige</label><input type="text" class="form-control" id="ptrait' . $dbref . '" value="' . $prestige . '" /></div><div class="form-group"><label for="sttrait">Stability</label><input type="text" class="form-control" id="sttrait' . $dbref . '" value="' . $stability . '" /></div><div class="form-group"><label for="fptrait">Feeding Pool</label><input type="text" class="form-control" id="fptrait' . $dbref . '" value="' . $fpool . '" /></div></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" class="btn btn-primary" name="tertraitssave" id="' . $dbref . '">Save Changes</button></div></div></div></div>';
				
				$prsattitude = '<select class="form-control">';
				for( $i=0;$i < 11; $i++){
					
					
					switch ($i){
						
					case 0:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Isolated</option>' : '<option>Isolated</option>');
						break;
					case 1:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Dead</option>' : '<option>Dead</option>');
						break;
					case 2:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Antagonistic</option>' : '<option>Antagonistic</option>');
						break;
					case 3:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Riotous</option>' : '<option>Riotous</option>');
						break;
					case 4:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Unruly</option>' : '<option>Unruly</option>');
						break;
					case 5:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Simmering</option>' : '<option>Simmering</option>');
						break;
					case 6:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Wary</option>' : '<option>Wary</option>');
						break;
					case 7:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Calm</option>' : '<option>Calm</option>');
						break;
					case 8:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Drowsy</option>' : '<option>Drowsy</option>');
						break;
					case 9:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Accepting</option>' : '<option>Accepting</option>');
						break;
					case 10:
						$prsattitude = $prsattitude . ($attitude == $i ? '<option selected>Welcoming</option>' : '<option>Welcoming</option>');
						break;
						
					}
										
				}
				
				$prsattitude = $prsattitude . '</select>';
				
				
				
				//At this point, LocName, Tools and Modal are finished.
				if ( !$errors ){
					
					if( !$stmtt = $dbcon->prepare("SELECT Name from Players WHERE Template != 'staff' ORDER BY Name")){
						
						$errors = 1;
						
					}
					
					$stmtt->execute();
					$stmtt->bind_result($pname);
					$Kingpinsel = '<select class="form-control"><option>None</option>';
					
					while ($stmtt->fetch()){
						
						if ( $pname == $kingpin ){
							
							$Kingpinsel = $Kingpinsel . '<option selected>' . $pname . '</option>';
							
						} else {
							
							$Kingpinsel = $Kingpinsel . '<option>' . $pname . ' </option>';							
							
						}
						
					}
					
					$Kingpinsel = $Kingpinsel . '</select>';
					
				}
				
				array_push($results,array('name' => '<input type="text" class="form-control" value="' . $name . '" />', 'Kingpin' => $Kingpinsel, 'Attitude' => $prsattitude, 'Tools' => $tools, 'modal'=> $modal));

			}

		}

	}

$dbcon->close();

}

echo json_encode($results);

?>