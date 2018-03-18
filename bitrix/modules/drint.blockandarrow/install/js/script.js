$(document).ready(function(){

		if(link != '' && include_block == "Y" && $(link).length)
		{
			if(type == 0)
			{
				$(link).parent().css({"position":"relative"});
				//$(link).css({"position": "absolute"}).addClass("fixed");
				var topPos = $(link).offset().top;
				$(window).scroll(function() 
				{ 
					var top = $(document).scrollTop();
					if (top > topPos) $(link).addClass('fixed'); 
					else $(link).removeClass('fixed');
				});
			}
			else
			{
				$(link).css({"position": "fixed"}).removeClass("fixed");
				if(pos == 1)
							   $(link).css({"top": pos_xy+"px", "right": pos_yx+"px"});
					else if(pos == 2)
							  $(link).css({"bottom": pos_xy+"px", "left": pos_yx+"px"});
					else if(pos == 3)
							   $(link).css({"bottom": pos_xy+"px", "right": pos_yx+"px"});
					else if(pos == 4)
					{
						width = ($(window).width() - $(link).width())/2;
						height = ($(window).height() - $(link).height())/2;
						$(link).css({"top": height + "px", "left": width + "px"});
						$("body").css({"overflow-y":"hidden", "margin-right": "17px"});
						var textClose = document.createElement('div');
						textClose.className = "closeBlock";
						textClose.innerHTML = 'X';
						$(link).append(textClose).css({"overflow":"visible"});
						
						height_body = $(window).height();
						
						if(black == "Y")
						{
							$(link).wrap('<div class="black_block">');
							$(".black_block").css("height", height_body);
						}
						
						$(".closeBlock").click(function(){
							$("body").css({"overflow":"visible", "margin-right": "0px"});
							if(black == "Y")
								$(".black_block").hide();
							else
								$(link).hide();
						});
					}
					else
						$(link).css({"top": pos_xy+"px", "left": pos_yx+"px"});
			}
		}
		if(url_img != '' && include_up == "Y")
		{		
			$("body").append("<div class='up_scroll' href='#'><img src='"+url_img+"' alt='¬верх'></a>");
			scroll_block = $('.up_scroll');
			scroll_block.css("bottom", up_pos_xy+'px');
			
			if(up_pos == 0){
				scroll_block.css("left", up_pos_yx+'px');				
			} else if(up_pos == 1){
				scroll_block.css("right", up_pos_yx+'px');				
			}
			
			$(window).scroll(function(){
				if ($(this).scrollTop() > 100) {
					scroll_block.fadeIn();
				} else {
					scroll_block.fadeOut();
				} 
			}); 
			
			scroll_block.click(function(){
				$("html, body").animate({ scrollTop: 0 }, 500);
				return false;
			}); 
		}
});
