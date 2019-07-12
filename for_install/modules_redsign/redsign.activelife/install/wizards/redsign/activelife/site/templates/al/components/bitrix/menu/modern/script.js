;(function ($, window, document, undefined) {
    "use strict";

    var pluginName = "SlineMenu";
    var defaultOptions = {
        cssPrefix: '.js-mm__',
        columns: 3,
        productColumnWidth: 270,
        transfromWidth: 768,
    };

    function Menu(element, options) {
        this.$element = $(element);
        this.options = $.extend(defaultOptions, options);

        this.getSelectors();
        this.getElements();
        this.init();
    }

    $.extend(Menu.prototype, {

        init: function() {

            this.hideNotFitElements();
            this.listenEvents();

            if(this.getWindowWidth() >= this.options.transfromWidth) {
                this.grid();
                this.alignSubmenu();
            }


            this.setReady();
        },

        resize: function() {

            this.setUnready();

            if(this.getWindowWidth() >= this.options.transfromWidth) {

                this.hideNotFitElements();
                this.breakGrid();
                this.grid();
                this.alignSubmenu();

            } else {
                this.breakGrid();
            }

            this.setReady();

        },

        alignSubmenu: function() {
            var $rootItems = this.$rootItems,
                containerWidth = this.$itemsContainer.parent().outerWidth();
				

            $rootItems.children(this.selectors.submenu).removeAttr('style').each($.proxy(function(key, item) {
                if($(item).position().left + $(item).outerWidth() > containerWidth) {
                    $(item).css('right', 0)
                }
            }, this));
        },

        hideNotFitElements: function() {

            var $rootItems = this.$rootItems,
                $moreContainer = this.$element.find(this.selectors.moreContainer),
                availableWidth = this.$itemsContainer.width(),
                filledWidth = this.$element.find(this.selectors.moreBtn).outerWidth() - 15,
                lastIndex = 0;

            $rootItems.removeClass('is-desktop-hide').each(function(key, item) {
                var $item = $(item);

                if(filledWidth + $item.outerWidth() <= availableWidth) {
                    filledWidth += $item.outerWidth();
                } else {
                    lastIndex = key - 1;
                    return false;
                }
            });

            if(lastIndex > 0) {
                $rootItems.filter(":gt(" + lastIndex + ")").addClass('is-desktop-hide');
                $moreContainer.html($rootItems.filter(":gt(" + lastIndex + ")").children("a").clone());
                this.showMoreBtn();
            } else {
                this.hideMoreBtn();
            }
        },

        getWindowWidth: function() {
            return window.innerWidth || (document.documentElement.clientWidth || 0);
        },

        hideMoreBtn: function() {
            this.$element.find(this.selectors.moreBtn).css('visibility', 'hidden');
        },

        showMoreBtn: function() {
            this.$element.find(this.selectors.moreBtn).css('visibility', 'visible');
        },

        setReady: function() {
            this.$element.addClass('is-ready');
        },

        setUnready: function() {
            this.$element.removeClass('is-ready');
        },

        toggleMenu: function() {
            this.$itemsContainer.toggleClass('is-open');
        },

        toggleSubmenu: function(btn) {
            $(btn).parent().toggleClass('is-sub-open');
        },

        grid: function() {
            var $rootItems = this.$rootItems;
            $rootItems.children(this.selectors.submenu).each($.proxy(this.gridSubMenu, this));
        },


        gridSubMenu: function(key, submenu) {
            var $items = $(submenu).children(this.selectors.item),
                columns = this.createColumns(),
                i;

            $items.each($.proxy(function(key, item) {
                var columnIndex = getIndexLowestColumn(columns),
                    $item = $(item),
                    itemHeight = $item.outerHeight(),
                    itemWidth = $item.outerWidth();

                columns[columnIndex].height += itemHeight;
                columns[columnIndex].width = columns[columnIndex].width < itemWidth ? itemWidth : columns[columnIndex].width;
                columns[columnIndex].$el.append($(item));

            }, this));

            if($(submenu).children(this.selectors.product).length > 0) {
                var productColumn = columns[columns.length] = this.createColumn(),
                    $product = $(submenu).children(this.selectors.product);

                productColumn.width = $product.outerWidth();
                productColumn.$el.append($product);
            };

            $(submenu).html(columns.map(function(col) { return col.$el.css({width: col.width})}));
        },

        createColumns: function(columnsCount) {
            var columns = [],
                i;

            columnsCount = columnsCount || this.getOption('columns');

            for(i = 0; i < columnsCount; i++) {
                columns[i] = this.createColumn();
            }

            return columns;
        },

        createColumn: function() {
            return {
                height: 0,
                width: 0,
                $el: $('<div>')
                        .addClass(this.selectors.column.slice(1, this.selectors.column.length))
                        .css({'display':'inline-block', 'vertical-align': 'top'})
            };
        },

        breakGrid: function() {
            var $rootItems = this.$rootItems;

            this.$element.find(this.selectors.column).children().unwrap();

            $rootItems.children(this.selectors.submenu).each($.proxy(function(i, subitems) {
                var $subitems = $(subitems);

                $subitems.children(this.selectors.item).sort(function(a, b) {
                    return $(a).data('index') >= $(b).data('index') ? 1: -1;
                }).appendTo($subitems);

            }, this));
        },

        getSelectors: function() {
            this.selectors = {};

            this.selectors.itemsContainer = this.options.cssPrefix + 'items';
            this.selectors.rootItems = this.options.cssPrefix + 'root-item';
            this.selectors.item = this.options.cssPrefix + 'item';
            this.selectors.moreBtn = this.options.cssPrefix + 'more-btn';
            this.selectors.moreContainer = this.options.cssPrefix + 'more-container';
            this.selectors.toggleButton = this.options.cssPrefix + 'toggle-button';
            this.selectors.toggleSubmenu = this.options.cssPrefix + 'toggle-submenu';
            this.selectors.submenu = this.options.cssPrefix + 'subitems';
            this.selectors.column = this.options.cssPrefix + 'column';
            this.selectors.product = this.options.cssPrefix + 'product';
        },

        getElements: function() {
            this.$rootItems = this.$element.find(this.selectors.rootItems);
            this.$itemsContainer = this.$element.find(this.selectors.itemsContainer);
        },

        listenEvents: function() {

            var _this = this;

            $(window).on('resize', this.debounce(this.resize, 100, this));
            this.$element.find(this.selectors.toggleButton).on('click', $.proxy(this.toggleMenu, this));
            this.$element.find(this.selectors.toggleSubmenu).on('click', function() {
                _this.toggleSubmenu(this);
            });
        },

        getOption: function(option) {
            var optionValue = this.options[option];

            var windowWidth = this.getWindowWidth(),
                breakpoint;

            for(breakpoint in this.options.responsive) {
                if(breakpoint <= windowWidth && this.options.responsive[breakpoint][option]) {
                    optionValue = this.options.responsive[breakpoint][option];
                }
            }

            return optionValue;
        },

        debounce: function(func, wait, context) {
            var timeout;

            return function() {
                var args = arguments;
                context = context || this;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }


    });

    function getIndexLowestColumn(columns) {
        var index = 0,
            minColumnHeight = columns[index].height;

        columns.forEach(function(col,i) {
            if(minColumnHeight > col.height) {
                minColumnHeight = col.height;
                index = i;
            }
        });

        return index;
    }

    $.fn[pluginName] = function (options) {
      return this.each(function () {
        if (!$.data(this, pluginName)) {
          $.data(this, pluginName, new Menu(this, options));
        }
      });
  };

})(jQuery, window, document);

$(document).ready(function() {

    $(".js-modern-menu").SlineMenu({
        cssPrefix: '.js-mm__',
        columns: 3,
        productColumnWidth: 270,
        transfromWidth: 768,
        /**responsive: {
            1200: {
                columns: 4
            }
        }**/
    });

});
