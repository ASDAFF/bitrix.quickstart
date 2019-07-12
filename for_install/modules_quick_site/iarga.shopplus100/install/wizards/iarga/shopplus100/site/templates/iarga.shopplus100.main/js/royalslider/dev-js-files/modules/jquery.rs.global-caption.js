(function($) {
	/**
	 *
	 * RoyalSlider global caption module
	 * @version 1.0:
	 * 
	 */ 
	$.extend($.rsProto, {
		_initGlobalCaption: function() {
			var self = this;
			if(self.st.globalCaption) {
				self.ev.on('rsAfterInit', function() {
					self.globalCaption = $('<div class="rsGCaption"></div>').appendTo(self.slider);
					setCurrCaptionHTML();
				});
				self.ev.on('rsBeforeAnimStart' , function() {
					setCurrCaptionHTML();
				});
				function setCurrCaptionHTML() {
					self.globalCaption.html(self.currSlide.caption);
				}
			}
		}
	});
	$.rsModules.globalCaption = $.rsProto._initGlobalCaption;
})(jQuery);
