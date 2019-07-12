$(document).ready(function(){
	$(document).on('click','.catalogFilter a:not(".reset")',function(e){
		e.preventDefault();
			$('.catalogFilter').append('<div class="preload"><div class="load"></div></div>');
			$.ajax({
				 url: $(this).attr('href'),
				 data: {ajaxfilter: 1},
				 dataType : "html",
				 type: "POST",
				 success: function (data, textStatus) {
					setTimeout(function(){
						$('.catalogFilter').html(data);
						$('.catalogFilter').css({'height':$('.catalogFilter .filter').height()+'px'});
					},0);
				}
			});
	});
});