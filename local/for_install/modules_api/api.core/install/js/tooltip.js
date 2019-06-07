/**
 * $.fn.apiTooltip
 */
(function ($) {

	var $tooltip, tooltipdelay, checkIdle;

	var defaults = {
		type: 'default',
		theme: 'default',

		offset: 5,
		pos: 'top',
		delay: 0, // in miliseconds
		cls: '',
		activeClass: 'api_tooltip_active',
		src: function (el) {
			var title = el.attr('title') || el.data("title");

			if (title !== undefined) {
				el.data('cached-title', title).removeAttr('title');
			}

			return el.data("cached-title");
		}

	};

	var methods = {

		init: function (params) {

			var options = $.extend(true, {}, defaults, params);

			if (!this.data('apiTooltip')) {
				this.data('apiTooltip', options);

				// код плагина

				$('html').addClass('api-tooltip-init');

				$(this).each(function (index, element) {

					$(element).on('click mouseenter', function (e) { //'click mouseover'

						if (!$tooltip) {
							$tooltip = $('<div class="api_tooltip"></div>').appendTo("body");
						}

						//z-index: 9999999; left: 400px; top: 1079.08px; height: 42px; width: 347.933px; animation-duration: 350ms; transition-duration: 350ms;
						$.fn.apiTooltip('show', $(element), $tooltip, options);
						//$(tipId).addClass(options.activeClass);
					});

					$(element).on('mouseleave', function () {//'mouseleave'
						$.fn.apiTooltip('hide', $tooltip, options);
					});

				});
			}

			return this;
		},
		show: function (element, $tooltip, options) {

			var tip = typeof(options.src) === 'function' ? options.src($(element)) : options.src;

			if (tooltipdelay) clearTimeout(tooltipdelay);
			if (checkIdle) clearInterval(checkIdle);

			if (typeof(tip) === 'string' ? !tip.length : true) return;

			//$tooltip.stop().css({top: -2000, visibility: 'hidden'}).removeClass(options.activeClass).show();
			//$tooltip.stop().css({top: -2000, visibility: 'hidden'}).removeClass(options.activeClass).show();
			$tooltip.stop().css({opacity: 0}).removeClass(options.activeClass);

			var html = '';
			html += '<div class="api_tooltip_dialog">';

			//START content
			html += '<div class="api_tooltip_content">';
			html += tip;
			html += '</div>';
			//END content

			html += '</div>';

			$tooltip.html(html);

			var pos = $.extend({}, element.offset(), {width: element[0].offsetWidth, height: element[0].offsetHeight}),
				 width = $tooltip[0].offsetWidth,
				 height = $tooltip[0].offsetHeight,
				 offset = typeof(options.offset) === "function" ? options.offset.call(element) : options.offset,
				 position = typeof(options.pos) === "function" ? options.pos.call(element) : options.pos,
				 tmppos = position.split("-"),
				 tcss = {
					 //display    : 'none',
					 //visibility : 'visible',
					 top: (pos.top + pos.height + height),
					 left: pos.left
				 };

			// prevent strange position
			// when tooltip is in offcanvas etc.
			if ($('html').css('position')=='fixed' || $('body').css('position')=='fixed'){
				var bodyoffset = $('body').offset(),
					 htmloffset = $('html').offset(),
					 docoffset  = {top: (htmloffset.top + bodyoffset.top), left: (htmloffset.left + bodyoffset.left)};

				pos.left -= docoffset.left;
				pos.top  -= docoffset.top;
			}


			/*if ((tmppos[0] == 'left' || tmppos[0] == 'right') && UI.langdirection == 'right') {
				tmppos[0] = tmppos[0] == 'left' ? 'right' : 'left';
			}*/

			var variants =  {
				bottom  : {top: pos.top + pos.height + offset, left: pos.left + pos.width / 2 - width / 2},
				top     : {top: pos.top - height - offset, left: pos.left + pos.width / 2 - width / 2},
				left    : {top: pos.top + pos.height / 2 - height / 2, left: pos.left - width - offset},
				right   : {top: pos.top + pos.height / 2 - height / 2, left: pos.left + pos.width + offset}
			};

			$.extend(tcss, variants[tmppos[0]]);

			if (tmppos.length == 2) tcss.left = (tmppos[1] == 'left') ? (pos.left) : ((pos.left + pos.width) - width);

			var checkBoundary =  function (left, top, width, height) {

				var axis = "";

				if (left < 0 || ((left - $(window).scrollTop()) + width) > window.innerWidth) {
					axis += "x";
				}

				if (top < 0 || ((top - $(window).scrollTop()) + height) > window.innerHeight) {
					axis += "y";
				}

				return axis;
			};

			var boundary = checkBoundary(tcss.left, tcss.top, width, height);

			if (boundary) {
				switch (boundary) {
					case 'x':
						if (tmppos.length == 2) {
							position = tmppos[0] + "-" + (tcss.left < 0 ? 'left' : 'right');
						} else {
							position = tcss.left < 0 ? 'right' : 'left';
						}
						break;

					case 'y':
						if (tmppos.length == 2) {
							position = (tcss.top < 0 ? 'bottom' : 'top') + '-' + tmppos[1];
						} else {
							position = (tcss.top < 0 ? 'bottom' : 'top');
						}
						break;

					case 'xy':
						if (tmppos.length == 2) {
							position = (tcss.top < 0 ? 'bottom' : 'top') + '-' + (tcss.left < 0 ? 'left' : 'right');
						} else {
							position = tcss.left < 0 ? 'right' : 'left';
						}
						break;
				}

				tmppos = position.split('-');

				$.extend(tcss, variants[tmppos[0]]);

				if (tmppos.length == 2) tcss.left = (tmppos[1] == 'left') ? (pos.left) : ((pos.left + pos.width) - width);
			}

			tcss.left -= $('body').position().left;

			console.log(tcss);

			tooltipdelay = setTimeout(function () {

				var classList = [
					 'api_tooltip',
					 'api_tooltip_theme_' + options.theme,
					 'api_tooltip_type_' + options.type,
					 'api_tooltip_' + position.replace('-','_'),
						options.cls,
				];

				//$tooltip.css(tcss).attr('class', ['uk-tooltip', 'uk-tooltip-'+position, $this.options.cls].join(' '));
				$tooltip
					.css(tcss)
					.attr('class', classList.join(' '));

				$tooltip.addClass(options.activeClass);
				//$tooltip.css({opacity: 0, display: 'block'}).addClass($this.options.activeClass).animate({opacity: 1}, parseInt($this.options.animation, 10) || 400);

				tooltipdelay = false;

				// close tooltip if element was removed or hidden
				checkIdle = setInterval(function () {
					if (!element.is(':visible'))
						$tooltip.css({opacity: 0}).removeClass(options.activeClass);
				}, 150);

			}, parseInt(options.delay, 10) || 0);

			/*$tooltip
			.addClass(options.pos)
			.css(variants[options.pos])
			.animate({top: '-=10', opacity: 1}, 50);
			//.animate({top: '+=10', opacity: 1}, 50);
			//.animate({top: variants[options.pos].top + 10, opacity: 1}, 50);*/
		},
		hide: function ($tooltip, options) {

			//if (tooltipdelay) clearTimeout(tooltipdelay);
			//if (checkIdle)  clearInterval(checkIdle);

			//$tooltip.stop();

			if ($tooltip) {
				$tooltip.stop().css({opacity: 0}).removeClass(options.activeClass);
				//$tooltip.remove();
			}
		}
	};

	$.fn.apiTooltip = function (method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Error! Method "' + method + '" not found in plugin $.fn.apiTooltip');
		}
	};

})(jQuery);