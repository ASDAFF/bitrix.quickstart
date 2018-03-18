(function($) {
	$.fn.aprofSimpleSliderDefault = function(options) {
		return this.each(function() {
			var image_cnt = 0;
			var slider = $(this);
			var slide = $(this).find("li");
			var slide_width = $(slide).width();
			var slide_cnt = $(slide).size();
			var slider_width = $(this).width();
			var m = parseInt($(slide).css("margin-right"))+parseInt($(slide).css("margin-left"))+parseInt($(slide).css("padding-right"))+parseInt($(slide).css("padding-left"));
			var cnt = parseInt((slider_width+15)/(slide_width+15));
			var new_width = (slide_width+m)*cnt-m;
			var dw = $(this).parent().width();
			$(this).css("width",new_width+"px");
			$(this).find(".aprof-simple-slider-wraper").css("width",new_width+"px");
			slider_width = new_width;
			
			var popupBlock = $(options.popupBlock);
			
			$(this).find(".aprof-simple-slider-larr").click(function(){
				if($(this).attr("disabled")=="disabled") return false;
				$(this).attr("disabled","disabled");
				var l = $(slider).find("ul").position().left;
				if(l<0){
					$(slider).find("ul").animate({
						left:"+="+(slide_width+m)+"px"
					},500,function(){
						$(slider).find(".aprof-simple-slider-larr").removeAttr("disabled");
						$(slider).find(".aprof-simple-slider-rarr").removeAttr("disabled");
					});
				}
				else{
					
					$(slider).find("ul").animate({
						left:"-"+(slide_cnt-cnt)*(slide_width+m)+"px"
					},500,function(){
						$(slider).find(".aprof-simple-slider-larr").removeAttr("disabled");
						$(slider).find(".aprof-simple-slider-rarr").removeAttr("disabled");
					});
				}
			});
			$(this).find(".aprof-simple-slider-rarr").click(function(){
				if($(this).attr("disabled")=="disabled") return false;
				$(this).attr("disabled","disabled");
				var l = $(slider).find("ul").position().left;
				var image_id = parseInt((l*(-1)+slider_width)/slide_width);
				if(image_id<slide_cnt){
					$(slider).find("ul").animate({
						left:"-="+(slide_width+m)+"px"
					},500,function(){
						$(slider).find(".aprof-simple-slider-larr").removeAttr("disabled");
						$(slider).find(".aprof-simple-slider-rarr").removeAttr("disabled");
					});
				}
				else{
					$(slider).find("ul").animate({
						left:"0px"
					},500,function(){
						$(slider).find(".aprof-simple-slider-larr").removeAttr("disabled");
						$(slider).find(".aprof-simple-slider-rarr").removeAttr("disabled");
					});
				}
			});
			
			$(this).find("li a").click(function(){
				image_cnt = $(slider).find("li a").index($(this));
				var src = $(this).attr("data-src");
				var html = '<img src="'+src+'" style="display:block;position:absolute;left:-200000px;" class="aprof_slider_image_big" /><a href="javascript:void(0);" class="aprof-slider-image-rarr"></a><a href="javascript:void(0);" class="aprof-slider-image-larr"></a><a class="aprof-popup-close" href="javascript:void(0);"></a>';
				$(popupBlock).find(".aprof-popup-block-content").html(html);
				$(popupBlock).show();
				$(slider).css({opacity:0});
				var w = $(this).attr("data-width");
				var l = dw/2-w/2;
				$(popupBlock).find(".aprof-popup-block-content").append('<div id="aprof-slider-loader"></div>');
				$(popupBlock).css({
					left:l+"px",
					width:dw-l+"px"
				});
				$(popupBlock).find(".aprof_slider_image_big").bind("load",function(){
					$(this).removeAttr("style");
					$(popupBlock).find(".aprof-slider-loader").hide();
					$(popupBlock).find(".aprof-slider-loader").remove();
				});
			});
			$(popupBlock).find(".aprof-popup-close").live("click",function(){
				$(popupBlock).hide();
				$(slider).css("opacity",1);
			});
			
			$(popupBlock).on('click', '.aprof-slider-image-rarr', function(){
				image_cnt++;
				if(image_cnt==slide_cnt){
					image_cnt = 0;
				}
				
				var image = $(slider).find("ul a").eq(image_cnt);
				var src= $(image).attr("data-src");
				if($(image).attr("ready")!="ready"){
					$(image).attr("ready","ready");
					$(popupBlock).find(".aprof_slider_image_big").css({
						"position":"absolute",
						"left":"-1000000px"
					});
					$(popupBlock).find(".aprof-popup-block-content").append('<div class="aprof-slider-loader"></div>');
				}
				$(popupBlock).find(".aprof_slider_image_big").attr('src',src);
				var w = $(image).attr("data-width");
				var l = dw/2-w/2;
				$(aprof_slider_image_big).css({
					left:l+"px",
					width:dw-l+"px"
				});
				$(popupBlock).find(".aprof_slider_image_big").bind("load",function(){
					$(this).removeAttr("style");
					$(".aprof-slider-loader").hide();
					$(".aprof-slider-loader").remove();
				});
			});
			$(popupBlock).on('click', ".aprof-slider-image-larr", function(){
				image_cnt--;
				if(image_cnt<0){
					image_cnt = slide_cnt-1;
				}
				
				var image = $(slider).find("ul a").eq(image_cnt);
				var src= $(image).attr("data-src");
				if($(image).attr("ready")!="ready"){
					$(image).attr("ready","ready");
					$(popupBlock).find(".aprof_slider_image_big").css({
						"position":"absolute",
						"left":"-1000000px"
					});
					$(popupBlock).find(".aprof-popup-block-content").append('<div class="aprof-slider-loader"></div>');
				}
				$(popupBlock).find(".aprof_slider_image_big").attr('src',src);
				var w = $(image).attr("data-width");
				var l = dw/2-w/2;
				$(popupBlock).css({
					left:l+"px",
					width:dw-l+"px"
				});
				$(popupBlock).find(".aprof_slider_image_big").bind("load",function(){
					$(this).removeAttr("style");
					$(".aprof-slider-loader").hide();
					$(".aprof-slider-loader").remove();
				});
			});
		});
	};
})(jQuery);