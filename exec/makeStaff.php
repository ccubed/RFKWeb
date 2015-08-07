<?php

$ini = parse_ini_file("rfk.ini");

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;

if ($dbcon->connect_errno){

	$errors = 1;
	
}

if ( !$errors ){
	
	if ( !$stmt=$dbcon->prepare("INSERT INTO app_auth(Name,Authgrp) VALUES (?,100)")){
	
		$errors = 1;
	
	}
	
	if ( !$errors ){
		
		$stmt->bind_param("s",$_POST['id']);
		$stmt->execute();
		$stmt->close();
		
	}
	
	$dbcon->close();
	
}

?>