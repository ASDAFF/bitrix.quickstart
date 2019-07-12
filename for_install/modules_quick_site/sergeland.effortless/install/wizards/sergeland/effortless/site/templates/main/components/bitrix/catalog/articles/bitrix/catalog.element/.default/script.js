/*----------- COMMENTS Form -----------*/
jQuery(function(){
	var form = $('form[name=COMMENTS]');

	form.submit(function() {
		$('#form-loading-comments').fadeIn();
		$('#error-comments, #success-comments, #beforesend-comments').hide();
		if(validate()){
			submission();
		} else{
			$('#form-loading-comments').hide();
			$('#beforesend-comments, #results-comments').fadeIn();
		};
		$('input, select, textarea, button', form).blur();
		return false;
	});

	function validate() {
		var errors = [];
		$('.req', form).each(function() {
			if(!$(this).val()){
				errors.push(1);
				$(this).addClass('error');
			} else $(this).removeClass('error');
		});  
		if(errors.length === 0)
			 return true;
		else return false;
	};

	function submission(){
		$.ajax({
				type: 'POST',
				url: form.attr('action'),
				dataType: 'json',
				data: form.serialize(),
				success: function(data){
					$('#form-loading-comments').hide();
					$('input, textarea', form).removeClass('error');
					if(data.MESSAGE.ERROR < 1){
						$('#results-comments, #success-comments').fadeIn();
						$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
					}else $('#results-comments, #error-comments').hide().fadeIn();
				},
				error: function(data){
					$('#form-loading-comments').hide();
					$('#results-comments, #error-comments').hide().fadeIn();
				}
		});
		return false;
	};
});