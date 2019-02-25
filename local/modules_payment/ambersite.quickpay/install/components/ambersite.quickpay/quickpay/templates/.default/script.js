if (!window.jQuery) {alert('jQuery is not installed!');}
function quickpaysum(inputval) {
	var paymentType = inputval;
	var sum = parseInt($('input[name=defaultsum]').val());
	var count = 1;
	if($('input[name=count]').length>0) {var count = parseInt($('input[name=count]').val());}
	if(typeof count === 'number' && count > 0) {sum = sum*count;}
	if(paymentType=='AC' && $('#quickpayform').data('comission')=='y') {var a = 0.02; sum = sum/(1-a);} 
	if(paymentType=='PC' && $('#quickpayform').data('comission')=='y') {var a = 0.005; sum = sum/(1-(a/(1+a)));} 
	sum = Math.ceil(sum); 
	$('input[name=sum]').val(sum); 
	$('#sendquickpayform span').text(sum);
}
$(document).ready(function() {
	if($('#quickpayform').data('roboto')=='y') {$('#quickpayform').css('fontFamily', '\'Roboto Condensed\''); $('#quickpayform input, #quickpayform textarea').css('fontFamily','\'Roboto Condensed\'');};
	$('#quickpayform input, #quickpayform select').styler();
	$('#quickpayform input[name=phone]').mask("+7 (999) 999-9999"); 
	if(!!$('#sendquickpayform').data('bgcolor')) {$('#sendquickpayform').css('background', $('#sendquickpayform').data('bgcolor'));}
	if(!!$('#sendquickpayform').data('textcolor')) {$('#sendquickpayform').css('color', $('#sendquickpayform').data('textcolor'));}
	quickpaysum($('input[name=paymentType]:checked').val());
	$('#paytype input, input[name=count]').on('change', function() {quickpaysum($('input[name=paymentType]:checked').val())});
	$('#quickpayform div.jq-number__spin').on('click', function() {quickpaysum($('input[name=paymentType]:checked').val())});
	
	$('#sendquickpayform').click(function() { 
		var that = this; 
		var $x; $(".qp_required").each(function() { if(!$(this).val().length) {$(this).css('border', '1px solid red'); $x = 'empt';} else {$(this).css('border', '1px solid #CCC');}});
		if($x != 'empt') {
			$(that).text($(that).data('otpravka')+'...'); 
			var serialdata = $('#quickpayform').serialize();
			$('#quickpayform input[type=text], #quickpayform input[type=number], #quickpayform textarea').attr('disabled','disabled');
			$.ajax({  
				type: "POST",  
				url: $(that).data('sendpath'),  
				data: serialdata,  
				dataType: "json",  
				success: function(msg){              
					if(msg['SEND']=='Y') { 
						$('input[name=targets]').val(msg['TARGET']); 
						$('input[name=label]').val(msg['ORDERCODE']); 
						$('input[name=successURL]').val(msg['SUCCESSURL']); 
						$('#quickpayform').submit(); 
					}  
				}  
			}); 
		}
	})
})