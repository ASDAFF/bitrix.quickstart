jQuery(function(){

	$(window).load(function() {
		$("body").removeClass("no-trans");
	});
	// Enable Smooth Scroll only on Chrome and only on Win and Linux Systems
	var platform = navigator.platform.toLowerCase();
	if ((platform.indexOf('win') == 0 || platform.indexOf('linux') == 0) && !Modernizr.touch) {
		if ($.browser.webkit) {
			$.webkitSmoothScroll();
			console.log("hello webkit");
		}
	};

	//Owl carousel
	//-----------------------------------------------
	if ($('.owl-carousel').length>0) {
		
		// 1 items
		$(".owl-carousel.content-slider").owlCarousel({
			singleItem: true,
			autoPlay: 5000,
			navigation: false,
			navigationText: false,
			pagination: false
		});
		
		$(".owl-carousel.content-slider-with-controls").owlCarousel({
			singleItem: true,
			autoPlay: false,
			navigation: true,
			navigationText: false,
			pagination: true
		});
		
		$(".owl-carousel.content-slider-with-controls-autoplay").owlCarousel({
			singleItem: true,
			autoPlay: 5000,
			navigation: true,
			navigationText: false,
			pagination: true
		});

		$(".owl-carousel.content-slider-with-controls-bottom").owlCarousel({
			singleItem: true,
			autoPlay: false,
			navigation: false,
			navigationText: false,
			pagination: true
		});
		
		$(".owl-carousel.content-slider-with-controls-bottom-autoplay").owlCarousel({
			singleItem: true,
			autoPlay: 5000,
			navigation: false,
			navigationText: false,
			pagination: true
		});

		$(".owl-carousel.carousel-items-1").owlCarousel({
			singleItem: true,
			autoPlay: false,
			navigation: true,
			navigationText: false,
			pagination: false
		});	
		
		$(".owl-carousel.carousel-autoplay-items-1").owlCarousel({
			singleItem: true,
			autoPlay: 5000,
			navigation: true,
			navigationText: false,
			pagination: false
		});

		
		// 3 items
		$(".owl-carousel.carousel-items-3").owlCarousel({
			items : 3,
			itemsDesktop: [1000,3],
			itemsDesktopSmall: [900,2],
			itemsTablet: [600,2],
			pagination: false,
			navigation: true,
			navigationText: false
		});	
		
		$(".owl-carousel.carousel-autoplay-items-3").owlCarousel({
			items: 3,
			itemsDesktop: [1000,3],
			itemsDesktopSmall: [900,2],
			itemsTablet: [600,2],
			autoPlay: 5000,
			pagination: false,
			navigation: true,
			navigationText: false
		});

		$(".owl-carousel.content-slider-with-controls-bottom-items-3").owlCarousel({
			items : 3,
			itemsDesktop: [1000,3],
			itemsDesktopSmall: [900,2],
			itemsTablet: [600,2],
			pagination: true,
			navigation: false,
			navigationText: false
		});			

		$(".owl-carousel.content-slider-with-controls-bottom-autoplay-items-3").owlCarousel({
			items : 3,
			itemsDesktop: [1000,3],
			itemsDesktopSmall: [900,2],
			itemsTablet: [600,2],
			autoPlay: 5000,
			pagination: true,
			navigation: false,
			navigationText: false
		});

		
		// 4 items
		$(".owl-carousel.carousel").owlCarousel({
			items: 4,
			itemsTablet: [1000,2],
			pagination: false,
			navigation: true,
			navigationText: false
		});

		$(".owl-carousel.carousel-autoplay").owlCarousel({
			items: 4,
			itemsTablet: [1000,2],
			autoPlay: 5000,
			pagination: false,
			navigation: true,
			navigationText: false
		});
	};

	// Animations
	//-----------------------------------------------
	if (($("[data-animation-effect]").length>0) && !Modernizr.touch) {
		$("[data-animation-effect]").each(function() {
			var item = $(this),
			animationEffect = item.attr("data-animation-effect");

			if(Modernizr.mq('only all and (min-width: 768px)') && Modernizr.csstransitions) {
				item.appear(function() {
					if(item.attr("data-effect-delay")) item.css("effect-delay", 0 + "ms");
					setTimeout(function() {
						item.addClass('animated object-visible ' + animationEffect);

					}, item.attr("data-effect-delay"));
				}, {accX: 0, accY: -130});
			} else {
				item.addClass('object-visible');
			}
		});
	};

	// Text Rotators
	//-----------------------------------------------
	if ($(".text-rotator").length>0) {
		$(".text-rotator").each(function() {
			var tr_animationEffect = $(this).attr("data-rotator-animation-effect");
			$(this).Morphext({
				animation: ""+tr_animationEffect+"", // Overrides default "bounceIn"
				separator: ",", // Overrides default ","
				speed: 5000 // Overrides default 2000
			});
		});
	};

	// Stats Count To
	//-----------------------------------------------
	if ($(".stats [data-to]").length>0) {
		$(".stats [data-to]").each(function() {
			var stat_item = $(this),
			offset = stat_item.offset().top;
			if($(window).scrollTop() > (offset - 800) && !(stat_item.hasClass('counting'))) {
				stat_item.addClass('counting');
				stat_item.countTo();
			};
			$(window).scroll(function() {
				if($(window).scrollTop() > (offset - 800) && !(stat_item.hasClass('counting'))) {
					stat_item.addClass('counting');
					stat_item.countTo();
				}
			});
		});
	};

	//hc-tabs
	//-----------------------------------------------
	if ($('.hc-tabs').length>0) {
		$(window).load(function() {
			var currentTab = $(".hc-tabs .nav.nav-tabs li.active a").attr("href"),
			tabsImageAnimation = $(".hc-tabs-top").find("[data-tab='" + currentTab + "']").attr("data-tab-animation-effect");
			$(".hc-tabs-top").find("[data-tab='" + currentTab + "']").addClass("current-img show " + tabsImageAnimation + " animated");
			
			$('.hc-tabs .nav.nav-tabs li a').on('click', function(event) {
				var currentTab = $(this).attr("href"),
				tabsImageAnimation = $(".hc-tabs-top").find("[data-tab='" + currentTab + "']").attr("data-tab-animation-effect");
				$(".current-img").removeClass("current-img show " + tabsImageAnimation + " animated");
				$(".hc-tabs-top").find("[data-tab='" + currentTab + "']").addClass("current-img show " + tabsImageAnimation + " animated");
			});
		});

	}

	// Animated Progress Bars
	//-----------------------------------------------
	if ($("[data-animate-width]").length>0) {
		$("[data-animate-width]").each(function() {
			$(this).appear(function() {
				$(this).animate({
					width: $(this).attr("data-animate-width")
				}, 800 );
			}, {accX: 0, accY: -100});
		});
	};

	// Animated Progress Bars
	//-----------------------------------------------
	if ($(".knob").length>0) {
		$(".knob").knob();
	}

	// Charts
	//-----------------------------------------------
	if ($(".graph").length>0) {
		// Creates random numbers you don't need this for real graphs
		var randomScalingFactor = function(){ return Math.round(Math.random()*500)};

		if ($(".graph.line").length>0) {
			// Data for line charts
			var lineChartData = {
				labels : ["January","February","March","April","May","June","July"],
				datasets : [
				{
					label: "First dataset",
					fillColor : "rgba(188,188,188,0.2)",
					strokeColor : "rgba(188,188,188,1)",
					pointColor : "rgba(188,188,188,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(188,188,188,1)",
					data : [250,300,250,200,250,300,250]
				},
				{
					label: "Second dataset",
					fillColor : "rgba(126,187,205,0.2)",
					strokeColor : "rgba(126,187,205,1)",
					pointColor : "rgba(126,187,205,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(126,187,205,1)",
					data : [300,250,200,250,300,250,200]
				},
				{
					label: "Third dataset",
					fillColor : "rgba(98,187,205,0.2)",
					strokeColor : "rgba(98,187,205,1)",
					pointColor : "rgba(98,187,205,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(98,187,205,1)",
					data : [0,100,200,300,400,500,400]
				}
				]
			}

			// Line Charts Initialization
			$(window).load(function() {
				var ctx = document.getElementById("lines-graph").getContext("2d");
				window.newLine = new Chart(ctx).Line(lineChartData, {
					responsive: true,
					bezierCurve : false
				});
			});
		}
		if ($(".graph.bar").length>0) {
			// Data for bar charts
			var barChartData = {
				labels : ["January","February","March","April","May","June","July"],
				datasets : [
					{
						fillColor : "rgba(188,188,188,0.5)",
						strokeColor : "rgba(188,188,188,0.8)",
						highlightFill: "rgba(188,188,188,0.75)",
						highlightStroke: "rgba(188,188,188,1)",
						data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
					},
					{
						fillColor : "rgba(168,187,205,0.5)",
						strokeColor : "rgba(168,187,205,0.8)",
						highlightFill : "rgba(168,187,205,0.75)",
						highlightStroke : "rgba(168,187,205,1)",
						data : [randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor(),randomScalingFactor()]
					}
				]
			}
			
			// Bar Charts Initialization		
			$(window).load(function() {
				var ctx = document.getElementById("bars-graph").getContext("2d");
				window.myBar = new Chart(ctx).Bar(barChartData, {
					responsive : true
				});
			});
		}
		if ($(".graph.pie").length>0) {			
			// Data for pie chart
			var pieData = [
				{
					value: 120,
					color:"#09afdf",
					highlight: "#6BD5F4",
					label: "Blue"
				},
				{
					value: 120,
					color: "#FDB45C",
					highlight: "#FFC870",
					label: "Yellow"
				},
				{
					value: 120,
					color: "#4D5360",
					highlight: "#616774",
					label: "Dark Grey"
				}
			];

			// Pie Chart Initialization
			$(window).load(function() {
				var ctx = document.getElementById("pie-graph").getContext("2d");
				window.myPie = new Chart(ctx).Pie(pieData);
			});
		}
		if ($(".graph.doughnut").length>0) {	
			// Data for doughnut chart
			var doughnutData = [
				{
					value: 120,
					color:"#09afdf",
					highlight: "#6BD5F4",
					label: "Blue"
				},
				{
					value: 120,
					color: "#FDB45C",
					highlight: "#FFC870",
					label: "Yellow"
				},
				{
					value: 120,
					color: "#4D5360",
					highlight: "#616774",
					label: "Dark Grey"
				}
			];
			
			// Doughnut Chart Initialization
			$(window).load(function() {
				var ctx = document.getElementById("doughnut-graph").getContext("2d");
				window.myDoughnut = new Chart(ctx).Doughnut(doughnutData, {responsive : true});
			});
		}
	};

	// Magnific popup
	//-----------------------------------------------
	if (($(".popup-img").length > 0) || ($(".popup-iframe").length > 0) || ($(".popup-img-single").length > 0)) { 		
		$(".popup-img").magnificPopup({
			type:"image",
			gallery: {
				enabled: true,
			}
		});
		$(".popup-img-single").magnificPopup({
			type:"image",
			gallery: {
				enabled: false,
			}
		});
		$('.popup-iframe').magnificPopup({
			disableOn: 700,
			type: 'iframe',
			preloader: false,
			fixedContentPos: false
		});
	};		
	
	// Sharrre plugin
	//-----------------------------------------------
	if ($('#share').length>0) {
		$('#share').sharrre({
			share: {
				twitter: true,
				facebook: true,
				googlePlus: true
			},
			template: '<ul class="social-links clearfix"><li class="facebook"><a href="#"><i class="fa fa-facebook"></i></a></li><li class="twitter"><a href="#"><i class="fa fa-twitter"></i></a></li><li class="googleplus"><a href="#"><i class="fa fa-google-plus"></i></a></li></ul>',
			enableHover: false,
			enableTracking: true,
			render: function(api, options){
				$(api.element).on('click', '.twitter a', function() {
					api.openPopup('twitter');
				});
				$(api.element).on('click', '.facebook a', function() {
					api.openPopup('facebook');
				});
				$(api.element).on('click', '.googleplus a', function() {
					api.openPopup('googlePlus');
				});
			}
		});
	};

	// Affix plugin
	//-----------------------------------------------
	if ($("#affix").length>0) {
		$(window).load(function() {

			var affixBottom = $(".footer").outerHeight(true) + $(".subfooter").outerHeight(true) + $(".blogpost footer").outerHeight(true),
			affixTop = $("#affix").offset().top;
			
			if ($(".comments").length>0) {
				affixBottom = affixBottom + $(".comments").outerHeight(true);
			}

			if ($(".comments-form").length>0) {
				affixBottom = affixBottom + $(".comments-form").outerHeight(true);
			}

			if ($(".footer-top").length>0) {
				affixBottom = affixBottom + $(".footer-top").outerHeight(true);
			}

			if ($(".header.float").length>0) {
				$("#affix").affix({
					offset: {
					  top: affixTop-150,
					  bottom: affixBottom+100
					}
				});
			} else {
				$("#affix").affix({
					offset: {
					  top: affixTop-35,
					  bottom: affixBottom+100
					}
				});
			}

		});
	}
	if ($(".affix-menu").length>0) {
		setTimeout(function () {
			var $sideBar = $('.sidebar')

			$sideBar.affix({
				offset: {
					top: function () {
						var offsetTop      = $sideBar.offset().top
						return (this.top = offsetTop - 65)
					},
					bottom: function () {
						var affixBottom = $(".footer").outerHeight(true) + $(".subfooter").outerHeight(true)
						if ($(".footer-top").length>0) {
							affixBottom = affixBottom + $(".footer-top").outerHeight(true)
						}						
						return (this.bottom = affixBottom+50)
					}
				}
			})
		}, 100)
	}

	//Smooth Scroll
	//-----------------------------------------------
	if ($(".smooth-scroll").length>0) {
		if($(".header.float").length>0) {
			$('.smooth-scroll a[href*=#]:not([href=#]), a[href*=#]:not([href=#]).smooth-scroll').click(function() {
				if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
					if (target.length) {
						$('html,body').animate({
							scrollTop: target.offset().top-65
						}, 1000);
						return false;
					}
				}
			});
		} else {
			$('.smooth-scroll a[href*=#]:not([href=#]), a[href*=#]:not([href=#]).smooth-scroll').click(function() {
				if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
					if (target.length) {
						$('html,body').animate({
							scrollTop: target.offset().top
						}, 1000);
						return false;
					}
				}
			});
		}
	}

	//Scroll Spy
	//-----------------------------------------------
	if($(".scrollspy").length>0) {
		$("body").addClass("scroll-spy");
		if($(".float.header").length>0) {
			$('body').scrollspy({ 
				target: '.scrollspy',
				offset: 85
			});
		} else {
			$('body').scrollspy({ 
				target: '.scrollspy',
				offset: 20
			});
		}
	}

	//Video Background
	//-----------------------------------------------		
	if($(".video-background").length>0) {
		if (Modernizr.touch) {
			$(".video-background").vide({
				mp4: "videos/background-video.mp4",
				webm: "videos/background-video.webm",
				poster: "videos/video-fallback.jpg"
			}, {
				volume: 1,
				playbackRate: 1,
				muted: true,
				loop: true,
				autoplay: true,
				position: "50% 60%", // Similar to the CSS `background-position` property.
				posterType: "jpg", // Poster image type. "detect" × auto-detection; "none" × no poster; "jpg", "png", "gif",... - extensions.
				resizing: true 
			});
		} else {
			$(".video-background").vide({
				mp4: "videos/background-video.mp4",
				webm: "videos/background-video.webm",
				poster: "videos/video-poster.jpg"
			}, {
				volume: 1,
				playbackRate: 1,
				muted: true,
				loop: true,
				autoplay: true,
				position: "50% 60%", // Similar to the CSS `background-position` property.
				posterType: "jpg", // Poster image type. "detect" × auto-detection; "none" × no poster; "jpg", "png", "gif",... - extensions.
				resizing: true 
			});
		};

	};

	//Scroll totop
	//-----------------------------------------------
	$(window).scroll(function() {
		if($(this).scrollTop() != 0) {
			$(".scrollToTop").fadeIn();	
		} else {
			$(".scrollToTop").fadeOut();
		}
	});
	
	$(".scrollToTop").click(function() {
		$("body,html").animate({scrollTop:0},800);
	});
	
	//Modal
	//-----------------------------------------------
	if($(".modal").length>0) {
		$(".modal").each(function() {
			$(".modal").prependTo( "body" );
		});
	}
	
	// Pricing tables popovers
	//-----------------------------------------------
	if ($(".pricing-tables").length>0) {
		$(".plan .pt-popover").popover({
			trigger: 'hover'
		});
	};

	// Parallax section
	//-----------------------------------------------
	if (($(".parallax").length>0)  && !Modernizr.touch ){
		$(".parallax").parallax("50%", 0.2, false);
	};

	if (($(".parallax-2").length>0)  && !Modernizr.touch ){
		$(".parallax-2").parallax("50%", 0.2, false);
	};
	if (($(".parallax-text").length>0)  && !Modernizr.touch ){
		$(window).scroll(function() {
			//Get the scoll position of the page
			scrollPos = $(this).scrollTop();

			//Scroll and fade out the banner text
			$('.parallax-text').css({
				'opacity' : 1-(scrollPos/400)
			});
		});
	};

	// Remove Button
	//-----------------------------------------------
	$(".btn-remove").click(function() {
		$(this).closest(".remove-data").remove();
	});

	// Shipping Checkbox
	//-----------------------------------------------
	if ($("#shipping-info-check").is(':checked')) {
		$("#shipping-information").hide();
	}
	$("#shipping-info-check").change(function(){
		if ($(this).is(':checked')) {
			$("#shipping-information").slideToggle();
		} else {
			$("#shipping-information").slideToggle();
		}
	});

	//This will prevent the event from bubbling up and close the dropdown when you type/click on text boxes (Header Top).
	//-----------------------------------------------
	$('.header-top .dropdown-menu input').click(function(e) {
		e.stopPropagation(); 
	});

	// Offcanvas side navbar
	//-----------------------------------------------

	if ($("#offcanvas").length>0) {
		$('#offcanvas').offcanvas({
			disableScrolling: false,
			toggle: false
		});
	};

	if ($("#offcanvas").length>0) {
		$('#offcanvas [data-toggle=dropdown]').on('click', function(event) {
		// Avoid following the href location when clicking
		event.preventDefault(); 
		// Avoid having the menu to close when clicking
		event.stopPropagation(); 
		// close all the siblings
		$(this).parent().siblings().removeClass('open');
		// close all the submenus of siblings
		$(this).parent().siblings().find('[data-toggle=dropdown]').parent().removeClass('open');
		// opening the one you clicked on
		$(this).parent().toggleClass('open');
		});
	};

}); // End document ready


