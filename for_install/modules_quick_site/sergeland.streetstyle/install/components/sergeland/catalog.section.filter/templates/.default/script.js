jQuery(function(){

//scroll filter
	$(".filter").jScrollPane({ mouseWheelSpeed:30, showArrows:true, verticalDragMinHeight:54, verticalDragMaxHeight:54, contentWidth:225 }); 

// slider
	$(".filter-container .slider").slider({
			min: 0,
			max: 20000,
			values: [0, 20000],
			animate: "normal",
			range: true,
			create: function(event, ui){			
				var that = $(event.target);				
				$(this).find(".ui-slider-handle").each(function(){				
					var elem = $("<div />").appendTo($(this));					
					if($(this).data("uiSliderHandleIndex") == 0){
						elem.addClass("left");
						elem.html(that.slider("values",0));
					}						
					else{
						elem.addClass("right");
						elem.html(that.slider("values",1));
					}						
				});
			},
			slide: function(event, ui){			
				$(this).find(".ui-slider-handle").each(function(){									
					if($(this).data("uiSliderHandleIndex") == 0)
						 $(this).children().html(ui.values[0]);					
					else $(this).children().html(ui.values[1]);						
				});				
			}
	});	
});