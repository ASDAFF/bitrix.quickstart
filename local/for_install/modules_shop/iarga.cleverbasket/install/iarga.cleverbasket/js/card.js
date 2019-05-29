var imagepath = "/bitrix/templates/.default/components/bitrix/sale.basket.basket/iarga.cleverbasket/images";
var antiCusel = 0;
jQuery(document).ready(function() {

	jQuery(".uniform_ia").initUnuform_ia();

	imagepath = jQuery("#b_template_ia").val()+"/images/";


	// Удаление из корзины
	jQuery(".link-remove-card_ia").click(function(){
		jQuery(".info-amount_ia").load(jQuery("#b_template_ia").val()+"/basket.php",{"add":jQuery(this).parents(".item_ia").find(".in_cart_ia").attr("data-rel"),'num':-1},b_loaded_ia);
		jQuery(this).parents(".item_ia").slideUp(200,function(){jQuery(this).remove();});
		
		return false;
	});

	// Смена по мышке
	jQuery(".amount-card_ia input, .manipulation_ia input").bind("focus",function(){jQuery(this).addClass("focus");});
	jQuery(".amount-card_ia input, .manipulation_ia input").bind("blur",function(){jQuery(this).removeClass("focus");});
	jQuery(".amount-card_ia input, .manipulation_ia input").bind("keyup mouseup",function(event){
		if(event.keyCode==38 || event.keyCode==187) jQuery(this).val(getval(jQuery(this).val())+1);
		else if(event.keyCode==40 || event.keyCode==189) jQuery(this).val( jQuery(this).val()>1 ? jQuery(this).val()-1:1);
		var id = jQuery(this).attr('data-rel');
		if(id==undefined) id = jQuery(this).parents('.amount-card_ia').attr('data-rel');
		jQuery(".info-amount_ia").load(jQuery("#b_template_ia").val()+"/basket.php",{"add":id,'num':jQuery(this).val()},b_loaded_ia);
	});

	// Кнопки плюс и минус
	jQuery(".select-number_ia .plus_ia, .select-number_ia .minus_ia").click(function(){
		var val = jQuery(this).parent().find("input").val();
		if(jQuery(this).hasClass("plus_ia")) val ++;
		else if(jQuery(this).hasClass("minus_ia")) val --;
		if(val < 1) val = 1;
		jQuery(this).parent().find("input").val(val).keyup();
		return false;
	});
	


	// Change person type
	jQuery(".PERSON_TYPE input").on('click change',function(){
		preloader_start_ia(this);
		$.post(jQuery("#b_template_ia").val()+"/props.php",{"person_type":this.value},function(data){
			preloader_stop_ia(this);
			jQuery(".order_props_ia").html(data);
			Custom.init();
			iargaInitProps();			
		});
	});
	iargaInitProps();
	jQuery(".info-amount_ia").load(jQuery("#b_template_ia").val()+"/basket.php",{},b_loaded_ia);
});


iargaInitProps = function(){
	jQuery(".autosave_ia").find("input, select").unbind('mouseup change keyup').bind('mouseup change keyup',function(){
		val = jQuery(this).val();
		if(jQuery(this).attr('type')=='checkbox' && !jQuery(this).is(":checked")) val = false; 
		setcookie_ia(jQuery(this).parents(".autosave_ia").attr("data-rel")+'_'+jQuery(this).attr('name'),val);
	});
	jQuery(".discount_code").on("keyup",function(){
		preloader_start_ia(this);
		jQuery(".info-amount_ia").load(jQuery("#b_template_ia").val()+"/basket.php",{"discount_code":jQuery(this).val()},function(){b_loaded_ia(); preloader_stop_ia();});
	});

	// Change city
	jQuery(".location select").change(function(ev){
		preloader_start_ia(this);
		$.post(jQuery("#b_template_ia").val()+"/delivery.php",{"city":this.value},function(data){
			preloader_stop_ia(this);
			jQuery(".delivery_ajax_ia").html(data);
			Custom.init();
			iargaInitCities();
		});
	});
	jQuery(".location select").change();
};
iargaInitCities = function(){
	// Change delivery type
	jQuery("input[name='delivery']").live('change',function(){
		jQuery(".info-amount_ia").load(jQuery("#b_template_ia").val()+"/basket.php",{'delivery_price':jQuery(this).attr('data-rel'), 'delivery':jQuery(this).val()},b_loaded_ia);
	});
}

