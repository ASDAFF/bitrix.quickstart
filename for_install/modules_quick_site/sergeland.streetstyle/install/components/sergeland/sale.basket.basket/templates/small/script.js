jQuery(function(){	

    $('.cart_info_container .cart-logo').click(function(){
	
			if($(this).hasClass("closed")){

					$(".cart_info_container .cart").slideToggle(300, function(){
					
						$(".cart_info_container").css("left", 5); 
						$(".cart_info_container .cart-logo").removeClass("closed").css("left", 0);
					});	
			
			}
			else{
					$(this).addClass("closed").css("left", 203);
					$(".cart_info_container").css("left", 10);
					$(".cart_info_container .cart").css("opacity", 0).slideToggle(300, function(){

						var jScrollPaneAPI = false,
							jScrollPane = $(".cart_info_container .cart").jScrollPane({ mouseWheelSpeed:30, showArrows:true, verticalDragMinHeight:130, verticalDragMaxHeight:130, contentWidth:175 });
						
						if(jScrollPane)
							jScrollPaneAPI = jScrollPane.data("jsp");
					
						if(jScrollPaneAPI){
						
							jScrollPaneAPI.reinitialise(); 
							jScrollPaneAPI.positionDragY(0);
							
							if(!jScrollPaneAPI.getIsScrollableV())
								$(".cart_info_container .jspContainer").height(jScrollPaneAPI.getContentHeight());				
						}
						
					}).fadeTo(300, 1);			
			}

	});

});