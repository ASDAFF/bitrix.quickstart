jQuery(function(){
	$(".point-faq a").click(function(){
		$.scrollTo($("[name="+$(this).attr("href").slice(1)+"]").offset().top - 85, 800, {easing:"swing", axis:"y"});
	}); 
	
	$("[href=#top]").click(function(){
		$.scrollTo($("[name=top]").offset().top - 175, 800, {easing:"swing", axis:"y"});
	}); 	
});