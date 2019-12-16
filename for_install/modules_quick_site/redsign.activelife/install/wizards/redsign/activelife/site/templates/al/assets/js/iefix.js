function RSAL_PlaceHolderForIE()
{
	if(window.navigator.userAgent.indexOf("MSIE")!=-1 && window.navigator.userAgent.indexOf("MSIE 10")<1){
		$("input[type='text'],textarea").each(function(){
			if($(this).val()==""){
				var ph = $(this).attr("placeholder");
				$(this).val(ph).css('color','#c6c6c7');
			}
		}).on('focus',function(){
			var ph = $(this).attr('placeholder');
			if($(this).val()==ph){
				$(this).val('').css('color','');
			}
		}).on('blur',function(){
			var ph = $(this).attr('placeholder');
			if($(this).val()==""){
				$(this).val(ph).css('color','#c6c6c7');
			}
		});
		// clear val if = placeholder
		$(document).on('submit','form',function(){
			$(this).find("input[type='text'],textarea").each(function(){
				var ph = $(this).attr('placeholder');
				if($(this).val()==ph){
					$(this).attr('value','');
				}
			})
		});
	}
}
var rsInputInit;
$(document).ready(function(){
	if(window.navigator.userAgent.indexOf("MSIE 7") != -1){
		rsInputInit = function(){
			$('input[type="checkbox"], input[type="radio"]').each(function(){
				if($(this).is(':checked')){
					$(this).next('label').addClass('checked').prepend('<span class="input_ie"></span>');
				}
				else{
					$(this).next('label').removeClass('checked').prepend('<span class="input_ie"></span>');
				}
			});
		}
		rsInputInit();
		$(document).on('click', 'input[type="checkbox"]+label , input[type="radio"]+label', function(){
			var $input = $(this).prev();
			if($input.attr('type') == 'radio'){
				$('input[type="radio"][name="'+$input.attr('name')+'"]').removeAttr('checked').next('label').removeClass('checked');
			}
			if($(this).hasClass('checked')){
				$(this).removeClass('checked');
				$input.removeAttr('checked');
			}
			else{
				$(this).addClass('checked');
				$input.attr('checked','checked');
			}
		});
	}
	else if(window.navigator.userAgent.indexOf("MSIE 8") != -1){
		rsInputInit = function(){
			$('input[type="checkbox"], input[type="radio"]').each(function(){
				if($(this).is(':checked')){
					$(this).next('label').addClass('checked');
				}
				else{
					$(this).next('label').removeClass('checked');
				}
			});
		}
		rsInputInit();
		$(document).on('change', 'input[type="checkbox"], input[type="radio"]', function(){
			
			var $input = $(this);
			if($input.attr('type') == 'radio'){
				$('input[type="radio"][name="'+$input.attr('name')+'"]').next('label').removeClass('checked');
			}
			if($(this).is(':checked')){
				$(this).next('label').addClass('checked');
			}
			else{
				$(this).next('label').removeClass('checked');
			}
		});
	}
	RSAL_PlaceHolderForIE();
});
