$(document).ready(function(){

	$(document).on('click','.vacancies .filter .btn',function(){
		var filter = $(this).data('filter');
		$('.vacancies .filter .btn').addClass('btn-default').removeClass('btn-primary');
		$(this).addClass('btn-primary').removeClass('btn-default')
		$('.vacancies').find('.panel-group .item .panel-heading').find('a').addClass('collapsed');
		$('.vacancies').find('.panel-group .item .panel-body').parent().removeClass('in');
		if( filter=='' ) {
			$('.vacancies').find('.panel-group').find('.item').show();
		} else {
			$('.vacancies').find('.panel-group').find('.item').hide();
			$('.vacancies').find('.panel-group').find('.item.filter'+filter).show();
		}
	});

	if( $('.vacanciesForm').find('input[name="RS_EXT_FIELD_0"]').length>0 ) {
		$(document).on('click','.btn-respond',function(){
			var vacName = $(this).data('vacancy');
			$('.vacanciesForm').show().find('input[name="RS_EXT_FIELD_0"]').val( vacName );
		});
	}

});