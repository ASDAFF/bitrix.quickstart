(function($) {
	/**
	 *
	 * RoyalSlider active class module 
	 * @version 1.0:
	 * 
	 */ 
	$.rsProto._initActiveClass = function() {
		var updClassTimeout,
			self = this;
		if(self.st.addActiveClass) {
			self.ev.on('rsBeforeMove', function() {
				updClass();
			});
			self.ev.on('rsAfterInit', function() {
				updClass();
			});
			function updClass() {
				if(updClassTimeout) {
					clearTimeout(updClassTimeout);
				}
				updClassTimeout = setTimeout(function() {
					if(self._oldHolder) self._oldHolder.removeClass('rsActiveSlide');
					if(self._currHolder) self._currHolder.addClass('rsActiveSlide');
					updClassTimeout = null;
				}, 50);
			}
		}
	};
	$.rsModules.activeClass = $.rsProto._initActiveClass;
})(jQuery);