/*----------- FEEDBACK Form -----------*/
jQuery(function(){
	var form = $('form[name=FEEDBACK]');

	form.submit(function() {
		$('#form-loading-feedback').fadeIn();
		$('#error-feedback, #success-feedback, #beforesend-feedback').hide();
		if(validate()){ 
			submission();
		} else{
			$('#form-loading-feedback').hide();
			$('#beforesend-feedback, #results-feedback').fadeIn();
		};
		$('input, select, textarea, button', form).blur();
		return false;
	});

	function validate() {
		var errors = [];
		$('.req', form).each(function() {
			if(!$(this).val()){
				errors.push(1);
				$(this).addClass('error');
			} else $(this).removeClass('error');
		});
		if(errors.length === 0)
			 return true;
		else return false;
	};

	function submission(){
		$.ajax({
				type: 'POST',  
				url: form.attr('action'),
				dataType: 'json',
				data: form.serialize(),
				success: function(data){
					$('#form-loading-feedback').hide();
					$('input, textarea', form).removeClass('error');
					if(data.MESSAGE.ERROR < 1){
						$('#results-feedback, #success-feedback').fadeIn();
						$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
					}else $('#results-feedback, #error-feedback').hide().fadeIn();
				},
				error: function(data){
					$('#form-loading-feedback').hide();
					$('#results-feedback, #error-feedback').hide().fadeIn();
				}
		});
		return false;
	};

});
/*----------- FEEDBACK_MODAL Form -----------*/
jQuery(function(){
	var form = $('form[name=FEEDBACK_MODAL]');

	form.submit(function() {
		$('#form-loading-feedback-modal').fadeIn();
		$('#error-feedback-modal, #success-feedback-modal, #beforesend-feedback-modal').hide();
		if(validate()){ 
			submission();
		} else{
			$('#form-loading-feedback-modal').hide();
			$('#beforesend-feedback-modal, #results-feedback-modal').fadeIn();
		};
		$('input, select, textarea, button', form).blur();
		return false;
	});

	function validate() {
		var errors = [];
		$('.req', form).each(function() {
			if(!$(this).val()){
				errors.push(1);
				$(this).addClass('error');
			} else $(this).removeClass('error');
		});
		if(errors.length === 0)
			 return true;
		else return false;
	};

	function submission(){
		$.ajax({
				type: 'POST',  
				url: form.attr('action'),
				dataType: 'json',
				data: form.serialize(),
				success: function(data){
					$('#form-loading-feedback-modal').hide();
					$('input, textarea', form).removeClass('error');
					if(data.MESSAGE.ERROR < 1){
						$('#results-feedback-modal, #success-feedback-modal').fadeIn();
						$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
					}else $('#results-feedback-modal, #error-feedback-modal').hide().fadeIn();
				},
				error: function(data){
					$('#form-loading-feedback-modal').hide();
					$('#results-feedback-modal, #error-feedback-modal').hide().fadeIn();
				}
		});
		return false;
	};
	
    $('button.close').click(function(){	
		$('#form-loading-feedback-modal, #results-feedback-modal').hide();
		$('input, textarea', form).removeClass('error');
		$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
	});	
});
/*----------- CALLBACK Form -----------*/
jQuery(function(){
	var form = $('form[name=CALLBACK]');

	form.submit(function() {
		$('#form-loading-callback').fadeIn();
		$('#error-callback, #success-callback, #beforesend-callback').hide();
		if(validate()){
			submission();
		} else{
			$('#form-loading-callback').hide();
			$('#beforesend-callback, #results-callback').fadeIn();
		};
		$('input, select, textarea, button', form).blur();
		return false;
	});

	function validate() {
		var errors = [];
		$('.req', form).each(function() {
			if(!$(this).val()){
				errors.push(1);
				$(this).addClass('error');
			} else $(this).removeClass('error');
		});
		if(errors.length === 0)
			 return true;
		else return false;
	};

	function submission(){
		$.ajax({
				type: 'POST',
				url: form.attr('action'),
				dataType: 'json',
				data: form.serialize(),
				success: function(data){
					$('#form-loading-callback').hide();
					$('input, textarea', form).removeClass('error');
					if(data.MESSAGE.ERROR < 1){
						$('#results-callback, #success-callback').fadeIn();
						$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
					}else $('#results-callback, #error-callback').hide().fadeIn();
				},
				error: function(data){
					$('#form-loading-callback').hide();
					$('#results-callback, #error-callback').hide().fadeIn();
				}
		});	
		return false;
	};
});
/*----------- CALLBACK_MODAL Form -----------*/
jQuery(function(){
	var form = $('form[name=CALLBACK_MODAL]');

	form.submit(function() {
		$('#form-loading-callback-modal').fadeIn();
		$('#error-callback-modal, #success-callback-modal, #beforesend-callback-modal').hide();
		if(validate()){
			submission();
		} else{
			$('#form-loading-callback-modal').hide();
			$('#beforesend-callback-modal, #results-callback-modal').fadeIn();
		};
		$('input, select, textarea, button', form).blur();
		return false;
	});

	function validate() {
		var errors = [];
		$('.req', form).each(function() {
			if(!$(this).val()){
				errors.push(1);
				$(this).addClass('error');
			} else $(this).removeClass('error');
		});
		if(errors.length === 0)
			 return true;
		else return false;
	};

	function submission(){
		$.ajax({
				type: 'POST',
				url: form.attr('action'),
				dataType: 'json',
				data: form.serialize(),
				success: function(data){
					$('#form-loading-callback-modal').hide();
					$('input, textarea', form).removeClass('error');
					if(data.MESSAGE.ERROR < 1){
						$('#results-callback-modal, #success-callback-modal').fadeIn();
						$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
					}else $('#results-callback-modal, #error-callback-modal').hide().fadeIn();
				},
				error: function(data){
					$('#form-loading-callback-modal').hide();
					$('#results-callback-modal, #error-callback-modal').hide().fadeIn();
				}
		});	
		return false;
	};
});
/*----------- CONTACTS Form -----------*/
jQuery(function(){
	var form = $('form[name=CONTACTS]');

	form.submit(function() {
		$('#form-loading-contacts').fadeIn();
		$('#error-contacts, #success-contacts, #beforesend-contacts').hide();
		if(validate()){ 
			submission();
		} else{
			$('#form-loading-contacts').hide();
			$('#beforesend-contacts, #results-contacts').fadeIn();
		};
		$('input, select, textarea, button', form).blur();
		return false;
	});

	function validate() {
		var errors = [];
		$('.req', form).each(function() {
			if(!$(this).val()){
				errors.push(1);
				$(this).addClass('error');
			} else $(this).removeClass('error');
		});
		if(errors.length === 0)
			 return true;
		else return false;
	};

	function submission(){
		$.ajax({
				type: 'POST',
				url: form.attr('action'),
				dataType: 'json',
				data: form.serialize(),
				success: function(data){
					$('#form-loading-contacts').hide();
					$('input, textarea', form).removeClass('error');
					if(data.MESSAGE.ERROR < 1){
						$('#results-contacts, #success-contacts').fadeIn();
						$('input, select, textarea', form).not('[type=hidden], [type=submit]').val('');
					}else $('#results-contacts, #error-contacts').hide().fadeIn();
				},
				error: function(data){
					$('#form-loading-contacts').hide();
					$('#results-contacts, #error-contacts').hide().fadeIn();
				}
		});	
		return false;
	};
});

if (jQuery(".btn-print").length>0) {
	function print_window() {
		var mywindow = window;
		mywindow.document.close();
		mywindow.focus();
		mywindow.print();
		mywindow.close();
	}
}