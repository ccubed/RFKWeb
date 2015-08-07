<?php

use \Defuse\Crypto\Crypto;
use \Defuse\Crypto\Exception as Ex;
require_once 'autoload.php';

$ini = parse_ini_file("rfk.ini");

$pcname = $_POST['cname'];
$user = $_POST['uname'];
$pw = $_POST['pw'];
$otpw = $_POST['otpw'];
$email = NULL;

if(!empty($_POST['email'])){
	
	$email = $_POST['email'];
	
}

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);

if ($dbcon->connect_errno){

	//redirect to error page, show sql connectivity error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_conn');
	die();
	
}

if (!($stmt = $dbcon->prepare("SELECT Name FROM Players WHERE WebUser=?"))){

	//redirects to error page, show general sql error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
	die();
	
}

$stmt->bind_param("s",$user);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows != 0){
	
	//redirects to error page, show username taken error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_untaken');
	die();
	
}

$stmt->close();

if (!($stmt = $dbcon->prepare("SELECT OTPW,Template FROM Players WHERE Name=?"))){

	//redirects to error page, show general sql error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
	die();

}

$stmt->bind_param('s',$pcname);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0){

	//redirects to error page, show not set up for web tools error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_nomatch');
	die();
	
}

if ($stmt->num_rows > 1){

	//redirects to error page, show multiple matches error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_mulmatch');
	die();
	
}

$stmt->bind_result($db_otpw,$db_template);
$stmt->fetch();

//make sure they used the right one time password
if ($db_otpw == "used"){

	//redirect to error page, show that this pc has already been claimed (Ie: one time password used already)
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_otpwu');
	die();
	
} else if ($otpw != $db_otpw){
	
	//redirect to error page, show wrong one time password error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_otpww');
	die();
	
}

$stmt->close();

if (!($stmt = $dbcon->prepare("UPDATE Players SET OTPW=?,WebUser=?,WebPass=?,Email=? WHERE Name=?"))){

	//redirects to error page, show general sql error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
	die();

}

$otpw = "used";
$key=hex2bin($ini['key']);
$encpw = Crypto::encrypt($pw,$key);
$stmt->bind_param('sssss',$otpw,$user,$encpw,$email,$pcname);
$stmt->execute();

if ($stmt->affected_rows != 1){

	//redirects to error page, show general sql error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
	die();
	
}

$stmt->close();

if ( $db_template == "STAFF" ){

	if ( !$stmt=$dbcon->prepare("INSERT INTO app_auth(Name,Authgrp) VALUES (?,?)") ){
		
		$stmt->close(); //No big deal, i can fix it later
	
	} else {
	
		$agrp = 100;
		$stmt->bind_param("si",$user,$agrp);
		$stmt->execute();
		$stmt->close();
		
	}
	
}

$dbcon->close();

//Successful
header('Location: http://kingsmouth.vertinext.com/results.html?which=registered');
die();

?>