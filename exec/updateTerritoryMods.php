<?php

$ini = parse_ini_file("rfk.ini");

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;

if ($dbcon->connect_errno){

	$errors = 1;
	
}

if ( !$errors ){
	
	if ( !$stmt=$dbcon->prepare("UPDATE bloodpool SET fmod_casanova=?,fmod_mugger=?,fmod_sandman=? WHERE dbref=?")){
	
		$errors = 1;
	
	}
	
	if ( !$errors ){
		
		$stmt->bind_param("iiii",$_POST['fmodcas'],$_POST['fmodmug'],$_POST['fmodsand'],$_POST['id']);
		$stmt->execute();
		$stmt->close();
		
	}
	
	$dbcon->close();
	
}

?>