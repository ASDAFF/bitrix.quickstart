$(function(){
    
    $('.b-uc__edit').bind('click', function(){ 
     
        $(this).parent().next("div").toggle(); 
  
        return false;  
     
    });
}); 
 