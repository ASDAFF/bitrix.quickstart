/* Copyright (c) 2010 Webrover (Corporate site http://www.webrover.ru)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * 
 * @name wGallery
 * @type jQuery
 * @return false
 * @author Gorbushko Aleksandr (Corporate blog of Alexander  http://blog.webrover.ru/gorbushko/)
 */

$(document).ready(function(){

//additional properties for jQuery object

	//align element in the middle of the screen
	$.fn.alignCenter = function() {
		var marginLeft = Math.max(40, parseInt($(window).width()/2 - $(this).width()/2)) + 'px';	//get margin left
		var marginTop = Math.max(40, parseInt($(window).height()/2 - $(this).height()/2)) + 'px';	//get margin top
		return $(this).css({'margin-left':marginLeft, 'margin-top':marginTop});	//return updated element
	};
	//\align element in the middle of the screen
	
	$.fn.alignCenterAnimated = function(options) {
		var options = jQuery.extend({
			newWidth: "",
			newHeight: ""
		},options);
		var contWidth;
		options.newWidth < 300 ? contWidth = 300 : contWidth = options.newWidth;
		var contHeight = options.newHeight;
		//options.newHeight < 100 ? contHeight = 100 : contHeight = options.newHeight;
		return this.each(function() {
			var newWidth = contWidth + 24;
			var newHeight = contHeight + 77;
			var marginLeft = Math.max(40, parseInt($(window).width()/2 - newWidth/2));	//get margin left
			var marginTop = Math.max(40, parseInt($(window).height()/2 - newHeight/2));	//get margin top
			$(this)
				.animate({
					'margin-left':marginLeft,
					'margin-top':marginTop
			}, 300, function(){
				
				$(this).find(' .image-conteiner')
					.animate({
						'width':contWidth,
						'height':contHeight
					}, 300, function(){
					  $(this).find('.image').show();
					})
			});
			
		
		});
	};

	$.fn.appendGallery = function(options){
		var options = jQuery.extend({
			bNext: "/import/i/webrover-gallery-button-next.gif",
			bPrev: "/import/i/webrover-gallery-button-prev.gif",
			bClose: "/import/i/webrover-gallery-button-close.gif",
			wgTitle: "Галерея",
			srcPreloader: "/import/i/webrover-gallery-preloader.gif"
		},options);
		return this.each(function() {
			var strWGallery = '<div id="webrover-gallery-backing" class="hidden"></div><div id="webrover-gallery-popup" class="hidden"><a href="javascript:void(0)" class="close" title="Закрыть"><img src="' + options.bClose + '" alt="X" /></a><div class="round round-top round-border"><div class="round-left"><div class="round-right"><div class="round-repeat"></div></div></div></div><div class="backing"><div class="title">'+ options.wgTitle +'</div><div class="round round-image"><div class="round-top-left"></div><div class="round-top-right"></div><div class="round-bottom-left"></div><div class="round-bottom-right"></div><div class="image-conteiner"><a title="Вперед" class="next-image" href="#"><img class="image" alt="" src=""></a><img class="preloader" alt="" src="' + options.srcPreloader + '"></div></div><div class="navigation"><a title="Назад" class="prev-image" href="#"><img alt="" src="' + options.bPrev + '"></a><a title="Вперед" class="next-image" href="#"><img alt="" src="' + options.bNext + '"></a></div></div><div class="round round-bottom round-border"><div class="round-left"><div class="round-right"><div class="round-repeat"></div></div></div></div></div>';
			$('body').prepend(strWGallery);
		});
	};
	
	$.fn.changeImage = function(options){
		var options = jQuery.extend({
			srartImgSrc: ""
		},options);
		var srartImgSrc = options.srartImgSrc;
		$('#webrover-gallery-popup .image-conteiner .preloader').show();
		$('#webrover-gallery-popup .image-conteiner .image').hide();
		return this.each(function() {
			var prevSrc = nextSrc = stopFlag = false;
			$(this).find('a').each(function(){
				var curSrc = $(this).attr('href');
				if(stopFlag){
					nextSrc = curSrc;
					return false;
				}
				if(srartImgSrc == curSrc) stopFlag = true;
				if(!stopFlag) prevSrc = curSrc;  
			});
			if(!prevSrc) prevSrc = srartImgSrc;
			if(!nextSrc) nextSrc = srartImgSrc;
			
			$('#webrover-gallery-popup .prev-image').attr('href',prevSrc);
			$('#webrover-gallery-popup .next-image').attr('href',nextSrc);
			var detailImg = $('#webrover-gallery-popup .image-conteiner .image');
			detailImg.attr('src',srartImgSrc);
			if($.browser.msie){
				detailImgObj = new Image;
				detailImgObj.src = srartImgSrc;
				var imgHeight = detailImgObj.height
				var imgWidth = detailImgObj.width;
				if(imgWidth < 300) imgWidth = 300;
				$('#webrover-gallery-popup .image-conteiner .preloader').hide();
				$('#webrover-gallery-popup')
					.width(imgWidth + 24)
					.height(imgHeight + 77)
					.alignCenter()
					.find(' .round-image')
						.width(imgWidth)
						.height(imgHeight)
						.find(' .image-conteiner')
							.width(imgWidth)
							.height(imgHeight)
							    .find('.image').show();
				$('#webrover-gallery-popup .round-image')
					.width(imgWidth)
					.height(imgHeight);
			} else{
				
				$('#webrover-gallery-popup .image-conteiner .image').load(function() {
					$('#webrover-gallery-popup .image-conteiner .preloader').hide();
					$('#webrover-gallery-popup').alignCenterAnimated({
						newWidth: detailImg.width(),
						newHeight: detailImg.height()
					});
				
				});
			};
		});
	};

	//open or close pop-up
	$.fn.togglePopup = function(){
		var wGalObj = $(this);
		if($('#webrover-gallery-popup').hasClass('hidden')){	//hidden - then display
		  
			$('#webrover-gallery-popup .image-conteiner .image').hide();
			$('#webrover-gallery-popup .image-conteiner .preloader').show();
		  
			if($.browser.msie){	//when IE - fade immediately
				$('#webrover-gallery-backing')
					.height($(document).height())
					.toggleClass('hidden')
					.click(function(){$(this).togglePopup();});
				$('#webrover-gallery-popup')
					.alignCenter()
					.toggleClass('hidden');
			} else {	//in all the rest browsers - fade slowly
				$('#webrover-gallery-backing')
					.height($(document).height())
					.toggleClass('hidden')
					.fadeTo('fast', 0.7)
					.click(function(){$(this).togglePopup();});
				$('#webrover-gallery-popup')
					.alignCenter()
					.toggleClass('hidden')
					.fadeTo('slow', 1);
			};

			$('#webrover-gallery-popup .prev-image, #webrover-gallery-popup .next-image').click(function(){
				var imgSrc = $(this).attr('href');
				wGalObj.changeImage({
				  srartImgSrc: imgSrc
				});
				return false;
			});
			
			$('#webrover-gallery-popup .close').click(function(){
				$(this).togglePopup();
				return false;
			});
		} else {	//visible - then hide
			$('#webrover-gallery-backing').toggleClass('hidden').removeAttr('style').unbind('click');
			$('#webrover-gallery-popup .close, #webrover-gallery-popup .prev-image, #webrover-gallery-popup .next-image').unbind('click');
			$('#webrover-gallery-popup').toggleClass('hidden').removeAttr('style');
		};
		//alert($(this).attr('class'));
	};
	//\open or close pop-up
	
	$.fn.wGallery = function(options){
		var options = jQuery.extend({
			bNext: "/import/i/webrover-gallery-button-next.gif",
			bPrev: "/import/i/webrover-gallery-button-prev.gif",
			bClose: "/import/i/webrover-gallery-button-close.gif",
			wgTitle: "Галерея",
			srcPreloader: "/import/i/webrover-gallery-preloader.gif"
		},options);
		return this.each(function() {

			if($.browser.msie && $.browser.version < 7) return true;

			var wGalObj = $(this);

			wGalObj.appendGallery({
				bNext: options.bNext,
				bPrev: options.bPrev,
				bClose: options.bClose,
				wgTitle: options.wgTitle,
				srcPreloader: options.srcPreloader
			});
			
			
			wGalObj.find('a').click(function(){
				wGalObj.togglePopup();
				var imgSrc = $(this).attr('href');
				wGalObj.changeImage({
				  srartImgSrc: imgSrc
				});
				return false;
			});
		});
	};

//\additional properties for jQuery object

});
