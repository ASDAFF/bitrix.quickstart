$(document).ready(function(){
	if($(".emarket-catalog-menu").is(":hidden")) { 
		var timeout_id;
		
		$(".header .catalog-link").on("mouseenter", function(){
			var parent = $(this);
			timeout_id = setTimeout(function(){
				parent.children('.emarket-catalog-menu').stop(true, true);
				parent.children('.emarket-catalog-menu').slideDown(300);
			} , 300);
		})
		$(".header .catalog-link").on("mouseleave", function(){
			if(timeout_id)
				clearTimeout(timeout_id);
			
			$(this).children('.emarket-catalog-menu').slideUp(200);
		})
	}
})