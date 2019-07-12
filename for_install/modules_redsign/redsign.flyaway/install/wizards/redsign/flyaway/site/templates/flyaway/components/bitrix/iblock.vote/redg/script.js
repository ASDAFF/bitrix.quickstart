!function(global) {
  'use strict';

  function RatingDecor(elem, params) {
    this.$element = jQuery(elem);
    this.params = params || {};

    this.onInit = this.params.onInit || null;
    this.classReady = this.params.classReady || 'JS-RatingDecor-ready';
    this.classActive = this.params.classActive || 'rating-item_active';

    this.__construct();
  };

  RatingDecor.prototype.__construct = function __construct() {
    this.$item = this.$element.find('.JS-RatingDecor-Item');

    this._init();
  };

  RatingDecor.prototype._init = function _init() {
    var _this = this;

    if( jQuery.isFunction(this.onInit) ){
      this.onInit.apply(window, []);
    }

    this.$item.on('mouseenter.JS-RatingDecor', function(e, data) {
      _this._showClass.apply(_this, [jQuery(this)]);
    });

    this.$item.on('mouseleave.JS-RatingDecor', function(e, data) {
      _this._hideClass.apply(_this, [jQuery(this)]);
    });

    this._ready();
  };

  RatingDecor.prototype._ready = function _ready() {
    this.$element
      .addClass('JS-RatingDecor-ready')
      .addClass(this.classReady);
  };

  RatingDecor.prototype._showClass = function _showClass($object) {
    if (!$object.hasClass(this.classActive)) {
      $object
      .addClass(this.classActive)
          .prevAll()
          .addClass(this.classActive);
    }
  };

  RatingDecor.prototype._hideClass = function _hideClass($object) {
    if ($object.hasClass(this.classActive)) {
      $object
      .removeClass(this.classActive)
          .prevAll()
          .removeClass(this.classActive);
    }
  };
  /*--/RatingDecor--*/

  global.RatingDecor = RatingDecor;
}(this);
