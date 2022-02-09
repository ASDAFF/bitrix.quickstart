$(function() {
/* переключение между разделами (begin) */
	$(".b-slider-menu__item").click(function() {
		if($(this).hasClass("active") == false) {
			var id = $(this).attr("href");
			
			if($(id).length) {
				$(".b-slider-menu__item").removeClass("active");
				$(this).addClass("active");
				
				$(".b-slider__section").removeClass("active");
				$(id).addClass("active")
			}
		}
		
		return false;
	});
/* переключение между разделами (end) */

/* анимация второго слайда: бытовая техника (begin) */
	var tech_main_w = $(".m-tech_1").width(),
		tech_text_3 = {
			left: parseInt($(".m-tech-text_3").css("left")),
			offsetLeft: parseInt($(".m-tech-text_1").css("left")),
		};
		tech_text_4 = {
			top: parseInt($(".m-tech-text_4").css("top")),
			offsetTop: parseInt($(".m-tech-text_2").css("top")),
		};
	$(".m-tech_1").hover(
		function() {
			$(this).stop().animate({width: (tech_main_w + 30)}, "fast");
		},
		function() {
			$(this).stop().animate({width: tech_main_w}, "fast");
			
			if($(this).hasClass("open")) {
				$(".m-tech-text_4").stop().animate({
					top: tech_text_4.top
				}, "fast", function() {
					$(".m-tech-text_3").stop().animate({
						left: tech_text_3.left
					}, "fast", function() {
						$(".m-tech-text_1, .m-tech-text_2").stop().fadeIn();
					});
				});
				
				$(this).removeClass("open");
			}
		}
	).click(function() {
		if($(this).hasClass("open") == false) {
			$(this).stop().animate({width: 980}, "fast");
			
			$(".m-tech-text_1, .m-tech-text_2").stop().fadeOut(function() {
				$(".m-tech-text_3").stop().animate({
					left: tech_text_3.offsetLeft
				}, "fast", function() {
					$(".m-tech-text_4").stop().animate({
						top: tech_text_4.offsetTop
					}, "fast");				
				});
			});
			
			$(this).addClass("open");
		}
		else {
			$(this).stop().animate({width: tech_main_w}, "fast");
		
			$(".m-tech-text_4").stop().animate({
				top: tech_text_4.top
			}, "fast", function() {
				$(".m-tech-text_3").stop().animate({
					left: tech_text_3.left
				}, "fast", function() {
					$(".m-tech-text_1, .m-tech-text_2").stop().fadeIn();
				});
			});
			
			$(this).removeClass("open");
		}
	});
/* анимация второго слайда: бытовая техника (end) */

/* анимация первого слайда: фото (begin) */
	var main_w = $(".m-photo_1").width(),
		img_3 = {
			top: parseInt($(".b-img_3").css("top")),
			left: parseInt($(".b-img_3").css("left")),
			offsetTop: parseInt($(".b-img_3").css("top")) - 20,
			offsetLeft: parseInt($(".b-img_3").css("left")) - 10
		},
		img_1 = {
			left: parseInt($(".b-img_1").css("left")),
			offsetLeft: parseInt($(".b-img_1").css("left")) - 10
		},
		img_5 = {
			top: parseInt($(".b-img_5").css("top")),
		};
		
	$(".m-photo_1").hover(
		function() {
			$(this).stop().animate({
				width: (main_w + 30)
			}, "fast");
			
			$(".b-img_3").stop().animate({
				top: img_3.offsetTop,
				left: img_3.offsetLeft
			}, "fast");
			
			$(".b-img_1").stop().animate({
				left: img_1.offsetLeft
			}, "fast");
		},
		function() {
			$(this).stop().animate({width: main_w}, "fast");
			
			$(".b-img_3").stop().animate({
				top: img_3.top,
				left: img_3.left,
				opacity: 1
			}, "fast");
			
			$(".b-img_1").stop().animate({
				left: img_1.left
			}, "fast");
			
			if($(this).hasClass("open")) {
				$(this).removeClass("open");
				
				$(".b-img_3").stop().animate({
					top: img_3.top,
					left: img_3.left,
					opacity: 1
				}, "normal");
				$(".b-img_4").stop().fadeOut();
				
				$(".b-text_1, .b-text_2, .b-img_2").stop().fadeIn("fast");
				
				$(".b-text_4, .b-text_5").stop().animate({
					left: -450
				}, "fast");
				
				$(".b-img_5").stop().animate({
					top: img_5.top
				}, "fast");
			}
		}
	)
	.click(function() {
		if($(this).hasClass("open") == false) {
			$(".b-text_1, .b-text_2, .b-img_2").stop().fadeOut("fast");
			
			$(this).stop().animate({
				width: 980
			}, "fast");
			
			$(".b-img_3").stop().animate({
				top: 20,
				left: 380,
				opacity: 0
			}, "normal");
			$(".b-img_4").stop().fadeIn();
			
			$(".b-img_1").animate({
				left: -230
			}, "fast");
			
			$(".b-text_4, .b-text_5").stop().animate({
				left: 40
			}, "fast");
			
			$(".b-img_5").stop().animate({
				top: 335
			}, "fast");
			
			$(this).addClass("open");
		}
		else {
			$(".b-text_1, .b-text_2, .b-img_2").stop().fadeIn("fast");
			
			$(this).stop().animate({
				width: main_w
			}, "fast");
			
			$(".b-img_3").stop().animate({
				top: img_3.offsetTop,
				left: img_3.offsetLeft,
				opacity: 1
			}, "normal");
			$(".b-img_4").stop().fadeOut();
			
			$(".b-img_1").stop().animate({
				left: img_1.offsetLeft
			}, "fast");
			
			$(".b-text_4, .b-text_5").stop().animate({
				left: -450
			}, "fast");
			
			$(".b-img_5").stop().animate({
				top: img_5.top
			}, "fast");
			
			$(this).removeClass("open");
		}
	});
/* анимация первого слайда: фото (end) */
});