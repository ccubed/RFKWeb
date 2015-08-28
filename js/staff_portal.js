$(document).ready(function(){
    
    $(".button-collapse").sideNav();
    
});
    
$(document).on("click","[name='TransToSurveys']",function(event){
    
    $("#content").fadeOut("slow", function(){
    
        $("#content").load("sections/surveys.html",function(){
            
            $.get("../exec/getNumSurveys.php", function( data ){
                
                if( data.status == 'success'){
                  
                    document.getElementsByName('OpenSurveysBadge')[0].innerHTML = data.open;
                    document.getElementsByName('ArchivedSurveysBadge')[0].innerHTML = data.archived;
                    
                } else {
                    
                    Materialize.toast("There was an error loading data. Error: " & data.details, 3000, "rounded");
                    
                }
                
            }, "json");
        
            $("#content").fadeIn("slow");
        
        });
        
    });
    
});

$(document).on("click","[name='TransToPortal']",function(event){
    
    $("#content").fadeOut("slow",function(){
        
        $("#content").load("sections/portal.html",function(){
            
            $("#content").fadeIn("slow");
            
        });
        
    });
    
});

$(document).on("click","[name='TransToSurveysOpen']",function(event){
    
    $("#datacontent").fadeOut("slow",function(){
        
        $("#datacontent").load("../exec/listSurveys.php", {"Open": 1}, function(){
            
            $("#datacontent").fadeIn("slow");
            
        });
        
    });
    
});

$(document).on("click","[name='TransToSurveysArchived']",function(event){
    
    $("#datacontent").fadeOut("slow",function(){
        
        $("#datacontent").load("../exec/listSurveys.php", {"Open": 0}, function(){
            
            $("#datacontent").fadeIn("slow");
            
        });
        
    });
    
});

$(document).on("click","[name='TransToSurveyView']",function(event){
    
    $("#content").fadeOut("slow",function(){
        
        
        
    });
    
});