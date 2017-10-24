$(document).ready(function(){
	
	$('.lw_include_file_articmodal-open').click(function(){
		$('#'+$(this).attr('data-window-id')).arcticmodal();
		return false;
	});
	
	$('.lw_include_file_articmodal-close').click(function(){
		$('#'+$(this).attr('data-window-id')).arcticmodal('close');
		return false;
	});
	
});