function ChangeGenerate(val)
{
	if(val)
		document.getElementById("sof_choose_login").style.display='none';
	else
		document.getElementById("sof_choose_login").style.display='block';

	try{document.order_reg_form.NEW_LOGIN.focus();}catch(e){}
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
}


$(function(){
    
    
    
   
    
       $('[name = ORDER_PROP_3]').change(function(){
            
            val = $(this).val();
            
            if(val == 670)
                $('.metro_').show();
            else 
                 $('.metro_').hide();
            
        });
        
        
         if($('[name = ORDER_PROP_3]').size() == 1){
       $('[name = ORDER_PROP_3]').change();
    }
    
    
    
      
       $('[name = ORDER_PROP_1]').change(function(){
            
            val = $(this).val();
            
            if(val == 670)
                $('.metro_').show();
            else 
                 $('.metro_').hide();
            
        });
        
        
         if($('[name = ORDER_PROP_1]').size() == 1){
       $('[name = ORDER_PROP_1]').change();
    }
         
        
    $('#goCartBtn').live('click', function(){
        location.href='/basket/';
        return false;
    });
    
    if($('[name = PERSON_TYPE]').val() == 2){
        
        $('[name = PAY_SYSTEM_ID]').live('click',  function(){
            
            if($(this).val() == 1){
                
                $('.m-cart-field__first div').hide();
                
            } else {
                
                $('.m-cart-field__first div').show(); 
                
            }
            
        });
    };
    
 
    $('#first_step_btn').live('click', function(){
        
        var NEW_EMAIL = $("#NEW_EMAIL").val();
        if(!NEW_EMAIL) {
                alert('Не введён E-mail');
                return false;  
            }else if(!isValidEmailAddress(NEW_EMAIL)){
                alert('Введён не корректный адрес E-mail');
                return false;
            } 
        
        var NEW_LAST_NAME = $("#NEW_LAST_NAME").val();
        if(!NEW_LAST_NAME) {
                alert('Не введёно ФИО');
                return false;
            }
            
        var PERSONAL_PHONE = $("#PERSONAL_PHONE").val();    
        if(!PERSONAL_PHONE) { 
                alert('Не введён телефон');
                return false;
            }
    
    });
      
    
    $('[name = DELIVERY_ID]').live('click', function(){
 
        if($(this).val() == 1){
            $('#addr').hide();
            $('#store').show();}
        else{
            $('#addr').show();    
            $('#store').hide();}
        
        
         if($(this).val() == 4){
             
             $('[name = ORDER_PROP_3]').val(670).hide();
            $('[name = ORDER_PROP_3]').change();
             
             $('[name = ORDER_PROP_1]').val(670).hide();
             $('[name = ORDER_PROP_1]').change();
            $('.city_label_').hide();
         } else   {
             $('[name = ORDER_PROP_3]').show();
             $('[name = ORDER_PROP_1]').show();  $('.city_label_').show();
            }
        
        
    });
    
    
    
    $('#seond_step_btn').live('click', function(){
   
  
        if(!$('[name = DELIVERY_ID]').filter('[checked = checked]').size()){
               alert('Не выбран способ доставки'); 
               return false;
        }
        
     
        if($('[name = DELIVERY_ID]').filter('[checked = checked]').val() != 1){
             
            if($("[name = ORDER_PROP_5]").size()){
                
                var ORDER_PROP_5 = $("[name = ORDER_PROP_5]").val();    
                if(!ORDER_PROP_5) { 
                        alert('Не введён адрес'); 
                        return false;
                    }

//                var ORDER_PROP_7 = $("[name = ORDER_PROP_7]").val();    
//                if(!ORDER_PROP_7) { 
//                        alert('Не введён комментарий'); 
//                        return false;
//                    }

   
             } else {
                 
                 
               var ORDER_PROP_4 = $("[name = ORDER_PROP_4]").val();    
                if(!ORDER_PROP_4) { 
                        alert('Не введён адрес'); 
                        return false;
                    }
 
//                var ORDER_PROP_6 = $("[name = ORDER_PROP_6]").val();    
//                if(!ORDER_PROP_6) { 
//                        alert('Не введён комментарий'); 
//                        return false;
//                    }
 
             }
        
        } else {
            
             if($("[name = ORDER_PROP_12]").size()){
                 
                if(!$("[name = ORDER_PROP_12]").filter('[checked = checked]').size()) { 
                       alert('Не выбран склад'); 
                       return false;
                    }
                    
             } else if($("[name = ORDER_PROP_11]").size()){
                    
                if(!$("[name = ORDER_PROP_11]").filter('[checked = checked]').size()) { 
                       alert('Не выбран склад'); 
                       return false;
                    }
                    
             }
 
        }
        
    });
    
});