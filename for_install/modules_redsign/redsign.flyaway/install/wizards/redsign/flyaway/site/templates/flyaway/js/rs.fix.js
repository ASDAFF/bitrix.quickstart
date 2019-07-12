!function(global) {
  'use strict';

  function Fix(elem, params) {
    this.$element = jQuery(elem);
    this.params = params || {};

    this.classFixed = this.params.classFixed || 'js-fix-fixed';
    this.classReady = this.params.classReady || 'js-fix-ready';

    this.__construct();
  };

    Fix.prototype.__construct = function __construct() {
      this.$window = jQuery(window);
      this.$bar = this.$element.find('.js-fix-bar');
      this.position = {top: 0};

      this._init();

      this.$element.data('js-fix', this);
    };

    Fix.prototype._init = function _init() {
      var _this = this;

      this.$element.trigger('jsfixinit', this);

      this.$window
        .on('resize.js-fix', function(e){
          _this._update.apply(_this, []);
        })
        .on('scroll.js-fix', function(e){
          _this._update.apply(_this, []);
        });

      this._update();

      this._ready();
    };

    Fix.prototype._ready = function _ready() {
      this.$element
        .addClass('js-fix-ready')
        .addClass(this.classReady);

      this.$element.trigger('jsfixready', this);
    };

    Fix.prototype._start = function _start() {
      
    };

    Fix.prototype._reset = function _reset() {
       this.$element
        .removeClass('js-fix-fixed')
        .removeClass(this.classFixed);
    };

    Fix.prototype._update = function _update() {
      var positionScrollTop,
					widthElement;

      this.position.top = this.$element.offset().top;
      positionScrollTop = this.$window.scrollTop();

      if (positionScrollTop > this.position.top) {
       if (!this.$bar.hasClass(this.classFixed)) {
         this.$bar
          .addClass('js-fix-fixed')
          .addClass(this.classFixed);
       }
			 
			 widthElement = this.$element.width();
			 this.$bar.css('width', widthElement);
      } else {
       this.$bar
        .removeClass('js-fix-fixed')
        .removeClass(this.classFixed);
				
			 this.$bar.css('width', '100%');
      }
    };
  /*--/Fix--*/

  global.Fix = Fix;
}(this);
