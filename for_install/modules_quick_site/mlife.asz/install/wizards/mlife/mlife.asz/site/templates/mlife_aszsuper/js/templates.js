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
	
	if($(window).width()>980 && $('div').is(':not("#bx-panel")')) {
		$('.mainTopper').css({'position':'fixed','margin-top':'0px','z-index':'49'});
		$('.mlfShap').css({'padding-top':'35px'});
	}
	if($('div').is('#bx-panel')) {
		$('.mainTopper').css({'margin-top':'0px','position':'relative'});
		$('.mlfShap').css({'padding-top':'5px'});
	}
	
});