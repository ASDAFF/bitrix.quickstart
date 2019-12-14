$(document).ready(function(){

	$(document).on('click','.faq .filter .btn',function(){
		var filter = $(this).data('filter');
		$('.faq .filter .btn').addClass('btn-default').removeClass('btn-primary');
		$(this).addClass('btn-primary').removeClass('btn-default')
		$('.faq').find('.panel-group .item .panel-heading').find('a').addClass('collapsed');
		$('.faq').find('.panel-group .item .panel-body').parent().removeClass('in');
		if( filter=='' ) {
			$('.faq').find('.panel-group').find('.item').show();
		} else {
			$('.faq').find('.panel-group').find('.item').hide();
			$('.faq').find('.panel-group').find('.item.filter'+filter).show();
		}
	});

});