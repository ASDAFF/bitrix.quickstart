$(document).ready(function(){
	$("#feedback-call").on("click", function(event){
		event.preventDefault();
		var params = {};
		delete params;
		params['form_parent'] = $(this).parent();
		params['form_id'] = 'feedback_call';
		params['form_title'] = BX.message('form_title1');
		params['form_title_submit'] = BX.message('form_title_submit1');
		em_show_feedback(params);
	});
    $("#emarket_call_me").on("click", function(event){
		event.preventDefault();
		var params = {};
		delete params;
		params['form_parent'] = $(this).parent();
		params['form_id'] = 'feedback_call_2';
		params['form_title'] = BX.message('form_title2');
		params['form_title_submit'] = BX.message('form_title_submit2');
        params['PRODUCT_ID'] = $(this).data('id');
		em_show_feedback(params);
	});
	$("#feedback-message").on("click", function(event){
		event.preventDefault();
		var params = {};
		delete params;
		params['form_parent'] = $(this).parent();
		params['form_id'] = 'feedback_write';
		params['form_title'] = BX.message('form_title3');
		params['form_title_submit'] = BX.message('form_title_submit3');
		em_show_feedback(params);
	});
})

jQuery.fn.exists = function() {
   return jQuery(this).length;
}

function em_show_feedback(params) 
{
	$('.feedback-bg').remove();
	$('.feedback-window').remove();
	
	var base_html = 
		'<div class="feedback-window" id="'+params['form_id']+'_window">'+
			'<div class="close"></div>'+
			'<div class="title">'+params['form_title']+'</div>'+
			'<input class="em_button disabled" name="feedback_submit" type="submit" value="'+params['form_title_submit']+'"/>'+
			'<input class="em-input-param" name="feedback_type" type="hidden" value="'+params['form_id']+'"/>'+
		'</div>';
		
	if(params['form_parent'])
	{
		params['form_parent'].prepend(base_html);
		$('#'+params['form_id']+'_window').addClass('arrow_box');
	}
	else
		$('body').prepend(base_html);

	switch(params['form_id'])
	{
		case 'feedback_call': 
			$('#'+params['form_id']+'_window').children('.title').after(
				'<input class="em-input-param em-input-required" id="user_name" name="user_name" type="text" placeholder="'+BX.message('js_kr_name')+'" />'+
				'<div class="user_phone">'+
					'<span>+7</span>'+
					'<input class="em-input-param em-input-required" id="user_phone" name="user_phone" type="tel" placeholder="'+BX.message('js_kr_phone')+'" required pattern="[0-9_-]{10}" title="'+BX.message('js_kr_format')+': (096) 999 99 99" autocomplete="off" />'+
				'</div>'
			);

			if(!is_mobile())
			{
				$('#user_phone')
					.mask("(999) 999-99-99")
					.removeAttr('required')
					.removeAttr('pattern')
					.removeAttr('title');
					
				$('#user_phone').on("focusin", function(){
					$(this).attr({'placeholder':'(___) ___ __ __'});
					$('.feedback-window .user_phone span').show();
				});
				$('#user_phone').on("focusout", function(){
					if($(this).text = '')
					{
						$(this).attr({'placeholder':BX.message('js_kr_phone')});
						$('.feedback-window .user_phone span').hide();
						$(this).removeClass('focus');
					}
					else
					{
						$(this).addClass('focus');
					}
				});
			}
		break;
        
        case 'feedback_call_2': 
			$('#'+params['form_id']+'_window').children('.title').after(
				'<input class="em-input-param em-input-required" id="user_name" name="user_name" type="text" placeholder="'+BX.message('js_kr_name')+'" />'+
				'<div class="user_phone">'+
					'<span>+7</span>'+
					'<input class="em-input-param em-input-required" id="user_phone" name="user_phone" type="tel" placeholder="'+BX.message('js_kr_phone')+'" required pattern="[0-9_-]{10}" title="'+BX.message('js_kr_format')+': (096) 999 99 99" autocomplete="off" />'+
				'<input class="em-input-param" type="hidden" name="product_id" value="'+params['PRODUCT_ID']+'" />'+
                '</div>'
			);

			if(!is_mobile())
			{
				$('#user_phone')
					.mask("(999) 999-99-99")
					.removeAttr('required')
					.removeAttr('pattern')
					.removeAttr('title');
					
				$('#user_phone').on("focusin", function(){
					$(this).attr({'placeholder':'(___) ___ __ __'});
					$('.feedback-window .user_phone span').show();
				});
				$('#user_phone').on("focusout", function(){
					if($(this).text = '')
					{
						$(this).attr({'placeholder':BX.message('js_kr_phone')});
						$('.feedback-window .user_phone span').hide();
						$(this).removeClass('focus');
					}
					else
					{
						$(this).addClass('focus');
					}
				});
			}
		break;
		
		case 'feedback_write':
			$('#'+params['form_id']+'_window').children('.title').after(
				'<input class="em-input-param em-input-required" id="user_name" name="user_name" type="text" placeholder="'+BX.message('js_kr_name')+'" />'+
				'<input class="em-input-param em-input-required" id="user_mail" name="user_mail" type="text" placeholder="E-mail" />'+
				'<textarea class="em-input-param em-input-required" id="user_message" name="user_message" type="text" placeholder="'+BX.message('js_kr_message')+'" ></textarea>'
			);

		break;
	}
	
	$('#'+params['form_id']+'_bg').show();
	$('#'+params['form_id']+'_window').fadeIn();
	
	delete ajax_params;

	var form = $('#'+params['form_id']+'_window'),
		submit = form.find('input[type="submit"]');
		close  = form.find('.close'),
		ajax_params = {};

	function checkInput()
	{
		form.find('.em-input-required').addClass('empty_field');
		form.find('.em-input-param').each(function(){
			ajax_params[$(this).attr('name')] = $(this).val();
			
			if($(this).hasClass('em-input-required'))
			{
				if($(this).val() != '')
				{
					$(this).removeClass('empty_field');
				}
				else 
					$(this).addClass('empty_field');
				
				//check phone
				if( !is_mobile() &&
					($(this).attr('name') == 'user_phone'))
				{
					if (($(this).val().indexOf("_") != -1) || $(this).val() == '' )
						$(this).addClass('empty_field');
					else
						$(this).removeClass('empty_field');
				}
				//check mail
				if( ($(this).attr('name') == 'user_mail'))
				{
					var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
					if(pattern.test($(this).val()))
						$(this).removeClass('empty_field');
					else
						$(this).addClass('empty_field');
				}
			}
		});
	}

	function lightEmpty(){
		form.find('.empty_field').css({'border-color':'red'});
		setTimeout(function(){
			form.find('.empty_field').removeAttr('style');
		},500);
	}

	setInterval(function(){
		checkInput();
		var sizeEmpty = form.find('.empty_field').size();

		if(sizeEmpty > 0)
		{
			if(submit.hasClass('disabled'))
				return false;
			else
				submit.addClass('disabled');
		} 
		else 
			submit.removeClass('disabled')
	}, 100);

		submit.on("click", function(event){
		event.preventDefault();
		if($(this).hasClass('disabled'))
		{
			lightEmpty();
			return false;
		} 
		else
		{
			$("<div/>", {
                          "class": "res-feedback",
                          html: "<span>"+BX.message("js_kr_req_send")+"</span>",                          
                    }).appendTo(".feedback-window");
			$.ajax({						
				type: "POST",
				url: EmarketSite.SITE_DIR+"ajax/send_message.php",
				data: ajax_params,
				success: function(data){				                                       					
					setTimeout(function(){
						$('#'+params['form_id']+'_bg').hide();
			             $('#'+params['form_id']+'_window').fadeOut(200);
                         $(".feedback-window").remove(".res-feedback");
					}, 3000);
				}
			});
		}
	});
	
	close.on("click", function(){
		$('#'+params['form_id']+'_bg').hide();
		$('#'+params['form_id']+'_window').fadeOut(200);
	});
}
jQuery.fn.extend({
  eModal: function() {
    var item  = $(this);
    $("<div/>", {
      "id": "overlay",      
    }).appendTo("body").fadeIn(400, // сначала плавно показываем темную подложку
            function(){ // после выполнения предъидущей анимации
                item 
                    .css('display', 'block')
                    .css('margin-left',"-"+item.width()/2+"px")
                    .animate({opacity: 1, top: '50%'}, 200); // плавно прибавляем прозрачность одновременно со съезжанием вниз
        });
  },
  eModalClose: function(){
    $(this).hide();
    $('#overlay').remove();
  } 
});
function BuyOneClick(item)
{
    var id = 0;
    if(item)
    {
        id = $(item).data('id');
    }
   $.post(EmarketSite.SITE_DIR+'ajax/oneclickbyu.php',{PRODUCT_ID:id},function(data){
    $("#OneClickEmodal .emodal-data").html(data).parent().eModal();;
   });
   
}