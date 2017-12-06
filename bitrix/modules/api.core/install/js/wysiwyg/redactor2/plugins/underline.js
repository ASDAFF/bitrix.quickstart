(function ($) {
	$.Redactor.prototype.underline = function () {
		return {
			init: function () {
				var button = this.button.addAfter('italic', 'underline', 'U');
				this.button.addCallback(button, this.underline.format);

				// Set icon
				this.button.setIcon(button, '<i class="re-icon-underline"></i>');
			},
			format: function () {
				this.inline.format('u');
			}
		};
	};

})(jQuery);