$(window).load(function() {

	if( !$('body').hasClass('mobile') ) {

		$('[name=age]').select2({
		    minimumResultsForSearch: -1
		});

		$('[name=region]').select2({
			width: 'resolve',
		});	

	}

	$('.phonemask').mask(wm.phone_mask);
    $(':checkbox').prettyCheckable({label: ' '});

    $('#btn-send').click(function() {
        var is_check = $('.terms input[name=terms]').attr('checked');
        return is_check;
    });

    $('#credit-form').validate({
        onKeyup: true,
        eachValidField: function() {
            $(this).closest('div').removeClass('err').addClass('success');
        },
        eachInvalidField: function() {
            $(this).closest('div').removeClass('success').addClass('err');
        }
    });

});

