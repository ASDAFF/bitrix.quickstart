jQuery(function(){

	// menu
	$(".dropdown ul").parent("li").addClass("parent");
	$(".dropdown li:first-child").addClass("first");
	$(".dropdown li:last-child").addClass("last");
	$(".dropdown li:only-child").removeClass("last").addClass("only");	
	$(".dropdown .current-menu-item, .dropdown .current-menu-ancestor").prev().addClass("current-prev");

	$(".topmenu .dropdown li").mouseenter(function(){
			$(this).children("ul").stop(true, true).slideDown();
	}).mouseleave(function(){
			$(this).children("ul").stop(true, true).hide();
	});	
});