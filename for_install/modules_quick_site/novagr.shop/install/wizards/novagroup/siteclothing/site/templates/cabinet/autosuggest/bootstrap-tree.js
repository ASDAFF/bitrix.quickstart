$(document).ready(function() {
	$('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
	$('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find('span.collapse').attr('title', 'Collapse this branch').on('click', function (e) {
		var children = $(this).parent().parent('li.parent_li').find(' > ul > li');
		if (children.is(':visible')) { 
			children.hide('fast');
			$(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
		} else {
			children.show('fast');
			$(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
		}
		e.stopPropagation();
    });
    // открываем нужный раздел
    if ($("#open-section").length) {
    	
    	var openSection = $("#open-section");
    
    	var li_parents = openSection.parents("li.parent_li");
    	var parents = [];
    	li_parents.each(function(n, element){
    	 	parents[$(element).attr("data-level")] = ($(element));
    	 	
    	 });
    	
    	 for (var i in parents) {
    	 	
    	 	if (i>0) {
    	 		
    	 		parents[i].find('span.collapse:first').click();
    	 		
    	 	}
    	 }
    	 
    	 $("#open-section span.collapse:first").click();
    	
    }    
});
