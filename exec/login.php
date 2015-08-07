<?php
ini_set("display_errors",1);
error_reporting(E_ALL);


use \Defuse\Crypto\Crypto;
use \Defuse\Crypto\Exception as Ex;
require_once 'autoload.php';

$ini = parse_ini_file("rfk.ini");

$user = $_POST['uname'];
$pw = $_POST['pw'];

$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);

if ($dbcon->connect_errno){

	//redirect to error page, show sql connectivity error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_conn');
	die();
	
}

if (!$stmt=$dbcon->prepare("SELECT WebPass,Name FROM Players WHERE WebUser=?")){
	
	//redirect to error page, show general sql error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
	die();
	
}

$stmt->bind_param("s",$user);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0){
	
	//redirect to error page, show username not registered error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_notreg');
	die();
	
} else if ($stmt->num_rows > 1) {
	
	//redirect to error page, show username registered to multiple players error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_multiple');
	die();
	
}

$stmt->bind_result($encpw,$pcname);
$stmt->fetch();

if ($pw != Crypto::decrypt($encpw,hex2bin($ini['key']))){

	//redirect to error page, show wrong password error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_wrongpw');
	die();	
	
}

$stmt->close();
$dbcon->close();
$ckdata = json_encode(array("Username" => $user, "Player_Name" => $pcname));
$life = empty($_POST['rme']) ? strtotime('+7 days') : strtotime('+100 years');
setcookie("RFKJournal",$ckdata,$life,"/","kingsmouth.vertinext.com");
	
//redirect to results page, show successful login
header('Location: http://kingsmouth.vertinext.com/results.html?which=login');
die();	

?>