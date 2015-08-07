<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Requiem for Kingsmouth Web Tools">
    <meta name="author" content="Charles C Click;Oukranos;Gizmo;Alzie">
    <link rel="icon" href="../../favicon.ico">

    <title>Requiem for Kingsmouth</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/navbar.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="../js/js.cookie.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <!-- Static navbar -->
      <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">RFK Web Tools</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="../index.html">Home</a></li>
              <li><a href="../about.html">About</a></li>
			  <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Player General<span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="../feedback.html">Contact Staff</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">OSS<span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li class="active"><a href="patrols.php">Check Patrols</a></li>
                </ul>
              </li>
			  <li><a href="../staff/main.php">Staff Area</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right" id="users">
              <li><a href="../auth/login.html">Login</a></li>
              <li><a href="../auth/register.html">Register</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>

      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
        <h1>Current Patrols</h1>
      </div>
	  
	  <div class="table-responsive">
		<table class="table table-hover">
		 <thead>
		    <tr>
				<th>Days Remaining</th>
				<th>Territory</th>
			</tr>
		 </thead>
		 <tbody>
			<?php
			
				$ini = parse_ini_file("../exec/rfk.ini");
			
				//Connect to Database
				$dbcon = new mysqli($ini['dbhost'],$ini['dbuser'],$ini['dbpass'],$ini['dbname'],$ini['dbport']);
				$errors = 0;
			
				if ($dbcon->connect_errno){
					
					echo "<tr class=\"danger\"><td colspan=\"2\" align=\"center\">Connection Error to Database</td></tr>";
					$errors = 1;
				}
				
				if ( !$errors ){
					
					if (!$stmt=$dbcon->prepare("Select Amount,Description FROM Patrols WHERE Player_Dbref=(SELECT Dbref FROM Players WHERE Name=?)")){

						echo "<tr class=\"danger\"><td colspan=\"2\" align=\"center\">Error Retrieving Data from Database</td></tr>";
						$errors = 1;
				
					}
					
					if ( !$errors ){
					
						if (!ISSET($_COOKIE['RFKJournal'])){
					
							echo "<tr class=\"warning\"><td colspan=\"2\" align=\"center\">You need to login first</td></tr>";
							$errors = 1;
				
						}
						
						if ( !$errors ){
				
							$ckdata = json_decode($_COOKIE['RFKJournal'],true);
				
							$stmt->bind_param("s",$pcname);
							$pcname = $ckdata['Player_Name'];
							$stmt->execute();
							$stmt->store_result();
				
							if ( !$errors && $stmt->num_rows > 0){
				
								$stmt->bind_result($col1,$col2);
				
								while( $stmt->fetch() ){
				
									echo "<tr><td>" . $col1 . "</td><td>" . substr($col2,strpos($col2," ")+1) . "</td></tr>";
					
								}
								
							}
							
							$stmt->close();
							
							if ( !$stmt=$dbcon->prepare("SELECT a.desc,DATEDIFF(a.dt_ends,now()) FROM retainer_actions a JOIN retainers b ON b.player_dbref=(SELECT Dbref FROM Players WHERE WebUser=?) AND b.id=a.retainer_id WHERE now() < a.dt_ends")){
							
								$errors = 1;
								
							}
							
							if (!$errors){
								
								$user = $ckdata['Username'];
								$stmt->bind_param("s",$user);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($rd,$rdays);
								
								if ( $stmt->num_rows > 0 ){
									
									while($stmt->fetch()){
									
										echo '<tr><td>' . $rdays . '</td><td>' . substr($rd,strpos($rd," ")+1) . '</td></tr>';
										
									}
									
								} else {
									
									echo '<tr><td align="center" class="info" colspan="2">You don\'t have any patrols.</td></tr>';
									$errors = 1;
									
								}
							
							}
							
						}
						
						$stmt->close();
					
					}
					
					$dbcon->close();
					
				}
				
			?>
		 </tbody>
		</table>
	  </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
	<script type="text/javascript">
	$( document ).ready(function(){
	
			if (jQuery.parseJSON(Cookies.get('RFKJournal')).Username){
			
				document.getElementById("users").innerHTML = "<li><a href=\"#\">" + jQuery.parseJSON(Cookies.get('RFKJournal')).Username + "</a></li><li><span data-placement=\"right\" data-toggle=\"tooltip\" title=\"Settings\"><button type=\"button\" class=\"btn btn-default\" style=\"margin-top:9px;margin-bottom:9px;\"><span class=\"glyphicon glyphicon-cog\"></span></button></span></li><li><span data-placement=\"right\" data-toggle=\"tooltip\" title=\"Logout\"><form action=\"../exec/logout.php\" method=\"post\"><button type=\"submit\" class=\"btn btn-default\" style=\"margin-top:9px;margin-bottom:9px;\"><span class=\"glyphicon glyphicon-off\"></span></button></form></span></li>";
			
			}
			
			$('[data-toggle="tooltip"]').tooltip();
		
	});
	
	</script>
  </body>
</html>
