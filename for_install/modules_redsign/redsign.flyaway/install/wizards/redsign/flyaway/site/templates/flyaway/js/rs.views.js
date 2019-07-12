!function(global) {
  'use strict';

  function Views(elem, params) {
    this.$element = jQuery(elem);
    this.params = params || {};

    this.classActive = this.params.classActive || 'js-views-active';
    this.classReady = this.params.classReady || 'js-views-ready';

    this.__construct();
  };

    Views.prototype.__construct = function __construct() {
      this.$window = jQuery(window);
      this.$switcher = this.$element.find('.js-views-switcher');
			this.length_list = this.$switcher.length;

      this._init();
    };

    Views.prototype._init = function _init() {
      var _this = this;

      this.$window.on('resize.js-views', function(e){
        _this.update.apply(_this, []);
      });

      this.$switcher.on('click.js-views', function(e){
        _this.active.apply(_this, [jQuery(this)]);
      });
			
      this.update();

      this._ready();
    };

    Views.prototype._ready = function _ready() {
      this.$element
        .addClass('js-views-ready')
        .addClass(this.classReady);

      this.$element.trigger('jsviewsready', this);
    };

    Views.prototype.update = function update() {
			var index = this.$switcher
										.filter('.' + this.classActive)
										.data('index');

			if (this.length_list == index && this.$switcher.filter(':visible').length <= 1 ) {
				this.$switcher.removeClass(this.classActive);
				this.$switcher.first().addClass(this.classActive);
			}
    };
		
    Views.prototype.active = function active($elem) {
			this.$switcher.removeClass(this.classActive);			
			$elem.addClass(this.classActive);
    };
  /*--/Views--*/

  global.Views = Views;
}(this);
