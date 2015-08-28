<?php

$ini = parse_ini_file("rfk.ini");

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;

if ($dbcon->connect_errno){

	$errors = 1;
    $results=array('status'=>'error','details'=>'Database Connection Failed');
    
}

if( !$errors ){

	if( !$stmt=$dbcon->prepare("SELECT SUM(case when archived=0 then 1 else 0 end) as Open, SUM(case when archived=1 then 1 else 0 end) as Closed FROM Feedback") ){

		$errors = 1;
        $results=array('status'=>'error','details'=>'Database Response Failed');

	}

	if ( !$errors ){

		$stmt->execute();
		$stmt->store_result();

		if ( $stmt->num_rows == 0 ){

			$errors = 1;
            $results=array('status'=>'error','details'=>'No Surveys Found');

		}

		if ( !$errors ){
			
			$results = array();
			$stmt->bind_result($open,$close);
			$stmt->fetch();
            $results = array('open' => $open,'archived' => $close,'status' => 'success');
			$stmt->close();

		}

	}

$dbcon->close();

}

echo json_encode($results);

?>