$(document).ready(function(){

	$('.playlist li').on('click', function(){

			var href = $(this).find('.set-name').attr('data-href');
			var ids = href.split('/watch?v=');
			id = ids[1];
			$('#media-player').attr('src', ids[0]+'/embed/'+id);
		
	})

})