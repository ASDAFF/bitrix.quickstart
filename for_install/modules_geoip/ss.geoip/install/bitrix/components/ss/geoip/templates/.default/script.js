$(document).ready(function(){

	$('#sec-popup-link').on('click', function(){
		$('#sec-popup, #sec-popup-bg').fadeIn(300);
	});

	$('#sec-popup-close, #sec-popup-bg').on('click', function(){
		$('#sec-popup, #sec-popup-bg').fadeOut(300);
	});

	$('#sec-popup').on('click', '.sec-cities-link, .sec-autocomplete-line', function(){

		var id = $(this).data('id');
		$('#sec-popup, #sec-popup-bg').fadeOut(300);

		$.ajax({
			type: "POST",
		    url: "/bitrix/components/ss/geoip/templates/.default/ajax.php",
			data: ({SS_AJAX_SITY_ID : id, SS_AJAX:'Y'}),
			success: function(result){
				if(result == 'SetCookie')
				{
					location.reload();
				}
			}
		});
	});

	$('#sec-popup').on('keyup', '.form-input input', function(){

		if($(this).val().length < 2)
		{
			$('#sec-autocomplete').hide();
		}
		else
		{
			$.ajax({
				type: "POST",
			    url: "/bitrix/components/ss/geoip/templates/.default/ajax.php",
				data: ({SS_AJAX_SEARCH_REQUEST : $(this).val(), SS_AJAX:'Y'}),
				success: function(result){
					if(result)
			        {
			        	$('#sec-autocomplete').html(result);
			        	$('#sec-autocomplete').show();
			        }
				}
			});
		}
	});

	$('#sec-popup').on('click', function(e){
		var div = $('#sec-autocomplete, .form-input input');
		if(div.is(':visible'))
		{
			if(!div.is(e.target))
			{
				$('#sec-autocomplete').hide();
			}
		}
	});

	$('#sec-popup').on('focus', '.form-input input', function(){
		if($('#sec-autocomplete').children().length > 0)
		{
			$('#sec-autocomplete').show();
		}
	});
});