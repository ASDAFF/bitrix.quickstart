var subscribe = function()
{
	if( !$('.js-subscribe').length ){
		return false;
	}

	var errors;
	$('.js-subscribe').submit(function(event){
		event.preventDefault();
		var $form = $(this);
		$form.parent().find('.js-subscribe-notification').remove();
		var loading = new site.ui.loading('body');
		$.post(
			'/ajax/form/subscribe/',
			$form.serializeArray(),
			function(response){
				errors = $(response).find('.js-subscribe-errors').length;

				if( errors ){
					$form.after($(response).find('.js-subscribe-errors').removeClass('hidden'));
				}
				else{
					$form.after($(response).find('.js-subscribe-success').removeClass('hidden'));
				}

				loading.hide();
				$('body').animate({scrollTop: $form.offset().top - 50});
			},
			'html'
		);
	});
}

$(document).ready(function()
{
	subscribe();
});