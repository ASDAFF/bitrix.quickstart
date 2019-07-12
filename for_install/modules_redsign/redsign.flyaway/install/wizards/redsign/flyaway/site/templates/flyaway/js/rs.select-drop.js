!function(global) {
  'use strict';

  function Select(elem, params) {
    this.$element = jQuery(elem);
    this.params = params || {};

    this.onSetValue = this.params.onSetValue || null;
    this.onSetFocus = this.params.onSetFocus || null;
		this.onDisabledField = this.params.onDisabledField || null;
		this.onUndisabledField = this.params.onUndisabledField || null;
    this.classActive = this.params.classActive || 'select-active';
    this.classUndisabled = this.params.classUndisabled || 'field-undisabled';
    this.classReady = this.params.classReady || 'js-select-ready';

    this.__construct();
  };

  Select.prototype.__construct = function __construct() {
    this.$document = jQuery(document);
    this.$label = this.$element.find('.js-select-label');
    this.$labelMore = this.$element.find('.js-select-labelmore');
    this.$field = this.$element.find('.js-select-field');
    this.$input = this.$element.find('.js-select-input');

    this._init();
  };

  Select.prototype._init = function _init() {
    var _this = this;

    this.$label.on('click.js-select', function() {
      _this._setValue.apply(_this, [jQuery(this)]);
    });

    this.$labelMore.on('click.js-select', function() {
      _this._setFocus.apply(_this, []);
    });

    this.$input.on('blur.js-select', function() {
      _this._removeFocus.apply(_this, []);
    });

    this.$document.on('click.js-select', function(e) {
      _this._close.apply(_this, [e]);
    });

    this._ready();
  };

  Select.prototype._ready = function _ready() {
    this.$element
      .addClass('js-select-ready')
      .addClass(this.classReady);
  };

  Select.prototype._disabledField = function _disabledField() {
    this.$field.removeClass(this.classUndisabled);
		
    if (jQuery.isFunction(this.onDisabledField)) {
      this.onDisabledField.apply(window, [this.$field]);
    }
  }

  Select.prototype._undisabledField = function _undisabledField() {
    this.$field.addClass(this.classUndisabled);
		
    if (jQuery.isFunction(this.onUndisabledField)) {
      this.onUndisabledField.apply(window, [this.$field]);
    }
  }

  Select.prototype._close = function _close(e) {
    if ((!this.$element.is(e.target)) && (this.$element.has(e.target).length === 0)) {
      this.$element
        .removeClass(this.classActive);
    }
  }

  Select.prototype._setActive = function _setActive() {
    if (!this.$element.hasClass(this.classActive)) {
      this.$element
        .addClass(this.classActive);
    }
  }

  Select.prototype._setValue = function _setValue($label) {
    var textLabel = {};
    if($label.parent().hasClass('disabled')){
        return false;
    }

    textLabel = $label.text();

    if (this.$input.is(':text')) {
      this.$input.val(textLabel).change();
    } else {
      this.$input.text(textLabel);
    }

    this._disabledField();

    this._setActive();

    if (jQuery.isFunction(this.onSetValue)) {
      this.onSetValue.apply(window, []);
    }
  };

  Select.prototype._setFocus = function _setFocus() {
    this.$input
      .focus()
      .select();

    this._undisabledField();

    this._setActive();

    if (jQuery.isFunction(this.onSetFocus)) {
      this.onSetFocus.apply(window, []);
    }
  };
	
  Select.prototype._removeFocus = function _removeFocus() {
		this._disabledField();
  };
  /*--/Select--*/

  global.Select = Select;
}(this);
