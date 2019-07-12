jQuery(function(){

//change about update script
$context = $("div.catalog-section");

//tooltip detail
$(".button_link.btn_yellow.all", $context).tooltip({"delay":{"show":50, "hide":150}});

//social
$(".share3", $context).sharesl();

//select color
	$(".color-item select", $context).sSelect({"ddMaxHeight":"100px"}).change(function(e){
	
		var selectColor 	= $(e.target),
			selectColorVal	= selectColor.val(),
			clickedOpt  	= false,	
			detail_content 	= selectColor.parent().parent().parent(),
			quickly_send 	= detail_content.find(".quickly-form .button.send"),
			selectSize   	= detail_content.find(".size-item select"),
			selectSizeVal   = selectSize.val(),
			selectSizeTek   = selectSize.find(":enabled").filter("[value="+selectSizeVal+"]");
						
			selectSize.children().attr("disabled", true).removeAttr("selected");
			selectSize.find("[data-color="+selectColorVal+"]").attr("disabled", false);
						
			if(!selectSize.hasClass("sku")){
				clickedOpt = selectSize.find(":enabled").filter("[data-size="+selectSizeTek.attr("data-size")+"]");
				clickedOpt.attr("selected", true);
			}			
			selectSize.resetSS();

			if(!clickedOpt)
				clickedOpt = selectSize.find('option:enabled').eq(0);

			detail_content.find(".button-make-goods a").attr("href", clickedOpt.attr("data-buy"));
			detail_content.find(".button-add-basket a").attr("href", clickedOpt.attr("data-add"));
				
			quickly_send.attr("data-size", clickedOpt.attr("data-size"));
			quickly_send.attr("data-color", clickedOpt.attr("data-color"));
			
			detail_content.find(".price-format span").not("div.old span").html(clickedOpt.attr("data-price"))
						  .end().filter("div.old span").html(clickedOpt.attr("data-old"));
			detail_content.find("span.artnumber").html(clickedOpt.attr("data-artnumber"));
			detail_content.find("span.amount").html(clickedOpt.attr("data-amount"));
	});	

//select size	
	$(".size-item select", $context).sSelect({"ddMaxHeight":"100px"}).change(function(e){
	
		var selectSize  	= $(e.target),
			selectSizeVal	= selectSize.val(),
			clickedOpt  	= selectSize.find(":enabled").filter("[value="+selectSizeVal+"]"),
			detail_content 	= selectSize.parent().parent().parent(),
			quickly_send 	= detail_content.find(".quickly-form .button.send");
			
			detail_content.find(".button-make-goods a").attr("href", clickedOpt.attr("data-buy"));
			detail_content.find(".button-add-basket a").attr("href", clickedOpt.attr("data-add"));

			quickly_send.attr("data-size", clickedOpt.attr("data-size"));
			quickly_send.attr("data-color", clickedOpt.attr("data-color"));
			
			detail_content.find(".price-format span").not("div.old span").html(clickedOpt.attr("data-price"))
						  .end().filter("div.old span").html(clickedOpt.attr("data-old"));
			detail_content.find("span.artnumber").html(clickedOpt.attr("data-artnumber"));
			detail_content.find("span.amount").html(clickedOpt.attr("data-amount"));
	});	
	
//click buy product
	$(".button-make-goods a", $context).click(function(){
		
		var url				= $(this).attr("href"),
			detail_content 	= $(this).parent().parent(),
			quantity		= parseInt(detail_content.find("input.quantity").val()) ? detail_content.find("input.quantity").val() : 1,
			quantityVar		= detail_content.find(".color-item select").children().eq(0).attr("data-quantity");
		
		$(this).attr("href", url+"&"+quantityVar+"="+quantity);	
	});
	
//click add product
	$(".button-add-basket a", $context).click(function(){
		
		var url				= $(this).attr("href"),
			path  			= $(this).attr("data-path"),
			detail_content 	= $(this).parent().parent(),
			loader			= detail_content.find(".loader"),
			quantity		= parseInt(detail_content.find("input.quantity").val()) ? detail_content.find("input.quantity").val() : 1,
			option			= detail_content.find(".color-item select").children().eq(0),
			quantityVar		= option.attr("data-quantity"),
			siteID			= option.attr("data-site");
		
		if(!detail_content.data("status")){
			detail_content.data("status", true);
			loader.show();
			
			$.get(url+"&"+quantityVar+"="+quantity, function(html){
				if(html.indexOf("<font class=\"errortext\">", 0) > 0){
					loader.hide();
					detail_content.data("status", false);
					detail_content.find(".basket-error").stop(true, true).slideDown(800).delay(1500).fadeOut(800);
				} 
				else			
					$(".cart_info_container").load(path, {"SITE_ID":siteID}, function(data, status){
							if(status == "success"){
								$(".cart_info_container .cart-logo").stop().animate({left:"130px"}, "normal", "swing", function(){
									loader.hide();
									$(this).stop().animate({left:"0px"}, "normal", "swing", function(){
										detail_content.data("status", false);
									});
								});	
								$(".cart_info_container .cart-logo").on("click", function(){
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
							}
					 });
			});	
		}
		return false;
	});

//open quickly form
	$(".quickly", $context).click(function(){
	
		 $(this).parent()
				.find(".quickly-form")
				.stop(true, true)
				.slideDown(800);
	});

//close quickly form
	$(".quickly-form .button.cancel", $context).click(function(){
		var modalWindow = $(this).parent();
		modalWindow.find("input").removeClass("warning");
		modalWindow.stop(true, true).fadeOut(800, function(){
			if(!modalWindow.data("status"))
				modalWindow.find("input").resetPH();
		});		
	});

//send quickly form
	$(".quickly-form .button.send", $context).click(function(){
	
		var elem 		= $(this),
			modalWindow = elem.parent(),
			left_block 	= modalWindow.parent(),
			inputPhone	= modalWindow.find("input[name=quickly-phone]"),
			phone		= inputPhone.val(),			
			siteID		= left_block.find(".color-item select").children().eq(0).attr("data-site");
		
		if(phone.length < 1){
			inputPhone.addClass("warning");
			return;
		}
		
		if(!modalWindow.data("status")){
			modalWindow.data("status", true);
			elem.addClass("load");
							
			$.post(elem.attr("data-path"), {
			
						  "ID": elem.attr("data-id"), 
					"QUANTITY": left_block.find(".quantity").val(), 
						"SIZE": elem.attr("data-size"), 
					   "COLOR": elem.attr("data-color"), 
						"NAME": modalWindow.find("input[name=quickly-name]").val(), 
					   "PHONE": phone,
					 "SITE_ID": siteID,
			}, 
			function(){					
				elem.removeClass("load");
				left_block.find(".quickly-send").stop(true, true).slideDown(800).delay(1500).fadeOut(800);
				
				modalWindow.stop(true, true).fadeOut(800, function(){				
					modalWindow.data("status", false);
					modalWindow.find("input").resetPH();
				});
			});
		}
	});

//focus input
	$(".quickly-form input", $context).focus(function(){
		$(this).removeClass("warning");
	});
	
//close quickly answer	
	$(".quickly-send span", $context).click(function(){
		$(this).parent().stop(true, true).fadeOut(800)
	});

//close basket error msg	
	$(".basket-error span", $context).click(function(){
		$(this).parent().stop(true, true).fadeOut(800)
	});	
	
//placeholder
	$(".quickly-form input", $context).placeholdersl();
	
//img:hover zoom
	$(".catalog-element-item .inner div.img", $context)
		.mouseenter(function(){
	
			$(this).children("img").stop().animate({"width":"200px"}, "normal", "swing");
			
			$(this).children("a.all").css({"opacity":0}).show().stop(true, true).animate({"right":"30px", "opacity":0.85}, {"queue":false, "duration":"normal", "easing":"swing"})			
				.mouseenter(function(){$(this).stop(true, true).css({"opacity":0.95});})
				.mouseleave(function(){$(this).stop(true, true).css({"opacity":0.85});});
			
			$(this).children("a.buy").css({"opacity":0}).show().stop(true, true).animate({"right":"39px", "opacity":0.85}, {"queue":false, "duration":"normal", "easing":"swing"})
				.mouseenter(function(){$(this).stop(true, true).css({"opacity":0.95});})
				.mouseleave(function(){$(this).stop(true, true).css({"opacity":0.85});});			
		})
		.mouseleave(function(){		
			$(this).children("img").stop().animate({width:"166px"}, "normal", "swing");
			$(this).children("a.all").stop().animate({"right":"65px", "opacity":0}, {"queue":false, "duration":"normal", "easing":"swing"});
			$(this).children("a.buy").stop().animate({"right":"0px", "opacity":0}, {"queue":false, "duration":"normal", "easing":"swing"});			
		});		
				
//open modal window
	$(".detail-item-block", $context).each(function(){
		$(this).data("left", parseInt($(this).css("left")));
	});	
		
	$(".catalog-element-item .button_link.buy", $context).click(function(){
	    
		$(".tracker").remove();		
		$(".detail-item-block", $context).stop(true, true).fadeOut(800);
		$(".quickly-send", $context).stop(true, true).hide();
		
		var content					= $(".content"),
			catalog_element_item 	= $(this).parents(".catalog-element-item"),
			container 				= catalog_element_item.children(".detail-item-block"),
			quickly_form			= container.find(".quickly-form"),
			contentCenter 			= content.offset().left + content.width()/2,
		    containerRight 			= $(this).offset().left - 143 + container.width(),
			left 					= container.data("left"),
			jScrollPane 			= container.find(".text").jScrollPane({ mouseWheelSpeed:20, verticalDragMinHeight:56, verticalDragMaxHeight:56});
		
		if(!quickly_form.data("status")){
			quickly_form.stop(true, true).hide();
			quickly_form.find("input").removeClass("warning");
			quickly_form.find("input").resetPH();
		}

		if(jScrollPane)
		   var jScrollPaneAPI = jScrollPane.data('jsp');
		
		// ver. 1.0
		left = left + contentCenter - containerRight + container.width()/2;
		
		// ver 2.0
		//if((catalog_element_item.offset().left + container.width()) < (content.offset().left + content.width()))
		//	 left = 0;
		//else left = -302;
		
		container.css({left:left}).stop(true, true).fadeIn(600, function(){
				
				if(jScrollPaneAPI){
					jScrollPaneAPI.reinitialise(); 
					jScrollPaneAPI.positionDragY(0);
				}	
		});
		
		// ver. 1.0
		$.scrollTo({top:container.offset().top - 105 - parseInt($(".header").css("top")), left:container.offset().left - 172}, 800, {easing:"swing"});
		
		// ver. 2.0
		//$.scrollTo({top:container.offset().top - 105, left:catalog_element_item.offset().left - container.width()/2}, 800, {easing:"swing"});
				
		$(container.find(".img img")).imagezoomsl({					
				zoomrange: [2, 6],
				zoomstart: 3.5,
				magnifiersize:[270, 320],
				rightoffset: 10,
				magnifierpos: 'right',
				switchsides: false,
				classstatusdiv:  "imagezoomsl",
				classmagnifier: "magnifier-item-element",
				magnifierborder: "none",
				cursorshadeborder: "10px solid black",
				magnifiereffectanimate: "fadeIn",
		});
		
		return false;
	});

//close detail page
	$(".detail-item-block .close", $context).click(function(){
		$(".tracker").remove();
		$(this).parent().stop(true, true).fadeOut(800);
	});	
});