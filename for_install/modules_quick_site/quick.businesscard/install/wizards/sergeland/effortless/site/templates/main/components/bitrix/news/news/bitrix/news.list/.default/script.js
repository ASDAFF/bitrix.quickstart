jQuery(document).ready(function($) {
    $('select', '.shop-footer').change(function(){	
		if($(this).val())
			window.location.href = window.location.pathname + $(this).val();
	});		
});