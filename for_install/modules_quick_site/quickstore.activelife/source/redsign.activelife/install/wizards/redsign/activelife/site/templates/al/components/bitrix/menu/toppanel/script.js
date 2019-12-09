;(function ($, window, document, undefined) {

  var pluginName = "slineTopMenu";


  function Menu(element, options) {
    this.options = $.extend({}, Menu.defaults, options);
    this.settings = $.extend({}, this.defaults);
    this.$element = $(element);
    this._items = [];
    this._more = [];

    this.setup();
    this.init();
  }

  Menu.defaults = {
    initedClass: 'inited',
    refreshClass: 'refreshing',

    itemElement: 'li',
    itemClass: 'rmenu__item',
    subMenuClass: 'rmenu__sub',
    moreClass: 'rmenu__more',
    moreMenuElement: 'ul',
    moreMenuClass: 'rmenu__sub',
    toggleClass: 'rmenu__toggle',
    moreInner: '• • •',
  };

  Menu.prototype.init = function () {

    this.$element.children(':not(.' + this.settings.moreClass + ')').filter(function () {
      return this.nodeType === 1;
    }).each($.proxy(function (index, item) {
      this._items.push($(item));
    }, this));
    
    this.$more = this.$element.children('.' + this.settings.moreClass);
		if (!this.$more.length) {
			this.$more = $('<' + this.settings.itemElement + '/>', {
				class: this.settings.itemClass + ' ' + this.settings.moreClass,
				style: 'display:none',
				html: this.settings.moreInner
			}).prependTo(this.$element);
		}

		this.$moreMenu = this.$more.children('.' + this.settings.moreMenuClass);
		if (!this.$moreMenu.length) {
			this.$moreMenu = $('<' + this.settings.moreMenuElement + '/>', {
				class: this.settings.moreMenuClass,
			}).appendTo(this.$more);
		}

    if (this.$element.is(':visible')) {
			this.refresh();
		}

    this.listenEvents();

    this.$element.addClass(this.settings.initedClass);
  };

  Menu.prototype.refresh = function() {

    this.setup();

    this.$element.addClass(this.options.refreshClass);

    this.update();

    this.$element.removeClass(this.options.refreshClass);
  };

  Menu.prototype.update = function () {

      //menuWidth = this.$element.parent().outerWidth() - 100,

    if (this.settings.dropdown) {
      this.$element.addClass('hml_menu-drop');
      this._more = this._items;
    } else {
      this.$element.removeClass('hml_menu-drop');
      this.menuWidth = this.$element.innerWidth();
/*
      this.$element.siblings().filter(function () {
        return this.nodeType === 1;
      }).each($.proxy(function (index, item) {
        this.menuWidth -= $(item).outerWidth(true);
      }, this));
*/
      if (this._more.length ) {
        for (var i in this._more) {
          this._more[i].hide();
        }
        this.$element.html(this._items.concat([this.$more]));
      }

      this._more = [];

      var width = 0;

      for (var i in this._items) {
        itemWidth = this._items[i].outerWidth() +
          parseInt(this._items[i].css('margin-left')) +
          parseInt(this._items[i].css('margin-right'));

        if (this.menuWidth < width + itemWidth) {
          this._more.push(this._items[i].show());
        } else {
          width += itemWidth;
          this._items[i].show();
        }
      }
    }

    if (this._more.length > 0) {
      itemWidth = this.$more.outerWidth() +
        parseInt(this.$more.css('margin-left')) +
        parseInt(this.$more.css('margin-right'));

      if (this.menuWidth < width + itemWidth) {
        this._more.unshift(this._items[this._items.length - this._more.length - 1]);
      }

      this.$more.show();
      this.$moreMenu.html(this._more);
    } else {
      this.$more.hide();
      this.$moreMenu.html('');
    }
  };

  Menu.prototype.setup = function () {
    var viewport = this.viewport(),
      overwrites = this.options.responsive,
      match = -1,
      settings = null;

    if (!overwrites) {
      settings = $.extend({}, this.options);
    } else {

      $.each(overwrites, function (breakpoint) {
        if (breakpoint <= viewport && breakpoint > match) {
          match = Number(breakpoint);
        }
      });

      settings = $.extend({}, this.options, overwrites[match]);

      delete settings.responsive;
    }

    if (this.settings === null || this._breakpoint !== match) {
      this._breakpoint = match;
      this.settings = settings;
    }
  };

   Menu.prototype.listenEvents = function () {
      
    var ctx = this;
    
    $(window).resize($.proxy(ctx.refresh, ctx));
  };

  Menu.prototype.viewport = function() {
    var width;
    if (window.innerWidth) {
      width = window.innerWidth;
    } else if (document.documentElement && document.documentElement.clientWidth) {
      width = document.documentElement.clientWidth;
    } else {
      throw 'Can not detect viewport width.';
    }
    return width;
  };

  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Menu(this, options));
      }
    });
  }

})(jQuery, window, document);

$(document).ready(function () {
  $(".menu_top").slineTopMenu({
    dropdown: true,
    itemClass: 'menu_top__item',
    moreClass: 'hml_menu__more',
    moreMenuClass: 'menu_top__sub',
    responsive: {
      768: {
        dropdown: false
      }
    }
  });
});