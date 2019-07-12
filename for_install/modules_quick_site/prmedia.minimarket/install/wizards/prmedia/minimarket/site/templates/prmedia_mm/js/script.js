
/***
makeup
***/
(function (window, $) {
	$(function () {
		
		var window_$ = $(window),
			body_$ = $('body'),
			header_$ = $('#header'),
			content_$ = $('#content'),
			footer_$ = $('#footer');

		/*** change font-size for small resolutions ***/
		function viewport_size () {
    	var e = window, a = 'inner';
    	if (!('innerWidth' in window )) {
        a = 'client';
        e = document.documentElement || document.body;
    	}
    	return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
		}
		function change_font_fn () {
			var w = viewport_size().width, font_size = '';
			if (w <= 800 && w >= 320) {
				var font_size = w / 20;
			}
			body_$.css('font-size', font_size);
			content_$.find('li:before').css('height', font_size);
			
			var footer_height = footer_$.outerHeight();
			content_$.css('padding-bottom', footer_height);
		};
		change_font_fn();
		window_$.on('resize', change_font_fn);



		/*** add additional links in header for small resolution ***/
		var auth_link_$ = header_$.find('.auth-link'),
			basket_link_$ = header_$.find('.basket-link');
		auth_link_$.clone().addClass('mob-auth-link').text('').appendTo($('#mob_header'));
		basket_link_$.clone().addClass('mob-basket-link').text('').appendTo($('#mob_header'));



		/*** mobile submenu ***/
		var menu_sub_$ = $('#menu ul li'),
			menu_sub_link_$ = menu_sub_$.find('a');
		menu_sub_link_$.on('click', function (e) {
			e.stopPropagation();
			return true;
		});
		menu_sub_$.on('click', function (e) {
			e.stopPropagation();
			var this_$ = $(this), 
				parent_$ = this_$.parent(),
				is_active = this_$.hasClass('active');

			if (is_active) {
				this_$.removeClass('active');
			} else {
				parent_$.find('.active').removeClass('active');
				this_$.addClass('active');
			}
		});
		
		
		/*** content-split ***/
		var content_split_button_$ = $('.content-split-button');
		content_split_button_$.click(function (e) {
			e.preventDefault();
			var this_$ = $(this),
				cls = this_$.data('cls');
			$('.content-split > div').each(function () {
				var th_$ = $(this);
				th_$.toggleClass('content-split-active', th_$.hasClass(cls));
			})
		});
	});
	
	/*** cusel fix ***/
	$(document).on('click', '.cuselActive label', function () {
		$('#cuselBox').hide();
	})
	
}) (window, jQuery);


/***
form element styling
***/
(function (window, $) {
	
	window.select_styling = function (context) {
		/*** select ***/
		var params = {
			changedEl: context + ' select',
			visRows: 5
    };
    cuSel(params);
    $(window).on('resize', function () {
    	$(context + ' .cusel').each(function () {
    		var max_width = 0;
    		$(this).find('label').each(function () {
    			var width = realWidth($(this)) + 20;
    			if (max_width === 0 || max_width < width) {
    				max_width = width;
    			}
    		});
    		$(this).width(max_width);
    	});
    	$('#cuselBox').hide();
    });
	};
	window.form_element_styling = function () {
		var document_$ = $(document),
			prototype_$ = $('#input_prototype'),
			radio_wrapper_class = '.input-radio',
			radio_selector = 'input[type="radio"]',
			radio_prototype_$ = prototype_$.find(radio_wrapper_class),
			radio_$ = document_$.find(radio_selector),
			checkbox_wrapper_class = '.input-checkbox',
			checkbox_selector = 'input[type="checkbox"]',
			checkbox_prototype_$ = prototype_$.find(checkbox_wrapper_class),
			checkbox_$ = document_$.find(checkbox_selector);

		/*** radiobutton ***/
		document_$.on('click', radio_wrapper_class, function () {$(this).find(radio_selector).click(); });
		radio_$.on(isIE() && isIE() < 9 ? 'propertychange' : 'change', radio_toggle_class);
		radio_$.on('click', function (e) {e.stopPropagation();});
		radio_$.each(function () {$(this).wrap(radio_prototype_$); });
		function radio_toggle_class () {
			$(radio_wrapper_class).each(function () {
				var el_$ = $(this); el_$.toggleClass('active', el_$.find(radio_selector).is(':checked'));
			});
		};
		radio_toggle_class();

		/*** checkbox ***/
		document_$.on('click', checkbox_wrapper_class, function () {$(this).find(checkbox_selector).click(); });
		checkbox_$.on(isIE() && isIE() < 9 ? 'propertychange' : 'change', checkbox_toggle_class);
		checkbox_$.on('click', function (e) {e.stopPropagation();});
		checkbox_$.each(function () {$(this).wrap(checkbox_prototype_$); });
		function checkbox_toggle_class () {
			$(checkbox_wrapper_class).each(function () {
				var el_$ = $(this); el_$.toggleClass('active', el_$.find(checkbox_selector).is(':checked'));
			});
		};
		checkbox_toggle_class();
		
		window.select_styling('#content');
	}
	$(window.form_element_styling);
	function realWidth (obj) {var clone = obj.clone(); clone.css("visibility","hidden"); $('#content .right-column').append(clone); var width = clone.innerWidth(); clone.remove(); return width; }
	function isIE () {var n = navigator.userAgent.toLowerCase(); return (n.indexOf('msie') != -1) ? parseInt(n.split('msie')[1]) : false;}
}) (window, jQuery);