function b_loaded_ia(){
	jQuery(".info-amount_ia input").each(function(){
		var inp = jQuery(".amount-card_ia[data-rel="+jQuery(this).attr("name")+"]");
		var val = inp.show().find("input:not(.focus)").val(jQuery(this).val());
	});	

	jQuery(".numgoods").html(jQuery(".info-amount_ia input[name=numgoods]").val());
	jQuery("span.discount_value").html(jQuery(".info-amount_ia input[name=discount_value]").val());
	jQuery(".allgoods").html(jQuery(".info-amount_ia input[name=allgoods]").val());
	jQuery(".total-pay_ia strong").html((jQuery(".info-amount_ia input[name=allsumm]").val())+jQuery(".info-amount_ia input[name=valute]").val());
	jQuery(".amount-card input.focus").focus().parent().show().prev().hide();

	if(jQuery(".info-amount_ia input").length > 5) jQuery(".total-pay").show();
	else jQuery(".total-pay").hide();


	if(jQuery(".card_ia .item_ia").length < 1){ jQuery(".card_ia, .checkout_ia, .total-pay_ia, .checkout-button_ia, .submit_ia").hide(); jQuery(".nocard_ia").show();}
	else { jQuery(".card_ia, .checkout_ia, .total-pay_ia, .checkout-button_ia, .submit_ia").show(); jQuery(".nocard_ia").hide();}


}
function preloader_start_ia(targ){
	if(jQuery(".iarga_preloared").length < 1){
		jQuery("<img src='"+imagepath+"/preloader.gif' class='iarga_preloared'>").prependTo(jQuery(targ).parents("dd"));
		setTimeout(function(){jQuery(".iarga_preloared").fadeOut(function(){jQuery(this).remove();});},3000);
	}
}

function preloader_stop_ia(){
	jQuery(".iarga_preloared").fadeOut(function(){jQuery(this).remove();});
}
function setcookie_ia(name,val){
	$.post(jQuery("#b_template_ia").val()+'/setcookie.php',{'name':name,'val':val});
}


