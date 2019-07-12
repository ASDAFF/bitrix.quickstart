jQuery(function(){

//change about update script
$context = $("div.catalog-section.element");

//tooltip detail
$(".button_link.btn_yellow.all", $context).tooltip({"delay":{"show":50, "hide":150}});

//social
$(".share2", $context).sharesl();

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
		 $(this).parents(".left-block").find(".quickly-form").stop(true, true).slideDown(800);
	});

//close quickly form
	$(".quickly-form .button.cancel", $context).click(function(){
		$(this).parent().find("input").removeClass("warning").end()
		.stop(true, true).fadeOut(800, function(){
			if(!$(this).data("status"))
				$(this).find("input").resetPH();
		});		
	});

//send quickly form
	$(".quickly-form .button.send", $context).click(function(){
	
		var elem 		= $(this),
			modalWindow = elem.parent(),
			left_block 	= modalWindow.parents(".left-block"),
			inputPhone	= modalWindow.find("input[name=quickly-phone]"),
			phone		= inputPhone.val(),			
			siteID		= left_block.find(".color-item select").children().eq(0).attr("data-site");

		if(phone.length < 1){
			inputPhone.addClass("warning").focus(function(){
				$(this).removeClass("warning");
			});
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
					modalWindow.data("status", false).find("input").resetPH();
				});
			});
		}
	});

	
//close quickly answer	
	$(".quickly-send span", $context).click(function(){
		$(this).parent().stop(true, true).fadeOut(800)
	});

//close basket error msg	
	$(".basket-error span", $context).click(function(){
		$(this).parent().stop(true, true).fadeOut(800)
	});	

//send cheap form
	$(".cheap-form .button.send", $context).click(function(){
	
		var elem 			= $(this),
			modalWindow 	= elem.parent(),
			detail_content 	= modalWindow.parents(".detail-content"),
			inputPhone		= modalWindow.find("input[name=cheap-phone]"),
			inputUrl		= modalWindow.find("input[name=cheap-url]"),
			inputName   	= modalWindow.find("input[name=cheap-name]"),
			textarea    	= modalWindow.find("textarea"),
			phone			= inputPhone.val(),
			url				= inputUrl.val(),
			name			= inputName.val(),
			comment			= textarea.val(),
			siteID			= detail_content.find(".color-item select").children().eq(0).attr("data-site");

		if(phone.length < 1){
			inputPhone.addClass("warning").focus(function(){
				$(this).removeClass("warning");
			});		
			setTimeout(function(){
				inputPhone.removeClass("warning");
			}, 5000);
		}
		if(url.length < 1){
			inputUrl.addClass("warning").focus(function(){
				$(this).removeClass("warning");
			});		
			setTimeout(function(){
				inputUrl.removeClass("warning");
			}, 5000);		
		}		
		if(phone.length < 1 || url.length < 1)
			return;

		if(!modalWindow.data("status")){
			modalWindow.data("status", true);
			elem.addClass("load");
							
			$.post(elem.attr("data-path"), {			
						  "ID": elem.attr("data-id"),  
						"NAME": name, 
					   "PHONE": phone,
					     "URL": url,
					 "COMMENT": comment,
					 "SITE_ID": siteID,
			}, 
			function(){				
				modalWindow.data("status", false).find("input, textarea").resetPH();
				elem.removeClass("load").css({"color":"#C2C2C2", "cursor":"text"}).animate({"top":10, "opacity":0}, "normal", "linear", function(){
						elem.html(elem.attr("data-loader")).unbind("click").animate({"top":0, "opacity":1}, "normal", "linear");
					 });				
			});
		}
	});

//tab
$("ul.tabs-prop li").click(function(){

	var elems = $("ul.tab-content").children("li"),
		elem  = elems.eq($(this).index());
		
	if(!$(this).hasClass("active")){
		$("ul.tabs-prop li").removeClass("active");
		$(this).addClass("active");	
		
		elems.hide();
		elem.css({"opacity":0}).show();
		elem.stop(true, true).animate({"opacity":1}, "normal", "linear");	
	}	
});

if(location.hash.length > 1){

	var tabs = $("ul.tabs-prop li"),
		tab = tabs.eq(tabs.length - 1),
		contents = $("ul.tab-content").children("li"),
		content = contents.eq(contents.length - 1);
	
	tabs.removeClass("active");
	tab.addClass("active");	
	
	contents.hide();
	content.css({"opacity":0}).show();
	content.stop(true, true).animate({"opacity":1}, "normal", "linear");	
}
	
//placeholder
	$(".quickly-form input, .cheap-form input, .cheap-form textarea", $context).placeholdersl();
			
	$(".left-block .img img", $context).imagezoomsl({					
				zoomrange: [4, 6],
				zoomstart: 4,
				magnifiersize:[482, 316],
				rightoffset: 10,
				magnifierpos: 'right',
				switchsides: false,
				classstatusdiv: "imagezoomsl",
				classmagnifier: "magnifier-element",
				classtracker: "tracker-element",
				cursorshadeborder: "10px solid black",
				magnifiereffectanimate: "fadeIn",
	});
 
	$(".tmb img", $context).click(function(){
	   var that = $(this),
		   pic  =  $("#picture").children("img");
	
	   if(pic.attr("src") !== $(this).attr("src"))
		   pic.fadeOut(600, function(){
				 $(this).attr("src", $(that).attr("src"))
						.attr("data-large", $(that).attr("data-large"))					
						.attr("data-help", $(that).attr("data-help"))						
						.fadeIn(1000);				
		   });
   });
	
	$(".text", $context).jScrollPane({ mouseWheelSpeed:20, verticalDragMinHeight:56, verticalDragMaxHeight:56});
});