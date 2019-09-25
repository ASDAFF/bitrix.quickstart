<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
error_reporting(0);
header('Content-Type: text/html; charset='.SITE_CHARSET);

__IncludeLang($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH."/lang/".LANGUAGE_ID."/ajax/oneclickbyu.php");
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {	
?>
<form name="SIMPLE_FORM_1" id="SIMPLE_FORM_1" action="/ajax/oneclickbyu.php" method="POST" enctype="multipart/form-data">              
    <input type="hidden" name="WEB_FORM_ID" value="1" />
    <div class="form-buyone">
    	<div>            
    	<input type="text" placeholder="<?=GetMessage("ONE_NAME")?>" class="inputtext" name="user_name" value="" />		
        </div>
    	<div>            
    	<input type="text" type="tel" id="user_phone_one" placeholder="<?=GetMessage("ONE_PHONE")?>" required pattern="[0-9_-]{10}" title="(096) 999 99 99" autocomplete="off" class="inputtext" name="user_phone" value=""/>		</div>
    	<div>           
    	<input type="text" placeholder="E-mail" class="inputtext" name="user_mail" value=""/>		</div>
        <input type="hidden"  name="product_id" id="product_id_one" value="<?=intval($_REQUEST['PRODUCT_ID'])?>"/>      
        <div class="btns-byuone"> 
    	   <input class="em_button" type="submit"  id="btn_oneclick" name="web_form_submit" value="<?=GetMessage("ONE_BNT")?>"/>
    	   <input type="hidden" name="feedback_type" value="buyoneclick"/>                			
    	</div>
    </div>
</form>
<?
}
?>
<script type="text/javascript">
$(function(){
    	if(!is_mobile())
		{
			$('#user_phone_one')
				.mask("+7(999) 999-99-99")
				.removeAttr('required')
				.removeAttr('pattern')
				.removeAttr('title');
				
			$('#user_phone_one').on("focusin", function(){
				$(this).attr({'placeholder':'(___) ___ __ __'});
				$('.feedback-window .user_phone span').show();
			});
			$('#user_phone_one').on("focusout", function(){
				if($(this).text = '')
				{
					$(this).attr({'placeholder':'<?=GetMessage("ONE_PHONE")?>'});
					$('.feedback-window .user_phone span').hide();
					$(this).removeClass('focus');
				}
				else
				{
					$(this).addClass('focus');
				}
			});
		}
 	
     var submit = $("#btn_oneclick");
     submit.click( function(event){
		event.preventDefault();		
		$.ajax({						
			type: "POST",
			url: EmarketSite.SITE_DIR+"ajax/send_message.php",
			data: $("#SIMPLE_FORM_1").serialize(),
			success: function(data){				
                var res = BX.parseJSON(data);
                if(res)
                {
                    if(res.error)
                    {                         
                             $("<div/>", {
                                            "class": "res-feedback",
                                            html: "<span class='error'>"+BX.message(res.msg)+"</span>",                          
                                        }).appendTo(".emodal-data");
                            setTimeout(function(){
	                               $("#emarket-feedback-response").remove();
                                    $("#OneClickEmodal").eModalClose();  
	                               }, 3000);
                              
                                 
                    }else
                    {
                        $("<div/>", {
                                            "class": "res-feedback",
                                            html: "<span>"+BX.message(res.msg)+"</span>",                          
                                        }).appendTo(".emodal-data");
                            setTimeout(function(){
	                               $("#emarket-feedback-response").remove();
                                    $("#OneClickEmodal").eModalClose();  
	                               }, 3000);       
                    }                                        
                }else
                {
                    alert(data);    
                }                                    				
			}
		});		
	});
});
</script>
<?}?>       