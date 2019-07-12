jQuery(document).ready(function () {
	"use strict";

	/* deletes empty containers (that are byproducts of using full-width sections) */
	jQuery('.container').each(function () {
		if (jQuery(this).children().length < 1) {
			jQuery(this).remove();
		}
	});
	/* ! */

	/* gives inputs with empty 'type' a 'text' type, needed to style them properly */
	jQuery('input').each(function () {
		if (jQuery(this).attr("type") == undefined) {
			jQuery(this).attr("type", "text");
		}
	});
	/* ! */

	/* gives accordion headings proper classes */
	jQuery('.accordion').each(function () {
		jQuery('.panel-collapse.in', this).prev().addClass('active');
		jQuery('.panel-collapse', this).on('hidden.bs.collapse',function () {
			jQuery(this).prev().removeClass('active');
		}).on('shown.bs.collapse', function () {
							jQuery(this).prev().addClass('active');
						});
	});
	/* ! */

	/* gives virtual focus event to navbar search button when it's text is focused and vice versa */
	(function () {
		var $search = jQuery('#navbar-search');
		var $parent = $search.parent().parent();
		var $button = jQuery('button', $parent);
		$search.focus(function () {
			$button.addClass("focus");
		}).blur(function () {
							$button.removeClass("focus");
						});
		$button.focus(function () {
			$search.addClass("focus");
		}).blur(function () {
							$search.removeClass("focus");
						});
	})();
	/* ! */

	/* gives virtual focus event to widget search button when it's text is focused*/
	(function () {
		jQuery('.search-widget input[type="search"]').each(function () {
			var $parent = jQuery(this).parent().parent();
			jQuery(this).focus(function () {
				jQuery('button', $parent).addClass("focus");
			}).blur(function () {
								jQuery('button', $parent).removeClass("focus");
							});
		})
	})();
	/* ! */

	/* shipment calculator toggle */
	(function () {
		jQuery('.shipment-calc-toggle').click(function () {
			$('.shipment-calc td > div').slideToggle();
		});
	})();
	/* ! */

	/* ship to billing address - hide form */

	jQuery("#ship-to-billing-address").on("change", function () {
   if (this.checked) {
     jQuery(this).closest("form").find("#dataToHide").slideUp(200);
   } else {
	   jQuery(this).closest("form").find("#dataToHide").slideDown(200);
   }
 });

	/* CHOSEN */
	/* plugin for extending select inputs */
	(function () {

	})();
	/* !CHOSEN */

	/* JQUERY UI SPINNER */
	function spinner(){
		if (jQuery().spinner) {
			var config = {
				'.spinner': {},
				'.spinner-quantity': {min: 1}
			};
			for (var selector in config) {
				jQuery(selector).spinner(config[selector]);
			}
		}
	 }
	/* !JQUERY UI SPINNER  */
    spinner();

	function adjustIsotopePrices(min, max) {
		if (!(jQuery("#isotopeContainer .isotope-item span.price").length > 0)) {
			// if not have price exit
			return false;
		}
		;
		$('#isotopeContainer .isotope-item').removeClass('priced').each(function () {
			var $e = $(this);
			var price = $('span.price', $e).text().replace('$', '');
			price = parseFloat(price);

			if (price >= min && price <= max) {
				$e.addClass('priced');
			}
		});

		$(window).trigger('hashchange');
	}


	/* !JQUERY UI SPINNER  */
     (function ($) {
        if (jQuery().slider) {

            /* !JQUERY UI SPINNER  */

            var $filter = $('.filters-range');

            var fMin = parseFloat($filter.attr('data-min'));
            var fMax = parseFloat($filter.attr('data-max'));            
            var cMin = parseFloat($filter.attr('min'));
            var cMax = parseFloat($filter.attr('max'));
            var cur=' '+$filter.attr('currency');
            var $filterValues = $('.filter-value', $filter);

            $('body').on('filters.reset', function () {
                $filterWidget.slider("values", 0, fMin);
                $filterWidget.slider("values", 1, fMax);

                $('.min', $filterValues).val(fMin);
                $('.max', $filterValues).val(fMax);

                //adjustIsotopePrices(fMin, fMax);
            });

            $(".filters-clear").click();


           // adjustIsotopePrices(fMin, fMax);

            var $filterWidget = $('.filter-widget', $filter);
            $filterWidget.slider({
                range: true,
                min: cMin,
                max: cMax,
                step: parseFloat($filter.attr('data-step')),
                values: [fMin, fMax],
                slide: function (e, ui) {
                    $('.min', $filterValues).val(ui.values[0]+cur);
                    $('.max', $filterValues).val(ui.values[1]+cur);
                },
                stop: function (e, ui) {
                    $('.min', $filterValues).val(ui.values[0]);
                    $('.max', $filterValues).val(ui.values[1]);
                    smartFilter.click(this)
                   // adjustIsotopePrices(ui.values[0], ui.values[1]);
                }
            });
            $('.min', $filterValues).val($filterWidget.slider("values", 0));
            $('.max', $filterValues).val($filterWidget.slider("values", 1));
            $('.min, .max', $filterValues).on('input',function () {
                if ($(this).hasClass('min')) {
                    $filterWidget.slider("values", 0, $(this).val().replace(/\D/g, ''));
                }
                if ($(this).hasClass('max')) {
                    $filterWidget.slider("values", 1, $(this).val().replace(/\D/g, ''));
                }
            }).on('blur',function () {
                               
                            }).on('focus', function () {
                                if ($(this).val().charAt(0) == cur) {
                                    $(this).val($(this).val().substr(1));
                                }
                            })

        }
    })(jQuery);
	/* RATY */
	$.rating=function () {
		if (jQuery().raty) {
			var config = {
				'.rating': {
					path: template_path+'/images',
					starOn: "rating-star-on.png",
					starOff: "rating-star-off.png",
					starHalf: "rating-star-half.png",
					numberMax: 5,
					width: false,
					score: function () {
						return $(this).attr('data-score');
					},
					noRatedMsg: "Not rated yet",
					hints: [null, null, null, null, null],
					half: true,
                    scoreName: 'PROPS[RATE]',
					readOnly: true
				},
				'.rate': {
					path: template_path+'/images',
					starOn: "rating-star-on-big.png",
					starOff: "rating-star-off-big.png",
					starHalf: "rating-star-half-big.png",
					numberMax: 5,
					width: false,
					score: function () {
						return $(this).attr('data-score');
					},
					noRatedMsg: "Not rated yet",
                    scoreName: 'PROPS[RATE]',
					hints: [null, null, null, null, null],
					half: true
				}
			};
			for (var selector in config) {
				jQuery(selector).raty(config[selector]);
			}
		}
	};
    $.rating();
	/* !RATY  */

	/* PRETTYPHOTO */
	(function () {
		if (jQuery().prettyPhoto) {
			if ($(document).width() >= 768) {
				$("a[data-rel^='prettyPhotoGallery']").prettyPhoto({
					animation_speed: 'fast', /* fast/slow/normal */
					slideshow: false, /* false OR interval time in ms */
					autoplay_slideshow: false, /* true/false */
					opacity: 0.90, /* Value between 0 and 1 */
					show_title: false, /* true/false */
					allow_resize: false, /* Resize the photos bigger than viewport. true/false */
					counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
					theme: 'pp_default pp_img_gallery', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
					horizontal_padding: 0, /* The padding on each side of the picture */
					hideflash: false, /* Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto */
					wmode: 'opaque', /* Set the flash wmode attribute */
					autoplay: true, /* Automatically start videos: True/False */
					modal: false, /* If set to true, only the close button will close the window */
					deeplinking: false, /* Allow prettyPhoto to update the url to enable deeplinking. */
					overlay_gallery: true, /* If set to true, a gallery will overlay the fullscreen image on mouse over */
					keyboard_shortcuts: true, /* Set to false if you open forms inside prettyPhoto */
					changepicturecallback: function () {
					}, /* Called everytime an item is shown/changed */
					callback: function () {
					}, /* Called when prettyPhoto is closed */
					ie6_fallback: true,
					markup: '<div class="pp_pic_holder"> \
                    <div class="pp_content_container"> \
                        <article class="pp_content"> \
                            <div class="ppt">&nbsp;</div> \
                            <div class="pp_loaderIcon"></div> \
                            <div class="pp_fade"> \
                                <a href="#" class="pp_expand" title="Expand the image">Expand</a> \
                                <div class="pp_hoverContainer"> \
                                    <a class="pp_next" href="#">next</a> \
                                    <a class="pp_previous" href="#">previous</a> \
                                </div> \
                                <div id="pp_full_res"></div> \
                                <div class="pp_details"> \
                                    <a class="pp_close" href="#">Close</a> \
                                </div> \
                            </div> \
                        </article> \
                    </div> \
                </div> \
                <div class="pp_overlay"></div>',
					gallery_markup: '<div class="pp_gallery"> \
                            <a href="#" class="pp_arrow_previous">Previous</a> \
                            <div> \
                                <ul> \
                                                            {gallery} \
                                </ul> \
                            </div> \
                            <a href="#" class="pp_arrow_next">Next</a> \
                        </div>',
					image_markup: '<img id="fullResImage" src="{path}" />',
					flash_markup: '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="{width}" height="{height}"><param name="wmode" value="{wmode}" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="{path}" /><embed src="{path}" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{width}" height="{height}" wmode="{wmode}"></embed></object>',
					quicktime_markup: '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="{height}" width="{width}"><param name="src" value="{path}"><param name="autoplay" value="{autoplay}"><param name="type" value="video/quicktime"><embed src="{path}" height="{height}" width="{width}" autoplay="{autoplay}" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>',
					iframe_markup: '<iframe src ="{path}" width="{width}" height="{height}" frameborder="no"></iframe>',
					inline_markup: '<div class="pp_inline">{content}</div>',
					custom_markup: '',
					social_tools: false /* html or false to disable */,
					hook: 'data-rel'
				});
			}
		}
	})();
	/* !PRETTYPHOTO  */

	/* BOOTSTRAP TABS CHANGING FROM URL */

	(function () {
		var url = document.location.toString();

		if (url.match("#")) {
			var window_hash = window.location.hash;
			if (jQuery(window_hash, '.tabs').length) {
				jQuery('.tabs .nav-tabs a[href="' + window_hash + '"]').tab('show');
			}
		}

		jQuery('a[href^="#"]').not('.nav-tabs a').on('click', function () {
			var $this = jQuery(this);
			if (!($this.attr("href") == "#")) {
				var link_hash = '#' + jQuery(this).attr('href').split('#')[1];

				if (jQuery(link_hash, '.tabs').length) {
					jQuery('.tabs .nav-tabs a[href="' + link_hash + '"]').tab('show');
					jQuery('html, body').animate({
						scrollTop: jQuery('.tabs').offset().top
					}, 300);
				}
			}
		});

		jQuery('.tabs .nav-tabs a').on('click', function (e) {
			window.location.hash = e.target.hash;
		});
	})();
	/* !BOOTSTRAP TABS CHANGING FROM URL */

	/* SIZE TABLE TOGGLE */
	(function () {
		/*
		if (jQuery('document').width() <= 767) {
			jQuery('.size-guide-wrapper').hide();
		}
		*/
		jQuery('.size-guide-toggle').on('click', function () {
				jQuery('.size-guide-wrapper').toggleClass("visible-xs visible-sm");
			return false;
		});
	})();
	/* ! */


	jQuery(window).on('scroll', function () {

		/* in-viewport plugin */
		(function ($) {
			$.belowthefold = function (element, settings) {
				var fold = $(window).height() + $(window).scrollTop();
				return fold <= $(element).offset().top - settings.threshold;
			};
			$.abovethetop = function (element, settings) {
				var top = $(window).scrollTop();
				return top >= $(element).offset().top + $(element).height() - settings.threshold;
			};
			$.rightofscreen = function (element, settings) {
				var fold = $(window).width() + $(window).scrollLeft();
				return fold <= $(element).offset().left - settings.threshold;
			};
			$.leftofscreen = function (element, settings) {
				var left = $(window).scrollLeft();
				return left >= $(element).offset().left + $(element).width() - settings.threshold;
			};
			$.inviewport = function (element, settings) {
				return!$.rightofscreen(element, settings) && !$.leftofscreen(element, settings) && !$.belowthefold(element, settings) && !$.abovethetop(element, settings);
			};
			$.extend($.expr[':'], {"below-the-fold": function (a, i, m) {
				return $.belowthefold(a, {threshold: 0});
			}, "above-the-top": function (a, i, m) {
				return $.abovethetop(a, {threshold: 0});
			}, "left-of-screen": function (a, i, m) {
				return $.leftofscreen(a, {threshold: 0});
			}, "right-of-screen": function (a, i, m) {
				return $.rightofscreen(a, {threshold: 0});
			}, "in-viewport": function (a, i, m) {
				return $.inviewport(a, {threshold: 0});
			}});
		})(jQuery);
		/* / in-viewport plugin */

		/* PROGRESS BARS */
		/* line */
		jQuery('.progress:in-viewport').each(function () {
			var $bar = jQuery('.progress-bar', this);

			if ($bar.width() == 5) {
				$bar.delay(700).stop().animate({
					'width': jQuery(this).attr('data-percentage') + '%'
				}, 600, 'linear');
			}
		});
		/* pie */
		jQuery('.pie-chart:in-viewport').each(function () {
			var $chart = jQuery(this);
			var scaleColor = $chart.attr('data-scalecolor');
			var trackColor = $chart.attr('data-trackcolor');

			$chart.easyPieChart({
				animate: $chart.attr('data-animate'),
				barColor: $chart.attr('data-barcolor'),
				trackColor: trackColor,
				scaleColor: scaleColor == 'false' ? false : scaleColor,
				lineCap: $chart.attr('data-linecap'),
				lineWidth: $chart.attr('data-linewidth'),
				size: $chart.attr('data-size')
			});
		});

	});

	jQuery(window).load(function () {

		/* set min-height of #Content so that the page is at least of window's height */
		(function () {
			var h = jQuery(window).height();
			h -= jQuery('body .wrapper > footer').outerHeight(true);
			h -= jQuery('#MainNav').outerHeight(true);

			jQuery('#Content, .four-o-four').css('min-height', h);
		})();

		/* MOBILE MENUS: */
		/* shopping cart */
		
		$.cartrefresh=function () {
            var $body = jQuery('body');
			var shoppingCart = {};
			shoppingCart.$wrap = jQuery('.shopping-cart-widget');
			shoppingCart.$button = jQuery('> button.btn', shoppingCart.$wrap);
			shoppingCart.$panel = jQuery('[role="complementary"]', shoppingCart.$wrap);
			shoppingCart.$unclick = null;

			shoppingCart.$panel.click(function (e) {
				e.stopPropagation();
			});

			shoppingCart.$button.click(function (e) {
				e.stopPropagation();
				jQuery('body').removeClass('active-nav').toggleClass('active-sidebar');
				if (shoppingCart.$unclick == null) {
					shoppingCart.$wrap.prepend('<div class="unclick"></div>');
					shoppingCart.$unclick = jQuery('.unclick', shoppingCart.$wrap);
					var height = jQuery("#Content").height() + jQuery('footer').height() - 11;
					shoppingCart.$panel.height(height + jQuery('#MainNav').height());
					shoppingCart.$unclick.height(height + 90);
					shoppingCart.$unclick.click(function () {
						$body.removeClass('active-sidebar active-nav');
					});
				}
                    if($(".ajax_overlay").length)$(".ajax_overlay").remove(); 
                    else
                    $('body').prepend('<div class="ajax_overlay" style="height: 100%;width: 100%;position: fixed;z-index: 1999;"></div>');
			});
            $(document).on('click', '.ajax_overlay', function(){
                $body.removeClass('active-sidebar active-nav');
                $(this).remove();
            });
            
            
		};
        $.cartrefresh();

		/* navigation */
		(function () {
			var navMenu = {};
			navMenu.$wrap = jQuery('#MainNav nav');
			navMenu.$button = jQuery('.navbar-header .btn', navMenu.$wrap);
			navMenu.$panel = jQuery('[role="navigation"]', navMenu.$wrap);
			navMenu.$unclick = null;

			navMenu.$button.click(function (e) {
				e.stopPropagation();
				jQuery('body').removeClass('active-sidebar').toggleClass('active-nav');
				if (navMenu.$unclick == null) {
					navMenu.$wrap.prepend('<div class="unclick"></div>');
					navMenu.$unclick = jQuery('.unclick', navMenu.$wrap);
					var height = jQuery("#Content").height() + jQuery('footer').height();
					navMenu.$panel.height(height);
					navMenu.$unclick.height(height);
					navMenu.$unclick.click(function () {
						$body.removeClass('active-sidebar active-nav');
					});
				}
			});
		})();


		/* FLEXSLIDER */
		(function () {
			/* full width */
			(function () {
				var i = 0;
				jQuery('.flexslider-full-width .flexslider').each(function () {
					jQuery('.flexslider-full-width-controls', this).attr('id', 'flexslider-full-width-controls-' + i);
					jQuery(this).flexslider({
						controlsContainer: '#flexslider-full-width-controls-' + i,
						smoothHeight: true,
						slideshow: false,
						animationSpeed: 1000
					});
					i++;
				});
			})();

			/* no pager */
			jQuery('.flexslider.flexslider-nopager').each(function () {
				jQuery(this).flexslider({
					controlNav: false,
					smoothHeight: true,
					slideshow: false,
					animationSpeed: 1000
				});
			});

			/* pager */
			jQuery('.flexslider.flexslider-pager').each(function () {
				jQuery(this).flexslider({
					controlNav: true,
					smoothHeight: true,
					slideshow: false,
					animationSpeed: 1000
				});
			});

			/* thumbnail slider */
			jQuery('.flexslider.flexslider-thumbnails').each(function () {
				var $this = jQuery(this);
				$this.flexslider({
					controlNav: false,
					smoothHeight: true,
					slideshow: false,
					animationSpeed: 600,
					before: function(slider) {
						var $thumbnails = $this.closest(".thumbnailSlider").find(".smallThumbnails");
						$thumbnails.find("li").removeClass("active");
					},
					after: function (slider) {
						setActiveThumbnail($this, slider.currentSlide);
					}
				});
			});

		})();
		/* !FLEXSLIDER */
	});


	/* thumbnail slider - bottom thumbnail list */
	jQuery(".thumbnailSlider .smallThumbnails li").click(function () {
		var $this = jQuery(this);
		var $slider = $this.closest(".thumbnailSlider").find(".flexslider");
		var target = $this.data("target");
		// target - number of slider - from 0
		if ($slider.data('flexslider')) {
			$slider.data('flexslider').flexAnimate(target);
		}

		setActiveThumbnail($slider, target);

		return false;

	})

	function setActiveThumbnail($slider, index) {
		var $thumbnails = $slider.closest(".thumbnailSlider").find(".smallThumbnails");
		$thumbnails.find("li").removeClass("active");
		jQuery("li[data-target='" + index + "']", $thumbnails).addClass("active");
	}


	/* clickable main parent item menu on desktop */
	function mobileMenuVisible() {
		if (jQuery(".navbar-header .navbar-toggle.btn").is(":visible")) {
			return true;
		} else {
			return false;
		}
	}

	jQuery(window).bind('resize',function () {
		if (!mobileMenuVisible()) {
			jQuery("#MainNav li.dropdown > .dropdown-toggle").removeAttr("data-toggle data-target");
		} else {
			jQuery("#MainNav li.dropdown > .dropdown-toggle").attr("data-toggle", "dropdown");
		}
	}).trigger('resize');


	/* ISOTOPE */

	var $container = $('#isotopeContainer');

	$container.imagesLoaded(function () {
		isotopeInit();
	});


	function isotopeInit() {

		var filters = {};

		// object that will keep track of options
		var isotopeOptions = {}, // defaults, used if not explicitly set in hash
						defaultOptions = {
							filter: '',
							sortBy: 'original-order',
							itemSelector: '.isotope-item',
							layoutMode: 'fitRows',
							sortAscending: true,
                            transformsEnabled: false
						};

		// set up Isotope
		//$container.isotope( setupOptions );
		var isOptionLinkClicked = false;


		function updateActiveElements($elem) {
			var $optionSet = $elem.parents('.myFilters');
			// remove current active
			$optionSet.find('.selected').siblings("input").attr("checked", false);
			$optionSet.find('.selected').removeClass('selected');
			// set active element
			$elem.addClass('selected');
			$elem.siblings("input").attr("checked", true);
		}

		$(".filters-clear").click(function () {
			jQuery(this).blur();
			jQuery(".myFilters").each(function () {
				//jQuery(this).find(".isotopeFilter:first").trigger("click");
				jQuery(".filters-active .element-header").hide();
			})
			// reset prioe range slider
			jQuery('body').trigger('filters.reset');

		});

		// filter buttons
		$('.myFilters .isotopeFilter').click(function () {
			var $this = $(this);
			// don't proceed if already selected
			

			updateActiveElements($this);

			var $optionSet = $this.parents('.myFilters');

			// store filter value in object
			// i.e. filters.color = 'red'

			var group = $optionSet.attr('data-option-group');
			var type = $optionSet.attr('data-option-type');

		

			

			// set hash, triggers hashchange on window
			$.bbq.pushState(isotopeOptions);
			isOptionLinkClicked = true;
               
		});
		var hashChanged = false;

		$(window).bind('hashchange', function (event) {
			// get options object from hash
			var hashOptions = window.location.hash ? $.deparam.fragment(window.location.hash, true) : {}, // do not animate first call
							aniEngine = hashChanged ? 'best-available' : 'none', // apply defaults where no option was specified
							options = $.extend({}, defaultOptions, hashOptions, { animationEngine: aniEngine });
			// apply options from hash
			// + add filter by price
			//options.filter = ".priced" + options.filter;
			$container.isotope(options);

			// update result counter
			var counter = 0;
			jQuery(".isotope-item").not('.isotope-hidden').each(function () {
				counter++;
			});
			jQuery(".filters-result-count > span").text(counter);

			// update you've selected box + remove 0, 1 elements : empty and 'priced'

			var youselected = options["filter"].split('.').splice(2);
			jQuery(".filters-active .filters-list").html(" ");
			jQuery(".shop-list-filters .filters-active").hide();
			jQuery(".filters-active .element-header").hide();
			for (var key in youselected) {
				var textLink = $(".myFilters").find("[data-option-value='." + youselected[key] + "']").text();
				jQuery(".filters-active .filters-list").append("<li><div class='form-group'><label><span class='filter-value' data-filter-temp='" + youselected[key] + "'>" + textLink + "</span><button type='button' class='close' aria-hidden='true'><span aria-hidden='true' data-icon='&#xe005;'></span></button></label></div></li>");
			}
			// if active filters show title
			if (!(options["filter"] == ".priced")) {
				jQuery(".filters-active .element-header").show();
				jQuery(".shop-list-filters .filters-active").show();
			}

			// remove you've selected on click and remove filter
			jQuery(".filters-active .filters-list").on('click', 'li', function () {
				var $this = jQuery(this).find(".filter-value");
				$(".myFilters").find("[data-option-value='." + $this.data("filter-temp") + "']").closest(".myFilters").find(".isotopeFilter:first").trigger("click");
				return false;
			});

			// save options
			isotopeOptions = hashOptions;

			// if option link was not clicked
			// then we'll need to update selected links
			if (!isOptionLinkClicked) {
				// iterate over options
				var hrefValue, $selectedLink;

				var hrefObj = {};
				hrefObj[ "filter" ] = options[ "filter" ];
				hrefObj[ "sortBy" ] = options[ "sortBy" ];
				// convert object into parameter string
				// i.e. { filter: '.inner-transition' } -> 'filter=.inner-transition'
				hrefValue = $.param(hrefObj).split('&');
				var hrefFilters = hrefValue[0].split('.');

				for (var key in hrefFilters) {
					$selectedLink = jQuery(".myFilters").find('[data-option-value=".' + hrefFilters[key] + '"]').trigger("click");
				}

				var hrefSort = hrefValue[1].split('=');
				$selectedLink = jQuery(".myFilters").find('[data-option-value="' + hrefSort[1] + '"]').trigger("click");

			}

			isOptionLinkClicked = false;
			hashChanged = true;
		})// trigger hashchange to capture any hash data on init
						.trigger('hashchange');
	}

	// isotope init end


	// show filters button init
	function updateToggleListFilters() {
		jQuery("#listFilters").removeAttr("style");

		jQuery("#toggleListFilters").each(function () {
			var $this = jQuery(this);
			if (jQuery("#listFilters").is(":visible")) {
				$this.text($this.data("texthidden"));
			} else {
				$this.text($this.data("textvisible"));
			}
		});
	};
	updateToggleListFilters();

	var resizeTimer;
	jQuery(window).resize(function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(updateToggleListFilters, 100);
	});

	jQuery("#toggleListFilters").click(function () {
		var $this = jQuery(this);
		$this.blur();
		jQuery("#listFilters").slideToggle(400, function () {
			if (jQuery(this).is(":visible")) {
				$this.text($this.data("texthidden"));
			} else {
				$this.text($this.data("textvisible"));
			}
		});
		return false;
	});


	/* tooltips init */
	jQuery("[data-toggle='tooltip']").tooltip();

	/* google map */


	/* remove element from wishlist after button close click */
	jQuery("button.close").click(function(){
		jQuery(this).closest(".shop-item-wishlist").fadeOut(200);
	})
 

});
/* / document ready */

