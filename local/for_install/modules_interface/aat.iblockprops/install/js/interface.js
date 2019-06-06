$(function(){

	$('#bx-admin-prefix').on('click', '.aat-iblockprops-list label.parent', function(){
			var li = $(this).parent('li'),
				child = li.children('ul');
			child.slideToggle();
		});
	
	$('#bx-admin-prefix').on('click', '.aat-iblockprops-list + .reset', function(){
			var list = $(this).prev('.aat-iblockprops-list');
			$('input[type=checkbox], input[type=radio]', list).removeAttr('checked');    
		});	
        
	$('#bx-admin-prefix').find('.aat-iblockprops-list').find('input[type=checkbox], input[type=radio]').each(function(){
			if ($(this).is(':checked')) {
				$(this).parents('ul').show().siblings('ul').show();
			}
		});
    
});