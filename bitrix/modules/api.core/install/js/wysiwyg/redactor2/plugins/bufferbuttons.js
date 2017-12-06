(function ($) {
	$.Redactor.prototype.bufferbuttons = function () {
		return {
			init: function () {
				var undo = this.button.addFirst('undo', 'Undo');
				var redo = this.button.addAfter('undo', 'redo', 'Redo');

				this.button.addCallback(undo, this.buffer.undo);
				this.button.addCallback(redo, this.buffer.redo);

				this.button.setIcon(undo, '<i class="re-icon-undo"></i>');
				this.button.setIcon(redo, '<i class="re-icon-redo"></i>');
			}
		};
	};
})(jQuery);