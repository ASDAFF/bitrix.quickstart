/*global jQuery,require*/
/**
* Баннеры
* @author ALFA Systems
**/
(function ($, window) {
    "use strict";
    var pluginName = "redsignBanners",
        namespace = "rs.banners.",
        defaultOptions = {
          height: 320,
          breakpoints: {
            large: 1024,
            medium: 768,
            small: 480,
            xsmall: 320
          },
          isAdjustBlocks: true,
          isAutoAdjustHeight: false,
          heightFactor: 2.2,
          heightAdjustStart: 'medium',
          sliderAdapter: 'owlAdapter'
        };
   function debounce(func, wait) {
        var timeout;
        return function() {
            var args = arguments,
                context = this;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }
    /**
    * @constructor
    **/
    function RedsignBanners(element, options) {
        this.$element = $(element);
        this.options = $.extend(defaultOptions, options);
        this.adapters = $.redsignBanners.adapters;
        this.sliderAdapter = null;
        this.init();
        this.listenEvents();
    }
    $.extend(RedsignBanners.prototype, {
        init: function () {
            var base = this;
            if(!this.adapters[this.options.sliderAdapter]) {
                console.warn("I can`t find adapter " + this.options.sliderAdapter);
                return;
            }
            this.sliderAdapter = new this.adapters[this.options.sliderAdapter](this);
            this.loadImages()
                .then(function() {
                    base.sliderAdapter.initSlider();
                },
                function() {
                    console.error("Images wasn`t load");
                });
        },
        resize: function() {
            this.sliderAdapter.resize();
        },
        setHeight: function(height) {
            height = height || this.getAdjustHeight();
            this.$element.css("height", height);
            this.$element.find(".rs-banners_banner").css("height", height);
            this.resize();
        },
        getAdjustHeight: function() {
            var height = this.options.height,
                windowHeight = $(window).width();
            if(windowHeight < this.options.breakpoints[this.options.heightAdjustStart]) {
                height = $(window).width() / this.options.heightFactor;
            }
            return height;
        },
        adjustBlocks: function() {
            var $banners = $(".rs-banners_banner");
            $banners.each($.proxy(function(key, banner) {
                var $infoBlocks = $(banner).find(".rs-banners_info"),
                    $infoBlocksChildren = $infoBlocks.children("div, a"),
                    i,
                    $element,
                    bannersHeight = this.$element.find(".rs-banners_banner:eq(0)").outerHeight(),
                    elementPositionWithHeight;
                if($infoBlocksChildren.length < 1) {
                    return;
                }
                $infoBlocksChildren.css("display", "inline-block");
                for(i = $infoBlocksChildren.length - 1; i >= 0; --i) {
                    $element = $($infoBlocksChildren[i]);
                    elementPositionWithHeight = $element.position().top + $element.outerHeight() + parseInt($element.css("marginTop"), 10) + 5;
                    if(bannersHeight - elementPositionWithHeight < 0) {
                        $element.hide();
                    }
                }
            }, this));
            this.trigger("adjustblocks");
        },
        update: function() {
            if(this.options.isAutoAdjustHeight) {
                this.setHeight(); 
            }
            if(this.options.isAdjustBlocks) {
                this.adjustBlocks();
            }
        },
        loadImages: function() {
            var promises = [];
            this.$element.find("[data-img-src]").each(function(e) {
                var src = $(this).data('img-src');
                if(!src) {
                    return;
                }
                promises.push(
                    $.Deferred(function(promise) {
                        $('<img>')
                          .attr('src', src)
                          .load(function() {
                              promise.resolve();
                          });
                    })
                );
            });
            return $.when.apply($, promises).then($.proxy(function() {
                this.trigger('images:load');
            }, this));
        },
        refresh: function() {
            debounce($.proxy(function() {
                this.update();
            }, this), 250)();
        },
        playVideo: function($elem) {
            $elem
              .parents(".rs-banners_banner")
              .find(".rs-banners_wrap")
              .css("visibility", "hidden");
            $elem.data('play', true);
            $elem.find('video').each(function(key, item) { 
                if(typeof item.play === 'function') {
                    item.play();
                }
            });
        },
        stopVideo: function($elem) {
            $elem
                  .parents(".rs-banners_banner")
                  .find(".rs-banners_wrap")
                  .css("visibility", "visible");
            $elem.data('play', false);
            $elem.find('video').each(function(key, item) {
                if(typeof item.pause === 'function') {
                    item.pause();
                }
            });
        },
        listenEvents: function() {
            var base = this,
                events = [
                  'resize'
                ];
            events.forEach(function(event) {
                base.$element.on(namespace + event, function(e, selector, data) {
                    if($.isFunction(base[event])) {
                        base[event].apply(base, data);
                    }
                });
            });          
            $(document).on('click', '.rs-banners_video-play', function() {
                var $elem = $(this).siblings('.rs-banners_video');
                console.log($elem);
                if(!$elem.data('play')) {
                    base.playVideo($elem);
                } else {
                    base.stopVideo($elem);
                }
            });
        },
        trigger: function(name) {
            window.setTimeout($.proxy(function() {
                this.$element.trigger(namespace + name);
            }, this));
        }
    });
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new RedsignBanners(this, options));
            }
        });
    };
}(jQuery, window));
/*global jQuery*/
/**
* Owl адаптер для баннеров
* @author ALFA Systems
**/
(function ($) {
    "use strict";
    $.redsignBanners = $.redsignBanners || {};
    $.redsignBanners.adapters = $.redsignBanners.adapters || {};
    var defaultOptions = {};
    /**
    * @constructor
    **/
    function OwlAdapter(core) {
        this.core = core;
        this.slider = {};
        this.sliderOptions = this.convertOptions();
        this.init();
    }
    $.extend(OwlAdapter.prototype, {
        init: function() {
            console.info("Owl Adapter");
            this.triggerEvents();
        },
        initSlider: function() {
            var $slider = this.core.$element.owlCarousel(this.sliderOptions);
            this.slider = $slider.data('owl.carousel'); 
        },
        convertOptions: function() {
            return $.extend(defaultOptions, this.core.options.params);
        },
        resize: function() {
            this.slider.invalidate('width');
            this.slider.refresh();
        },
        triggerEvents: function() {
            this.core.$element.on("refreshed.owl.carousel", $.proxy(this.core.refresh, this.core));
        }
    });
    $.redsignBanners.adapters.owlAdapter = OwlAdapter;
}(jQuery));
/*global jQuery*/
/**
* Slick адаптер для баннеров
* @author ALFA Systems
**/
(function ($) {
    "use strict";
    $.redsignBanners = $.redsignBanners || {};
    $.redsignBanners.adapters = $.redsignBanners.adapters || {};
    var defaultOptions = {};
    /**
    * @constructor
    **/
    function SlickAdapter(core) {
        this.core = core;
        this.slider = {};
        this.sliderOptions = this.convertOptions();
        this.init();
    }
    $.extend(SlickAdapter.prototype, {
        init: function() {
            console.warn("Slick adapter");
        },
        initSlider: function() {
            var $slider = this.core.$element.slick(this.sliderOptions);
            this.slider = $slider.slick('getSlick'); 
        },
        convertOptions: function() {
            var options = {},
                i,
                optionsRename = {
                  items: 'slidesToShow',
                  loop: 'infinite',
                  nav: 'arrows',
                  smartSpeed: 'speed'
                };
            for(i in this.core.options.params) {
                if(this.core.options.params.hasOwnProperty(i)) {
                    if(optionsRename[i]) {
                        options[optionsRename[i]] = this.core.options.params[i];
                    } else {
                        options[i] = this.core.options.params[i];
                    }
                }
            }
            return $.extend(defaultOptions, options);
        },
        resize: function() {
            this.slider.resize();
        }
    });
    $.redsignBanners.adapters.slickAdapter = SlickAdapter;
}(jQuery));
