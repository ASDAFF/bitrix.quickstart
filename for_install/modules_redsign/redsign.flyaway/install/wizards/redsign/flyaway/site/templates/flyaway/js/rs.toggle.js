!function(global) {
  'use strict';

  function Toggle(elem, params) {
    this.$element = jQuery(elem);
    this.params = params || {};

    this.__construct();
  };

	Toggle.prototype.__construct = function __construct() {
            this.$switcher = this.$element.find('.js-toggle-switcher');

            this.onevent = this.params.onevent || '';		
            this.unevent = this.params.unevent || '';	
            this.classActive = this.params.classActive || 'active';

            this._init();
	};

	Toggle.prototype._init = function _init() {
            var _this = this;

            this.$switcher.on(this.unevent + '.js-toggle', function(e){
                    _this.removeClass.apply(_this, []);
            });

            this.$switcher.on(this.onevent + '.js-toggle', function(e){
                    _this.addClass.apply(_this, []);
            });

            this.ready();
	};

	Toggle.prototype.ready = function ready() {
            this.$element
                .addClass('js-toggle-ready')
                .addClass(this.classReady);

            this.$element.trigger('jscompareready', this);
	};

	Toggle.prototype.addClass = function addClass() {
            if (!this.$element.hasClass(this.classActive)) {
                this.$element.addClass(this.classActive);
            }
	};
	
	Toggle.prototype.removeClass = function removeClass() {
            this.$element.removeClass(this.classActive);
	};
  /*--/Toggle--*/
    global.Toggle = Toggle;
}(this);
