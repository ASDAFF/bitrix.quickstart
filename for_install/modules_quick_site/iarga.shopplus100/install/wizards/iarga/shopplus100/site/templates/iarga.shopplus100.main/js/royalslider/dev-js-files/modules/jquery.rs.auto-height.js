(function($) {
	/**
	 *
	 * RoyalSlider auto height module
	 * @version 1.0.1:
	 * 
	 */ 
	$.extend($.rsProto, {
		_initAutoHeight: function() {
			var self = this;
			if(self.st.autoHeight) {
				var holder,
					tH,
					currHeight;
				self.slider.addClass('rsAutoHeight');
				self.ev.on('rsAfterInit', function() {
					setTimeout(function() {
						updHeight(false);
						setTimeout(function() {
							self.slider.append('<div id="clear" style="clear:both;"></div>');
							if(self._useCSS3Transitions) {
								self._sliderOverflow.css(self._vendorPref + 'transition', 'height ' + self.st.transitionSpeed + 'ms ease-in-out');
							}
						}, 16);
					}, 16);
				});
				self.ev.on('rsBeforeAnimStart', function() {
					updHeight(true);
				});
				self.ev.on('rsBeforeSizeSet' , function() {
					setTimeout(function() {
						updHeight(false);
					}, 16);
				});
				function updHeight(animate) {
					var slide = self.slides[self.currSlideId];
					holder = slide.holder;
					if(!slide.isLoaded) {
						self.ev.off('rsAfterContentSet.rsAutoHeight').on('rsAfterContentSet.rsAutoHeight', function(e, slideObject) {
							if(slide === slideObject) {
								updHeight();
							}
						});
						return;
					}
					if(holder) {
						tH = holder.height();
						if(tH !== 0 && tH !== currHeight) {
							self._wrapHeight = tH;
							if(self._useCSS3Transitions || !animate) {
								self._sliderOverflow.css('height', tH);
							} else {
								self._sliderOverflow.stop(true,true).animate({height: tH}, self.st.transitionSpeed);
							}
							
						}
					}
				}
			}
			
		}
	});
	$.rsModules.autoHeight = $.rsProto._initAutoHeight;
})(jQuery);

// Top secret module ^^
// (function($) {
// 	/**
// 	 *
// 	 * RoyalSlider auto hide nav module
// 	 * @version 0.9:
// 	 * 
// 	 */ 
// 	$.extend($.rsProto, {
// 		_initAutoHideControlNav: function() {
// 			var self = this;
// 			if(!self.hasTouch) {
// 				self.ev.one('rsAfterInit', function() {
// 					if(self._controlNav) {
// 						self._controlNav.addClass('rsHidden');

// 						var hoverEl = self.slider;
// 						hoverEl.one("mousemove.controlnav",function() {
// 							self._controlNav.removeClass('rsHidden');
// 						});

// 						hoverEl.hover(
// 							function() {
// 								self._controlNav.removeClass('rsHidden');
// 							},
// 							function() {
// 								self._controlNav.addClass('rsHidden');
// 							}
// 						);
// 					}
					
// 				});
// 			}
// 		}
// 	});
// 	$.rsModules.autoHideNav = $.rsProto._initAutoHideControlNav;
// })(jQuery);
