<?php

$ini = parse_ini_file("rfk.ini");

$msg = $_POST['fbtext'];
$cname = $_POST['cname'];
$subject = NULL;
$type = $_POST['type'];

if ( ISSET($_POST['title']) ){

	$subject = $_POST['title'];
	
}

//prepare mysql
$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);

if ($dbcon->connect_errno){

	//redirect to error page, show sql connectivity error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_conn');
	die();
	
}

if (!($stmt = $dbcon->prepare("INSERT INTO Feedback(Name,FBText,Title,Type) VALUES (?,?,?,?)"))){

	//redirects to error page, show general sql error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
	die();

}

if (!$stmt->bind_param("ssss",$cname,$msg,$subject,$type)){
	
	//redirect to error page, show general sql error
	header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
	die();
		
}

//we're good at this point, clean up and send to fbthanks.html
$stmt->execute();
$stmt->close();
$dbcon->close();
header('Location: http://kingsmouth.vertinext.com/results.html?which=feedback');
die();
	
?>