$(document).ready(function(){

	$('#sec-header-regions').on('click', '.sec-cities-link, .sec-autocomplete-line', function(){

		var id = $(this).data('id');

		$(this).parents('.sec-header-primary-cities').find('ul li').removeClass('sec-your-city');
		$(this).parent().addClass('sec-your-city');

		if($(this).hasClass('sec-autocomplete-line'))
		{
			var text = $(this).text();
			var regexp = /([\S^,]+)[,]+.*/i;
			var match = regexp.exec(text);

			$('#sec-region-open').html(match[1] + '<i></i>');
			$(this).parents('.form-input').find('input').val(text);

			$('.sec-header-primary-cities li').removeClass('sec-your-city');
		}
		else
		{
			$('#sec-region-open').html($(this).text() + '<i></i>');
			$('.form-input').find('input').val('');
		}

		$.ajax({
			type: "POST",
			dataType: "json",
		    url: "/bitrix/components/ss/geoip/templates/mvideo.style/ajax.php",
			data: ({SS_AJAX_SITY_ID : id, SS_AJAX:'Y'}),
			success: function(result)
			{

			}
		});
	});

	$('#sec-header-regions').on('keyup', '.form-input input', function(){

		if($(this).val().length < 2)
		{
			$('#sec-autocomplete').html('').hide();
		}
		else
		{
			$.ajax({
				type: "POST",
			    url: "/bitrix/components/ss/geoip/templates/mvideo.style/ajax.php",
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

	$('#sec-header-regions').on('click', function(e){
		var div = $('#sec-autocomplete, .form-input input');
		if(div.is(':visible'))
		{
			if(!div.is(e.target))
			{
				$('#sec-autocomplete').hide();
			}
		}
	});

	$('#sec-header-regions').on('focus', '.form-input input', function(){
		if($('#sec-autocomplete').children().length > 0)
		{
			$('#sec-autocomplete').show();
		}
	});

	$('#sec-header-wrap').on('click', '#sec-region-close, #sec-region-open', function(){
		$('#sec-header-regions').slideToggle();
		$('#sec-region-open').toggleClass('active');

		if($('#sec-header-wrap, .sec-city-popup').is(':visible'))
		{
			$('#sec-header-wrap .sec-city-popup').hide();
		}
	});

	$('#sec-header-wrap').on('click', '.sec-city-popup-close, .sec-city-popup-enter', function(){
		$('#sec-header-wrap .sec-city-popup').hide(300);
	});

	$('#sec-header-wrap').on('click', '.sec-city-popup-other-city', function(){
		$('#sec-header-regions').slideToggle();
		$('#sec-header-wrap .sec-city-popup').hide();
	});
});

function SetCoordinate(lon, lat)
{
	window.myMap.geoObjects.removeAll();

	myGeoObject = new ymaps.Placemark([lon, lat], {}, {
        preset: 'islands#dotIcon',
        iconColor: '#333'
    });

	window.myMap.geoObjects.add(myGeoObject);
	window.myMap.panTo([lon, lat], {delay: 150, flying: 1});
}