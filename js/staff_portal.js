$(document).ready(function(){
    
    $(".button-collapse").sideNav();
    
});
    
$(document).on("click","[name='TransToSurveys']",function(event){
    
    $("#content").fadeOut("slow", function(){
    
        $("#content").load("sections/surveys.html",function(){
        
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