// quantity logic
(function (window, $) {

	// required dom objects
	var document_$ = $(document);

	// increase/decrease and changes of product count
	// used in top/section/detail components
	document_$.on('ready', product_count_control_fn);
	function product_count_control_fn () {

		// requred dom objects
		var buy_form_$ = $('.product-form');
		var plus_btn_$ = buy_form_$.find('.count-btn.plus');
		var minus_btn_$ = buy_form_$.find('.count-btn.minus');
		var quantity_inp_$ = buy_form_$.find('.count-field');

		// handler for +/- buttons clicks
		// change quantity
		plus_btn_$.click(function () {
			return change_quantity_fn($(this), 1);
		});
		minus_btn_$.click(function () {
			return change_quantity_fn($(this), -1);
		});
		set_quantity_value_fn = function (el_$, value) {
			el_$.val(value);
			$('.basket [name="' + el_$.attr('name') + '"]').val(value);
			$('.mobile-basket [name="' + el_$.attr('name') + '"]').val(value);
		};
		var change_quantity_fn = function (element, multiple) {
			var quantity_inp_$ = element.closest('.product-form').find('.count-field');
			var value = quantity_inp_$.val();
			var step = quantity_inp_$.data('step');

			step = parseFloat(step);
			if (isNaN(step) || step <= 0) {
				step = 1;
			}

			value = parseFloat(value);
			if (isNaN(value) || value <= 0) {
				value = step;
			}

			var available_multiples = [1, -1];
			if ($.inArray(multiple, available_multiples) === -1) {
				return false;
			}

			var new_value = value + step * multiple;
			if (new_value <= 0) {
				new_value = value;
			}

			var order = 1 / step;
			new_value = Math.round(new_value * order) / order;

			set_quantity_value_fn(quantity_inp_$, new_value);

			return false;
		};

		// in quantity input can be only numbers and one decimal point
		// another symbols will be removed
		quantity_inp_$.keyup(function () {
			var value = $(this).val();
			value = value.replace(/[^0-9\.]/g,'');
			if(value.split('.').length > 2) {
				value = value.replace(/\.+$/,"");
			}
			set_quantity_value_fn($(this), value);
			//$(this).val(value);
		});
		quantity_inp_$.focusout(function () {
			var value = $(this).val();
			var tmp = parseFloat(value);
			if (isNaN(tmp) || tmp <= 0) {
				var step = parseFloat($(this).data('step'));
				if (isNaN(step) || step <= 0) {
					step = 1;
				}
				value = step;
			}
			set_quantity_value_fn($(this), value);
			//$(this).val(value);
		});
	};
}) (window, jQuery);