<?php

	if(isset($_COOKIE['RFKJournal'])){
	
		unset($_COOKIE['RFKJournal']);
		setcookie('RFKJournal','',time()-3600,"/","kingsmouth.vertinext.com");	
		
	}
	
	header("Location: http://kingsmouth.vertinext.com/results.html?which=logout");
	die();

?>