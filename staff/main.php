<?php

$ini = parse_ini_file("../exec/rfk.ini");

//what are we grabbing
//Feedback Surveys
$surveys = array("Open" => 0,"Archived" => 0);
//census data
$census = array("Mortal" => 0,"Vampire" => 0,"Ghoul" => 0);
//registered users
$users = array("Registered" => 0, "Unregistered" => 0);
			
//Connect to Database
$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
$errors = 0;
			
if ($dbcon->connect_errno){
					
	$errors = 1;
}

if (!$errors){
	
	if (!$stmt=$dbcon->prepare("SELECT SUM(case when Archived=0 then 1 else 0 end) as numo,SUM(case when Archived=1 then 1 else 0 end) as numa FROM Feedback")){
		
		$errors = 1;
	
	}
	
	if( !$errors ){
	
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($surveys['Open'],$surveys['Archived']);
		$stmt->fetch();
		$stmt->close();
		
		if (!$stmt=$dbcon->prepare("SELECT Template_Vampire,Template_Ghoul,Template_Mortal FROM census ORDER BY dt_time DESC LIMIT 1")){
		
			$errors = 1;
			
		}
		
		if( !$errors ){
			
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($census['Vampire'],$census['Ghoul'],$census['Mortal']);
			$stmt->fetch();
			$stmt->close();
			
			if (!$stmt=$dbcon->prepare("SELECT SUM(case when WebUser IS NULL then 1 else 0 end) as numr,SUM(case when WebUser IS NOT NULL then 1 else 0 end) as numu FROM Players")){
		
				$errors = 1;
			
			}
			
			if ( !$errors ){
			
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($users['Unregistered'],$users['Registered']);
				$stmt->fetch();
				$stmt->close();
				
			}
		
		}
		
	}
	
	$dbcon->close();
	
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="RFK Staff Area">
    <meta name="author" content="Charles Click;Oukranos;Alzie">
    <link rel="icon" href="../../favicon.ico">

    <title>RFK Staff Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/dashboard.css" rel="stylesheet">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="../js/Chart.min.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  
	<?php
	
		if (!ISSET($_COOKIE['RFKJournal'])){
		
			//redirects to error page, show not logged in
			header('Location: http://kingsmouth.vertinext.com/errors.html?which=gen_nologin');
			die();
			
		}
		
		$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
		$errors = 0;
			
		if ($dbcon->connect_errno){
					
			//redirects to error page, show not logged in
			header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_conn');
			die();
			
		}
		
		if (!$stmt=$dbcon->prepare("SELECT Authgrp FROM app_auth WHERE Name=?")){
			
			//redirects to error page, show not logged in
			header('Location: http://kingsmouth.vertinext.com/errors.html?which=sql_generr');
			die();
			
		}
		
		$ckdata = json_decode($_COOKIE['RFKJournal'],true);
		$stmt->bind_param("s",$pcname);
		$pcname = $ckdata['Username'];
		$stmt->execute();
		$stmt->store_result();
		
		if ($stmt->num_rows == 0){
			
			//redirects to error page, show not logged in
			header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_notauth');
			die();
			
		}
		
		$stmt->bind_result($authlvl);
		$stmt->fetch();
		
		if ( $authlvl != 100 ){
			
			//redirects to error page, show not logged in
			header('Location: http://kingsmouth.vertinext.com/errors.html?which=err_notauth');
			die();
			
		}
		
		$stmt->close();
		$dbcon->close();
	
	?>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="../index.html">Kingsmouth Staff</a>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
		  <ul class="nav nav-sidebar">
			<li><a href="../index.html">Home</a></li>
			<li><a href="#overview">Top</a></li>
		  </ul>
          <ul class="nav nav-sidebar">
            <li><button class="btn btn-link" id="SurveysNB">Surveys</button></li>
			<li><button class="btn btn-link" id="SurveysANB">Archived Surveys</button></li>
            <li><button class="btn btn-link" id="UsersNB">Registered Users</button></li>
		  </ul>
		  <ul class="nav nav-sidebar">
			<li><button class="btn btn-link" id="BoonsNB">Boons</button></li>
			<li><button class="btn btn-link" id="RumorsNB">Search Rumors</button></li>
		  </ul>
		  <ul class="nav nav-sidebar">
			<li><button class="btn btn-link" id="TerritoriesNB">List Territories</button></li>
			<li><button class="btn btn-link" id="UCrisesNB">List Unwritten Crises</button></li>
			<li><button class="btn btn-link" id="BloodpoolsNB">List Bloodpools</button></li>
			<li><button class="btn btn-link" id="PatrolsNB">List Patrols</button></li>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header" id="overview">Overview</h1>

          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
				<canvas id="surveychart" width="200" height="200"></canvas>
				<h4>Feedback Surveys</h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
				<canvas id="censuschart" width="200" height="200"></canvas>
				<h4>Census Data</h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
				<canvas id="userchart" width="200" height="200"></canvas>
				<h4>Registered Users</h4>
            </div>
          </div>

          <h2 class="sub-header" id="dataHeader">No Data Loaded</h2>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr id="tbHead">
					<th>Select a Category on the Left</th>
                </tr>
              </thead>
              <tbody id="tbData">
				<td></td>
              </tbody>
            </table>
          </div>
		  <div id="extraStorage">
		  </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../js/bootstrap.min.js"></script>
	<script type="text/javascript">
	
		$( document ).ready(function(){
	
		var surveyData = <?php echo json_encode($surveys); ?>;
		var censusData = <?php echo json_encode($census); ?>;
		var userData = <?php echo json_encode($users); ?>;
		
		var ctxsd = document.getElementById("surveychart").getContext("2d");
		var ctxcd = document.getElementById("censuschart").getContext("2d");
		var ctxud = document.getElementById("userchart").getContext("2d");
		
		var datasd = [{value:surveyData.Open,color:"#6747FF",highlight:"#7959FF",label:"Open"},{value:surveyData.Archived,color:"#67FF4F",highlight:"#83FF71",label:"Archived"}];
		var datacd = [{value:censusData.Vampire,color:"#E881A8",highlight:"#E89EBA",label:"Vampires"},{value:censusData.Ghoul,color:"#FF6852",highlight:"#FF917C",label:"Ghouls"},{value:censusData.Mortal,color:"#E8E33D",highlight:"#E8E263",label:"Mortals"}];
		var dataud = [{value:userData.Registered,color:"#FFAC40",highlight:"#FFBCSF",label:"Registered"},{value:userData.Unregistered,color:"#8CFFEE",highlight:"#BDFFFC",label:"Unregistered"}];
		
		var ctxsdc = new Chart(ctxsd).Doughnut(datasd,{animatescale:true});
		var ctxcdc = new Chart(ctxcd).Doughnut(datacd,{animatesale:true});
		var ctxudc = new Chart(ctxud).Doughnut(dataud,{animatescale:true});
		
		$(function () {
			$(document).tooltip({
				selector:'[data-toggle="tooltip"]'
			})
		});
		
		});
	
	</script>
	<script src="staff.js"></script>	
  </body>
</html>