//  uniform_ia 
$.fn.initUnuform_ia = function(){
	// jquery uniform_ia - needs Jquery

	jQuery(this).find("input.repl, textarea.repl").focus(function(){if(jQuery(this).val()==jQuery(this).attr("data-alt")) jQuery(this).val("");});
	jQuery(this).find("input.repl, textarea.repl").blur(function(){if(jQuery(this).val()=="") jQuery(this).val(jQuery(this).attr("data-alt"));});
	jQuery(this).find("input.repl, textarea.repl").keyup(function(){if(jQuery(this).val()=="" || jQuery(this).val()==jQuery(this).attr("data-alt")) jQuery(this).removeClass("act"); else jQuery(this).addClass("act");});
	jQuery(this).find("input.repl, textarea.repl").keyup();

	jQuery(this).find(".submit_ia").unbind('click').bind('click',function(){
		if(jQuery(this).attr('name')!=undefined) jQuery("<input type='hidden' name='"+jQuery(this).attr('name')+"' class='tempadd'  value='1'>").appendTo(jQuery(this).parents('form'));		
		jQuery(this).parents('form').submit(); return false;
	});

	jQuery(this).find("input").bind('keypress',function(event,form){
		if(event.keyCode==13){
			jQuery(this).parents("form").submit();
		}
	});

	
	jQuery(this).submit(function(){

		if(antiCusel == 0){
			jQuery(".uniform_ia.active p.step").html('');
			jQuery(".uniform_ia.active .errortext").each(function(){jQuery(this).closest(".error").removeClass("error")})
			jQuery(this).addClass("active").removeClass("success");
			jQuery(this).find(".element-form").removeClass("error");
			jQuery(this).find("p.error").show().html("<img src='"+imagepath+"preloader.gif'>").removeClass("success");
			var inps = jQuery(this).find("input.repl, textarea.repl");
			for(i=0;i<inps.length;i++){
				jQuery("<input type='hidden' name='"+inps.eq(i).attr("name")+"_default_value' value='"+inps.eq(i).attr('data-alt')+"' class='tempadd'>").appendTo(jQuery(this));
			}

			jQuery("#newframe").remove();
			var newframe = jQuery('<iframe id="newframe" name="newframe" src="'+jQuery(this).attr('action')+'"></iframe>').appendTo("body").hide();
			var newform = jQuery(this).attr("target","newframe").attr("method","post").attr("enctype","multipart/form-data");

		
			
			//newform.submit();

			newframe.bind('load',function(){
				jQuery(".tempadd").remove();
				var data = jQuery(this).contents().find('body').html();
				if (typeof handler == 'function') {
					handler(data);
				}
				if(data.match("step") && jQuery(".uniform_ia.active").find(".step").length){
					var mat = data.match(/step ([0-9]+):([0-9]+)/);
					jQuery(".uniform_ia.active").find(".skipadr").remove();
					jQuery(".uniform_ia.active").find(".skip_adr").remove();
					jQuery(".uniform_ia.active").append(jQuery("<input type='hidden' name='skip' class='skip'>").val(mat[2]));
					jQuery(".uniform_ia.active").append(jQuery("<input type='hidden' name='skip_adr' class='skip_adr'>").val(mat[1]));
					jQuery(".uniform_ia.active p.step").html(jQuery(".uniform_ia.active p.step").html()+'<br>'+data.replace('step','').replace('error',''));
					jQuery(".uniform_ia.active p.active").html('');
					setTimeout(function(){jQuery(".uniform_ia.active").removeClass('active').submit();},2000);
				}else if(data.match("error")){
					setTimeout((function(data_){return function(){
						var mat = data.match(/errorblock/);
						if(mat){
							dataArr = data.replace("error ","").split(/errorblock:[0-9a-zA-Z_\-]+/);
							codesArr = data.replace("error ","").match(/errorblock:[0-9a-zA-Z_\-]+/g);	
							for(i in dataArr){
								if(codesArr[i]!=undefined){
									code = codesArr[i].replace('errorblock:','');
									$code = jQuery(".uniform_ia.active").find("input[name='"+code+"'], textarea[name='"+code+"']");
									if($code.length){
										$code.parent().addClass("error").find(".errortext").html(dataArr[i]);
										data = data.replace(dataArr[i]+codesArr[i],"");
									}else{
										data = data.replace(dataArr[i],"");
									}
								}
							}	
							jQuery(".uniform_ia.active p.error").html(data.replace("error ","")).fadeIn();
							jQuery(".uniform_ia.active").removeClass("active");
						}else{
							jQuery(".uniform_ia.active p.error").html(data.replace("error ","")).fadeIn();
						}
					}})(data),200);			
				}else  if(data.match("success")){
					setTimeout((function(data_){return function(){
						if(data.match("refresh")) history.go(0);
						else if(data.match("redirect")){
							var mat = data.split(":");
							if(mat) location.href = (mat[1]);
						}else if(data.match("alert")){
							alert("success");
						}else if(data.match("closeit")){
							jQuery(".uniform_ia.active").html("<p>"+data.replace("success ","").replace("closeit","")+"</p>");
							setTimeout(function(){jQuery(".popup_bg").click();},3000);
						}else{
							if(data.match("clear")){
								data = data.replace("clear", "");
								jQuery(".uniform_ia.active input, .uniform_ia.active textarea").each(function(){
									if(jQuery(this).attr("type")=="text") jQuery(this).val("");
									else if(jQuery(this).is("textarea")) jQuery(this).val("");
									else if(jQuery(this).attr("type")=="checkbox") jQuery(this).arrt("checked", false);
									else if(jQuery(this).attr("type")=="radio") jQuery(this).arrt("checked", false);
								});
							}
							if(data.match("nodelete")){
								jQuery(".uniform_ia.active p.error").html(data.replace("success ","").replace("nodelete","")).addClass("success");
							}else{
								jQuery(".uniform_ia.active").html("<p>"+data.replace("success ","")+"</p>");
							}
							//jQuery(".uniform_ia.active p.error").html(data.replace("success ","")).addClass("success").fadeIn();
						}
						jQuery(".uniform_ia.active").removeClass("active");
					}})(data),200);		
				}else{
					jQuery(".uniform_ia.active p.error").html("").fadeIn();
					jQuery(".uniform_ia.active").removeClass("active");
				}
			});
		}
		//return false;
	});
}
