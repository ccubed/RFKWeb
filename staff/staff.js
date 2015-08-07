$( document ).ready( function(){

	//Load Survey Functionality
	$("#SurveysNB").click( function(){
		
		document.getElementById("dataHeader").innerHTML = "Open Surveys";
		document.getElementById("tbHead").innerHTML = "<th>Name</th><th>Title</th><th>Type</th><th>Toolbox</th>";
		var msa = document.getElementById("extraStorage");
		var tbd = document.getElementById("tbData");
		tbd.innerHTML = "";
		msa.innerHTML = "";
		
		$.get("../exec/getSurveys.php", function( data ){
			
			for (var i=0; i < data.length; i++){
			
				tbd.innerHTML += "<tr><td>" + data[i].Name + "</td><td>" + data[i].Title + "</td><td>" + data[i].Type + "</td><td>" + data[i].Tools + "</td></tr>";
				msa.innerHTML += data[i].modal;
				
			}
			
		},"json");
	
	});
	
	//Load Archived Surveys Functionality
	$("#SurveysANB").click( function(){
	
		document.getElementById("dataHeader").innerHTML = "Archived Surveys";
		document.getElementById("tbHead").innerHTML = "<th>Name</th><th>Title</th><th>Type</th><th>Toolbox</th>";
		var msa = document.getElementById("extraStorage");
		var tbd = document.getElementById("tbData");
		tbd.innerHTML = "";
		msa.innerHTML = "";
			
		$.get("../exec/getSurveysA.php", function( data ){
			
			for (var i=0; i < data.length; i++){
			
				tbd.innerHTML += "<tr><td>" + data[i].Name + "</td><td>" + data[i].Title + "</td><td>" + data[i].Type + "</td><td>" + data[i].Tools + "</td></tr>";
				msa.innerHTML += data[i].modal;
				
			}
			
		},"json");
	
	});
	
	//Load Registered Users Functionality
	$("#UsersNB").click( function(){
	
		document.getElementById("dataHeader").innerHTML = "Registered Users";
		document.getElementById("tbHead").innerHTML = "<th>Username</th><th>PC Name</th><th>Role</th><th>Toolbox</th>";
		var msa = document.getElementById("extraStorage");
		var tbd = document.getElementById("tbData");
		tbd.innerHTML = "";
		msa.innerHTML = "";
			
		$.get("../exec/getUsers.php", function( data ){
			
			for (var i=0; i < data.length; i++){
			
				tbd.innerHTML += "<tr><td>" + data[i].UName + "</td><td>" + data[i].PCName + "</td><td>" + data[i].Role + "</td><td>" + data[i].Tools + "</td></tr>";
				
			}
			
		},"json");
	
	});
	
	//Load List Bloodpools Functionality
	$("#BloodpoolsNB").click( function(){
	
		document.getElementById("dataHeader").innerHTML = "List Bloodpools";
		document.getElementById("tbHead").innerHTML = "<th>Location</th><th>Toolbox</th>";
		var msa = document.getElementById("extraStorage");
		var tbd = document.getElementById("tbData");
		tbd.innerHTML = "";
		msa.innerHTML = "";
			
		$.get("../exec/getBloodpools.php", function( data ){
			
			for (var i=0; i < data.length; i++){
			
				tbd.innerHTML += "<tr><td>" + data[i].LocName + "</td><td>" + data[i].Tools + "</td></tr>";
				msa.innerHTML += data[i].modal;
				
			}
			
		},"json");
	
	});
	
	//Load List Territories Functionality
	$("#TerritoriesNB").click( function(){
	
		document.getElementById("dataHeader").innerHTML = "List Territories";
		document.getElementById("tbHead").innerHTML = "<th>Name</th><th>Kingpin</th><th>Attitude</th><th>Toolbox</th>";
		var msa = document.getElementById("extraStorage");
		var tbd = document.getElementById("tbData");
		tbd.innerHTML = "";
		msa.innerHTML = "";
			
		$.get("../exec/getTerritories.php", function( data ){
			
			for (var a=0; a < data.length; a++){
			
				tbd.innerHTML += "<tr><td>" + data[a].name + "</td><td>" + data[a].Kingpin + "</td><td>" + data[a].Attitude + "</td><td>" + data[a].Tools + "</td></tr>";
				msa.innerHTML += data[a].modal;
				
			}
			
		},"json");
	
	});

});

//Activate Archive Button
$(document).on( "click", "button[name='archive']", function(event){
				
	var a = document.getElementById("tbData");
	$.post( "../exec/archiveSurvey.php", { id: event.target.id } );
	a.removeChild(event.target.parentElement.parentElement.parentElement);
				
});

//Activate Save Changes Button on Territories
$(document).on( "click", "button[name='modsaves']", function(event){

	var b = event.target.id;
	var c = document.getElementById("fmodcas" + b).value;
	var d = document.getElementById("fmodmug" + b).value;
	var e = document.getElementById("fmodsand" + b ).value;
	$.post( "../exec/updateTerritoryMods.php", { id: b, fmodcas: c, fmodmug: d, fmodsand: e } );
	event.target.className = "btn btn-success";
	event.target.innerHTML = "Changes Saved";
	var a = event.target;
	setTimeout(function(){
		
		a.className = "btn btn-primary";
		a.innerHTML = "Save Changes";
	
	},3000,a);
				
});

//Activate Make Staff Button
$(document).on( "click", "button[name='makeStaff']", function(event){

	var a = event.target;
	if ( a.className == "glyphicon glyphicon-sunglasses" ){
	
		a = a.parentElement;
		
	}
	$.post( "../exec/makeStaff.php", { id: a.id } );
	a.parentElement.parentElement.parentElement.childNodes[2].innerHTML = "Staff";
				
});