(function ($) {
	$.Redactor.prototype.scriptbuttons = function () {
		return {
			init: function () {
				var sup = this.button.add('superscript', 'Superscript');
				var sub = this.button.add('subscript', 'Subscript');

				this.button.addCallback(sup, this.scriptbuttons.formatSup);
				this.button.addCallback(sub, this.scriptbuttons.formatSub);

				// Set icons
				this.button.setIcon(sup, '<i class="re-icon-sup"></i>');
				this.button.setIcon(sub, '<i class="re-icon-sub"></i>');
			},
			formatSup: function () {
				this.inline.format('sup');
			},
			formatSub: function () {
				this.inline.format('sub');
			}
		};
	};
})(jQuery);