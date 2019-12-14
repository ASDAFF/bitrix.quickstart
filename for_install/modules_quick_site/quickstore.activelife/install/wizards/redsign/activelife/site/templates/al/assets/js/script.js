;(function(window){

  if (window.rsSline) {
    return;
  }

  window.rsSline = function() {

    var app = this;
    this.offers = {};
    this.cartList = {};
    this.favoriteList = {};
    this.compareList = {};
    this.stocks = {};
    /*
    this.domClicked = [];
    this.iDomRefreshTimeout = 250;
    this.iDomRefreshDelay = 250;
    this.onReady = {};
    */
    this.fancyTimeout = 1000;
    this.ajaxTimeout = 0;
    this.ajaxTimeoutTime = 1000;
    this.ajaxExec = false;
    this.grid = {
      0: 0,
      xs: 480,
      sm: 768,
      md: 992,
      lg: 1220,
    };
    this.pageWidth = this.getPageWidth();
    this.pageHeight = this.getPageHeight();

    this.fancyOptions = {
      autoSize: true,
      openEffect : 'fade',
      closeEffect : 'fade',
      closeClick  : false,
      width: '80%',
      minWidth: 300,
      maxWidth : 1263,
      height: 'auto',
      maxHeight : '100%',
      fitToView: true,
      scrolling: 'visible',
      padding    : [25, 30, 25, 30],
      beforeLoad: function() {
        if (!!this.ajax.data) {
          this.ajax.data.rs_ajax__page = 'Y';
        }

        var ajaxData;
        if (!!this.element) {
          var ajaxData = this.element.data('insert_data');
        }

        if(window.appSLine.getPageWidth() <= window.appSLine.grid.xs) {

          if (ajaxData != undefined) {
            BX.localStorage.set('ajax_data', ajaxData);
          }
          window.location = this.element.data('realurl') ? this.element.data('realurl') : this.href;
          return false;
        } else {
            if (ajaxData != undefined) {
              this.ajax.data.ajax_data = ajaxData;
            }
        }
      },
      afterLoad: function(){
        var $fancyElement = this.element,
            $fancyInner = this.inner,
            //elementData = this.element != undefined ? $fancyElement.data('insert_data') : undefined;
            elementData = this.ajax.data != undefined && this.ajax.data.ajax_data != undefined
                ? BX.parseJSON(this.ajax.data.ajax_data)
                : undefined;
        if (
          elementData != undefined &&
          (typeof(elementData) == 'object')
        ) {
          setTimeout(function() {
            for (var fieldName in elementData) {
              $fancyInner.find('[name="' + fieldName + '"]').val(BX.util.htmlspecialcharsback(elementData[fieldName]));
            }
          }, 50);
        }
      },
      beforeShow: function(){
        /*
        var $nodes;
        $nodes = $('.fancybox-inner').find('.errortext');
        if ($nodes.length > 0) {
          $nodes.hide();
        }
        $nodes = $('.fancybox-inner').find('.notetext');
        if ($nodes.length > 0) {
          $nodes.hide();
        }
        */
      },
      afterClose: function(){
        if (RSAL_FancyReloadPageAfterClose) {
          window.location.reload();
        }
      },
      afterShow: function(){
        console.log('afterShow');
        appSLine.ready(this.inner);
      },
      tpl: {
        closeBtn: '<svg class="fancybox-item fancybox-close icon-close icon-svg" href="javascript:;" title="Close"><use xlink:href="#svg-close"></use></svg>',
        loading: '<div id="fancybox-loading" class="loading">\
          <div class="loading__in loading__1"></div>\
          <div class="loading__in loading__2"></div>\
          <div class="loading__in loading__3"></div>\
          <div class="loading__in loading__4"></div>\
          <div class="loading__in loading__5"></div>\
          <div class="loading__in loading__6"></div>\
          <div class="loading__in loading__7"></div>\
          <div class="loading__in loading__8"></div>\
        </div>'

      },
      helpers: {
          title: {
              type: 'inside',
              position : 'top'
          }
      }
    };

    this.owlOptions = {
      loop:true,
      nav:true,
      navClass:['owl-nav__prev js-click', 'owl-nav__next js-click'],
      navText:['<svg class="icon-left icon-svg"><use xlink:href="#svg-left"></use></svg>','<svg class="icon-right icon-svg"><use xlink:href="#svg-right"></use></svg>'],
      responsive: {
        0: {items: 2},

      },
      onInitialize: function () {
        if (this.$element.hasClass('grid-wrap') || this.$element.hasClass('js-grid')) {
          this._breakpoint = 0;
        }
      },

      onInitialized: function () {
        this.$element.addClass('owl-carousel');

        if (this.$element.closest('.fancybox-inner').length) {
          $.fancybox.update();
        }
      },
      onChanged : function () {
        this.settings.loop = (this._items.length <= this.settings.items) ? false : this.options.loop;
      },
    };

    this.owlOptions.responsive[this.grid.xs] = {
      items: 2
    };
    this.owlOptions.responsive[this.grid.sm] = {
      items: 3
    };
    this.owlOptions.responsive[this.grid.md] = {
      items: 3
    };
    this.owlOptions.responsive[this.grid.lg] = {
      items: 5
    };
  };

  sec = -1;
  rsSline.prototype.timer = function() {
    var iDateNow = BX.message('SERVER_TIME');
    sec++;
    iDateNow = parseInt(iDateNow) + sec;

    $('.js_timer').each(function(index){
      var $timerHtml = $(this),
          dataTimer = BX.parseJSON($timerHtml.data('timer')),
          iTimeLimit = dataTimer.DATE_TO - dataTimer.DATE_FROM,
          iTimeLeft = dataTimer.DATE_TO - iDateNow;

      if (iTimeLeft < 1 && dataTimer.AUTO_RENEWAL == 'Y') {
        for (lim = 0; lim < 200; lim++) {
          newdateTo = (lim * iTimeLimit + dataTimer.DATE_TO) - iDateNow;
          if (newdateTo > 0) {
            iTimeLeft = newdateTo;
            break;
          }
        }
      }
      if (iTimeLeft > 0) {
        var days = parseInt((iTimeLeft / (60 * 60 )) / 24);
        if (days < 10) {
          days = '0' + days;
        }
        days = days.toString();

        var hourse = parseInt(iTimeLeft / (60 * 60 ));
        var hours =  parseInt((iTimeLeft / (60 * 60 )) % 24);
        if (hours < 10) {
          hours = '0' + hours;
        }
        hours = hours.toString();

        var minutes = parseInt(iTimeLeft / (60)) % 60;
        if (minutes < 10)
        {
          minutes = '0' + minutes;
        }
        minutes = minutes.toString();
        var seconds = parseInt(iTimeLeft) % 60;
        if (seconds < 10) {
          seconds = '0' + seconds;
        }
        seconds = seconds.toString();

        var widthTimerPerc = false;

        if (!!dataTimer.DINAMICA_DATA) {
          if (dataTimer.DINAMICA_DATA == 'evenly') {
            widthTimerPerc = Math.floor(100-((iTimeLeft / iTimeLimit) * 100));
          } else {
            var prevPerc = false;
            var firstPerc = false;

            for (var timePerc in dataTimer.DINAMICA_DATA) {
              if (!prevPerc) {
                prevPerc = timePerc;
                firstPerc = timePerc;
              }
              if (prevPerc < hourse && hourse < timePerc) {
                widthTimerPerc = dataTimer.DINAMICA_DATA[timePerc];
                break;
              }
              prevPerc = timePerc;
            }
            if (!widthTimerPerc) {
              if (hourse > prevPerc) {
                widthTimerPerc = dataTimer.DINAMICA_DATA[prevPerc];
              } else if(hourse < prevPerc) {
                widthTimerPerc = dataTimer.DINAMICA_DATA[firstPerc];
              }
            }
          }

          if (widthTimerPerc) {
            $timerHtml.find('.js_timer-progress').text(widthTimerPerc + '%');
          }
        }
        /*else
        {
          widthTimerPerc = Math.floor((iTimeLeft / iTimeLimit) * 100);
          $timerHtml.find('.js_timer-progress').text(widthTimerPerc + '%');
        }*/

        var $days = $timerHtml.find('.js_timer-d');
        var $seconds = $timerHtml.find('.js_timer-s');
        if (days < 1) {
          if ($days.is(':visible')) {
            $days.parent().hide().next('.timer__cell').hide();
          }
          $seconds.text(seconds);
          if ($seconds.is(':hidden')) {
            $seconds.parent().show().prev('.timer__cell').show();
          }
        } else if(days > 0) {
          $days.text(days);
          if ($seconds.is(':hidden')) {
            $days.parent().show().next('.timer__cell').show();
          }
          if ($seconds.is(':visible')) {
            $seconds.parent().hide().prev('.timer__cell').hide();
          }
        }
        $timerHtml.find('.js_timer-H').text(hours);
        $timerHtml.find('.js_timer-i').text(minutes);
      } else {
        $timerHtml.css('display', 'none')
          .closest('.js-element').removeClass('da2 qb');
      }
    });
  }

  rsSline.prototype.setProductCart = function($productItem) {

    var offerId = parseInt($productItem.find('.js-product_id').val());

    if (!!this.cartList) {
      if (!!this.cartList[offerId]) {
         $productItem.addClass('is-incart');
      } else {
         $productItem.removeClass('is-incart');
      }
    }
  };

  rsSline.prototype.setProductCompare = function($productItem) {

    var offerId = parseInt($productItem.find('.js-product_id').val());

    if (!!this.compareList) {
      if (!!this.compareList[offerId]) {
         $productItem.find('.js-compare').addClass('checked');
      } else {
         $productItem.find('.js-compare').removeClass('checked');
      }
    }
  };

  rsSline.prototype.setProductFavorite = function($productItem) {
    var productId = $productItem.data('product-id');

    if (!!this.favoriteList) {
      if (!!this.favoriteList[productId]) {
         $productItem.find('.js-favorite').addClass('checked');
      } else {
         $productItem.find('.js-favorite').removeClass('checked');
      }
    }
  };

  rsSline.prototype.setProductItems = function(options) {
    var opt = $.extend({
        'wrap': '',
        'items': ''
        }, options),
        app = this;

    if (opt.items == '') {
      if (opt.wrap == '') {
        var $productItems = $('.js-product');
      } else {
        var $productItems = $(opt.wrap).find('.js-product');
      }
    } else {
      var $productItems = opt.items;
    }

    $productItems.each(function() {

      var $productItem = $(this);

      if (!!app.cartList) {
        app.setProductCart($productItem);
      }
      if (!!app.compareList) {
        app.setProductCompare($productItem);
      }
      if (!!app.favoriteList) {
        app.setProductFavorite($productItem);
      }
    });
  }

  rsSline.prototype.mobilePoppup = function() {
    if($(document).width() < 780) {
      $('.js-popup-ajax').removeClass('js-popup-ajax').addClass('fancymobilelink');

      $(document).on('click.fancymobilelink', 'a.fancymobilelink', function(){
        var data = $(this).data('insert_data');

        BX.localStorage.set('ajax_data', data);
      });
    } else {
      $(document).off('click.fancymobilelink');
    }
  }

  rsSline.prototype.gridInit = function(options) {
    var app = this,
        opt = $.extend({
          'wrap': '',
          'items': ''
        }, options);

    var sOwlSlider = '.js-catalog_slider';
      //sGridWrap = '.grid-wrap';
    if (1024 > this.pageWidth) {
      sOwlSlider += ', .js-grid';
      //sGridWrap += ', .js-grid';
    }
/*    else {
      $('.js-grid.owl-carousel').trigger('destroy.owl.carousel').removeClass('owl-carousel').removeAttr('style');
    }
*/
    if (1024 >= this.pageWidth) {
      sOwlSlider += ', .rs_set-default';
    } else {
      $('.rs_set-default.owl-carousel').trigger('destroy.owl.carousel').removeClass('owl-carousel').removeAttr('style');
    }

    /*
    var $gridWrap = $();
    if (!!opt.items) {
      $gridWrap = $(opt.items);
    } else {
      if (!!opt.wrap) {
        $gridWrap = $(opt.wrap).find('.js-grid');
      } else {
        $gridWrap = $(sGridWrap);
      }
    }

    $gridWrap.each(function() {
      var $wrap = $(this).show();
      if (parseInt($wrap.closest('.wrap').width()) > parseInt($wrap.width() + parseInt($wrap.css('marginLeft')))) {
        $wrap.removeClass('grid-wrap-full');
      } else {
        $wrap.addClass('grid-wrap-full');
      }
      $wrap.removeAttr('style');
    });
    */

    $(sOwlSlider).each(function() {

      var $slider = $(this);

      if (!$slider.hasClass('owl-carousel')) {

        var extOwlOptions = {
          dots:false,
          responsive: {}
        };
        extOwlOptions.responsive[0] = {
          items: 2
        };

        extOwlOptions.responsive[app.grid.xs] = {
          items: 2
        };

        extOwlOptions.responsive[app.grid.sm] = {
          items: 3
        };

        extOwlOptions.responsive[app.grid.md] = {
          items: 4
        };

        extOwlOptions.responsive[app.grid.lg] = {
          items: 5
        };

        $slider.width($slider.parent().width() - 1)
          .owlCarousel($.extend(true, {}, app.owlOptions, extOwlOptions));

      } else {
        $slider.find('.owl-stage').hide();
        $slider.removeAttr('style').width($slider.parent().width() - 1).find('.owl-stage').show();
      }
    });

  };

  rsSline.prototype.ready = function(rootDom) {

    var app = this;
    this.setup();
    this.setProductItems();

    this.mobilePoppup();

    // Timer
    setInterval(function() {
      app.timer();
    }, 1000);

    // Click protection
    /*
    $(document).on('click','.click_protection',function(e){
      e.stopImmediatePropagation();
      console.warn('Click protection');
      //alert( BX.message("RSAL_JS_TO_MACH_CLICK_LIKES") );
      return false;
    });
    */

    // a -> submit form
    /*
    $(document).on('click','form a.submit',function(){
      $(this).parents('form').find('input[type="submit"]').trigger('click');
      return false;
    });
    */

    $(window).resize(function(){
      app.onResize();
    });
  }

  rsSline.prototype.onResize = function() {
    this.setup();
    this.mobilePoppup();
    console.log('RESIZE');
  }

  rsSline.prototype.setup = function(options) {
    this.pageWidth = this.getPageWidth();
    this.pageHeight = this.getPageHeight();

    var appMain = $('#webpage');
    appMain.children('.l-main').css('height', 'auto');

    if (appMain.height() < this.pageHeight) {
      appMain.children('.l-main').css('min-height', this.pageHeight - appMain.children('.l-header').height() - appMain.children('.l-footer').height());
    }

    this.gridInit();
    console.log('this.pageWidth', this.pageWidth);
  }

  rsSline.prototype.getPageWidth = function(options) {
    return (window.innerWidth)
      ? window.innerWidth
      : (document.documentElement && document.documentElement.clientWidth)
        ? document.documentElement.clientWidth
        : 0;
  }

  rsSline.prototype.getPageHeight = function(options) {
    return (window.innerHeight)
      ? window.innerHeight
      : (document.documentElement && document.documentElement.clientHeight)
        ? document.documentElement.clientHeight
        : 0;
  }
  
  rsSline.prototype.encodeURI = function(url) {
    var arParts = url.split(/(:\/\/|:\\d+\/|\/|\?|=|&)/),
        encoded = '';

    if (arParts.length > 0) {
      for (var i in arParts) {
        encoded += (i % 2) ? arParts[i] : encodeURIComponent(arParts[i]);
      }
    }

    return encoded;
  }

  window.rsSline = rsSline;

})(window);

var appSLine = new rsSline();

;(function ($) {
  appSLine.gridInit();
})(jQuery);


;(function ($) {
  $.fn.onImageLoad = function (callback) {
    function isImageLoaded(img) {
      if (!img.complete) {
        return false;
      }
      if (typeof img.naturalWidth !== "undefined" && img.naturalWidth === 0) {
        return false;
      }
      return true;
    }

    return this.each(function () {
      var ele = $(this);
      if (ele.is("img") && $.isFunction(callback)) {
        ele.one("load", callback);
        if (isImageLoaded(this)) {
          ele.trigger("load");
        }
      }
    });
  };
})(jQuery);

;(function ($) {
  $.fn.setHtmlByUrl = function(options) {
    var settings = $.extend({
      'url': ''
    }, options);

    return this.each(function() {
      if ('' != settings.url)
      {
        var $this = $(this);
        $.ajax({
          type: 'GET',
          dataType: 'html',
          url: settings.url,
          beforeSend: function () {
            if('localStorage' in window && window[ 'localStorage' ] !== null)
            {
              data = localStorage.getItem(settings.url);
              if (data)
              {
                localStorage.setItem(settings.url, data);
                $this.html(data);
                return false;
              }
              return true;
            }
          },
          success: function (data) {
            localStorage.setItem(settings.url, data);
            $this.html(data);
          },
        });
      }
    });
  };
})(jQuery);

(function($){
  $.fn.rsToggleDark = function(options){

    options = $.extend( $.fn.rsToggleDark.defaults, options );

    return this.each(function(){
      var $this = $(this);

      var $back = $this.children('.overlay__back');

      if (options.progress && $back.length) {
        $status = $back.find('.load__status').html(options.message);
      } else {
        if (!$this.hasClass('overlay')) {
          $this.addClass('overlay');
          $back = $('<div class="overlay__back vcenter">' +
            '<div class="overlay__progress vcenter__in">' +
                '<div class="load">' +
                    '<div class="load__ball load__1"><div class="load__inner"></div></div>' +
                    '<div class="load__ball load__2"><div class="load__inner"></div></div>' +
                    '<div class="load__ball load__3"><div class="load__inner"></div></div>' +
                    '<div class="load__ball load__4"><div class="load__inner"></div></div>' +
                    '<div class="load__ball load__5"><div class="load__inner"></div></div>' +
                '</div>' +
            '</div>' +
            '</div>');
          $back.appendTo($this);
        } else {
          $this.removeClass('overlay').children('.overlay__back').remove();
        }
      }
    });

    $.fn.rsToggleDark.defaults = {
      progress: false,
      progressLeft: false,
      progressTop: false,
      text: false,
    };
  };
})(jQuery);

/*
if (BX) {
  var standartShowWait = BX.showWait,
      standartCloseWait = BX.closeWait,
      lastWait = [];
  BX.showWait = function(node, obMsg, options) {
    if (!!node) {
      if (!BX.hasClass(node, 'overlay')) {
        BX.addClass(node, 'overlay');

        var obMsg = node.bxmsg =  node.appendChild(
          BX.create(
            'div',
            {
                props: {
                    className: 'overlay__back vcenter'
                },
                html: '<div class="overlay__progress vcenter__in">\
                  <div class="load">\
                    <div class="load__ball load__1"><div class="load__inner"></div></div>\
                    <div class="load__ball load__2"><div class="load__inner"></div></div>\
                    <div class="load__ball load__3"><div class="load__inner"></div></div>\
                    <div class="load__ball load__4"><div class="load__inner"></div></div>\
                    <div class="load__ball load__5"><div class="load__inner"></div></div>\
                  </div>\
                </div>'
            }
          )
        );
      }

      lastWait[lastWait.length] = obMsg;
      return obMsg;

    } else {
      return standartShowWait(node, obMsg);
    }
  };

  BX.closeWait = function(node, obMsg, options) {
    if (!!node) {
      if (BX.hasClass(node, 'overlay')) {
        BX.removeClass(node, 'overlay');

        var back = BX.findChild(node, {
            className: 'overlay__back'
        });

        if (!!back) {
          BX.remove(back);
        }
      }
    } else {
      return standartShowWait(node, obMsg);
    }
  };
}
*/
// fancy callbacks
var RSAL_FancyCloseDelay = 1200,
    RSAL_FancyReloadPageAfterClose = false;
    RSAL_PHONETABLET = "N";

if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
    RSAL_PHONETABLET = 'Y';
}

function RSAL_FancyCloseAfterRequest(delay) {
  if(delay < 1)
    delay = RSAL_FancyCloseDelay;
  setTimeout(function(){
    $.fancybox.close();
  }, delay);
}

// /fancy callbacks

if (window.frameCacheVars !== undefined) {
    BX.addCustomEvent("onFrameDataReceived" , function(json) {
      appSLine.ready();
    });
} else {
    BX.ready(function() {
      appSLine.ready();
    });
}

$(document).on('click', '.js-ajax_link, .fancybox-skin .js-ajax_fancy', function(e){
  e.preventDefault();

  if (this.form == undefined) {
    $.fancybox(this, $.extend(
      {}, appSLine.fancyOptions,
      {
        type: 'ajax',
        ajax: {
          type: 'POST',
          data: {
            'backurl': window.location.href
          }
        }
      }
    ));
  } else {

  }
});

$(document).on('submit', '.fancybox-inner .js-ajax_form', function(e){
  var $form = $(this),
      frame_name = 'frame' + Math.round(Math.random() * 1000),
      $input = $('<input>', {
        type: 'hidden',
        name: 'rs_ajax__page',
        value: 'Y'
      }),
      $iframe = $('<iframe></iframe>', {
        id: frame_name,
        name: frame_name,
        style: 'display:none',
        load: function() {
          $.fancybox(
            $(this).contents().find('body').html(),
            $.extend({}, appSLine.fancyOptions, {
              title: $form.data('fancybox-title') || $form.attr('title') || '',
              afterShow: function(){
                $iframe.remove();
              }
            }
          ));
        }
      });

  $form.append($input).attr('target', frame_name);

  //$(document).trigger('ajaxSuccess');
  $('body').append($iframe);
  BX.onCustomEvent('onAjaxSuccess');
  $.fancybox.showLoading();
});

$(document).on('mouseenter', '.js-basket-minus, .js-basket-plus', function(){
  $('html').addClass("disableSelection");
});

$(document).on('mouseleave', '.js-basket-minus, .js-basket-plus', function(){
  $('html').removeClass("disableSelection");
});

$(document).on('click', '.js-basket-plus', function() {
  clearTimeout(rsSline.ajaxTimeout);
  var $input = $(this).siblings('.js-quantity'),
    value = parseFloat($input.val()),
    ratio = parseFloat($input.attr('step')),
    real = ratio.toString().split('.', 2)[1],
    length = 0;
  if (real !== undefined)
  {
    length = real.length;
  }
  $input.val((value + ratio).toFixed(length));
  rsSline.ajaxTimeout = setTimeout(function(){
    $input.trigger('change');
  }, appSLine.ajaxTimeoutTime);
});

$(document).on('click', '.js-basket-minus', function() {
  clearTimeout(rsSline.ajaxTimeout);
  var $input = $(this).siblings('.js-quantity'),
    value = parseFloat($input.val()),
    ratio = parseFloat($input.attr('step')),
    real = ratio.toString().split('.', 2)[1],
    length = 0;
  if (real !== undefined)
  {
    length = real.length;
  }
  if (value > ratio)
  {
    $input.val((value - ratio).toFixed(length));
    rsSline.ajaxTimeout = setTimeout(function(){
      $input.trigger('change');
    }, appSLine.ajaxTimeoutTime);
  }
});

$(document).on('blur', '.js-quantity', function() {
  var $input = $(this),
    value = parseFloat($input.val()),
    ratio = parseFloat($input.attr('step')),
    real = ratio.toString().split('.', 2)[1],
    length = 0;
  if (real !== undefined)
  {
    length = real.length;
  }
  if (0 < value)
  {
    $input.val((ratio * Math.floor(value / ratio)).toFixed(length));
  }
  else
  {
    $input.val(ratio);
  }
});

$(document).on('click','.rs_gallery-thumb', function(e){
  var $link = $(this),
      $thumbs = $link.closest('.rs_gallery-thumbs');

  $thumbs.find('.rs_gallery-thumb').removeClass('checked');
  $link.addClass('checked').closest('.rs_gallery').find('.rs_gallery-detal').attr('src', $(this).attr('href'));
  return false;
});

$(document).on('click', '.rs_gallery-prev, .rs_gallery-next', function(){
  var $btn = $(this),
      $gallery = $(this).parents('.rs_gallery'),
      $curPic = $gallery.find('.rs_gallery-thumb.checked');

  if ($btn.hasClass('rs_gallery-prev')) {
    var $checked = $curPic.prev('.rs_gallery-thumb');
    if ($checked.length > 0) {
      $checked.trigger('click');
    } else {
      $gallery.find('.rs_gallery-thumb:last').trigger('click');
    }
  } else {
    var $checked = $curPic.next('.rs_gallery-thumb');
    if ($checked.length > 0) {
      $checked.trigger('click');
    } else {
      $gallery.find('.rs_gallery-thumb:first').trigger('click');
    }
  }
  return false;
});

$(document).on('mouseenter mouseleave', '.rs_gallery-prev, .rs_gallery-next', function(){
  $('html').toggleClass('disableSelection');
});

$(document).on('click', '.rs_gallery-pic', function(){
  $(this).find('.rs_gallery-next').trigger('click');
});

$(document).on('click','.js-product .js-add2cart',function(e) {
  var $addBtn = $(this),
      $formObj = $(this.form),
      iProductId = parseInt($formObj.find('.js-product_id').val());

  if (iProductId > 0) {

    var $product = $formObj.closest('.js-product'),
      $darkArea = $product.children('.catalog_item__inner'),
      sHref = $addBtn.attr('href'),
      ajaxRequest = {
        type: 'POST',
        dataType: 'html',

        success: function(data) {
          data = BX.parseJSON(data);
          if ((data.STATUS === 'OK')) {
            BX.onCustomEvent('OnBasketChange');
            appSLine.cartList[iProductId] = true;
            appSLine.setProductCart($product);
          }
        },
        error: function() {
          console.warn('add2cart - error responsed?');
        },
        complete:function() {
          $darkArea.rsToggleDark();
        }
      };

    if (!sHref) {
      ajaxRequest.data = $(this.form).serialize() + '&ajax_basket=Y';
    } else {
      ajaxRequest.url = sHref;
      ajaxRequest.data = 'ajax_basket=Y';
    }

    var $productProps = $product.find('.product_props').find('input');

    if ($productProps.length > 0) {
      ajaxRequest.data += '&' + $product.find('.product_props').find('input').serialize();
    } else {
      ajaxRequest.data += '&prop[0]';
    }

    if ($darkArea.length < 1) {
      $darkArea = $product;
    }

    $darkArea.rsToggleDark({progress: true});
    $.ajax(ajaxRequest);

  } else {
    console.warn( 'add product to basket failed' );
  }
  return false;
});

$(document).on('click','.js-product .js-compare',function(e) {
  e.preventDefault();
  var $addBtn = $(this),
      $product = $(this).closest('.js-product'),
      arProduct = $product.data();

  if (arProduct != undefined) {
    var iProductId = arProduct.offerId ? arProduct.offerId : arProduct.productId,
        url = $addBtn.attr('href').replace('#ID#', iProductId),
        $darkArea = $product.children('.rs_product-inner'),
        ajaxRequest = {
          type: 'POST',
          data: {ajax_action: 'Y'},
          url: url,
          success: function(data) {
            data = BX.parseJSON(data);
            if (data.STATUS === 'OK') {
              if (!!appSLine.compareList[iProductId]) {
                  delete appSLine.compareList[iProductId];
              } else {
                  appSLine.compareList[iProductId] = true;
              }

              if (!data.COUNT) {
                  data.COUNT = 0;
                  for (var i in appSLine.compareList) {
                      data.COUNT++;
                  }
              }
            }
            appSLine.setProductCompare($product);
            BX.onCustomEvent('OnCompareChange');
          },
          error: function() {
            console.warn('add2cmp - error responsed?');
          },
          complete:function() {
            $darkArea.rsToggleDark();
          }
        };

    if ($darkArea.length < 1) {
        $darkArea = $product;
    }

    if (!!appSLine.compareList[iProductId]) {
        ajaxRequest.url = ajaxRequest.url.replace('ADD_TO_COMPARE_LIST', 'DELETE_FROM_COMPARE_LIST');//bitrixfix
    }

    $darkArea.rsToggleDark({progress: true});
    $.ajax(ajaxRequest);

  } else {
      console.warn('add product to compare failed');
  }
  return false;
});

$(document).on('click','.js-product .js-product__unsubscribe',function(e) {
  e.preventDefault();
  var $link = $(this),
      $product = $(this).closest('.js-product'),
      arProduct = $product.data();

  if (arProduct != undefined) {
    var iProductId = arProduct.offerId ? arProduct.offerId : arProduct.productId,
        url = '/bitrix/components/bitrix/catalog.product.subscribe.list/ajax.php',
        $darkArea = $product.children('.rs_product-inner'),
        ajaxRequest = {
          type: 'POST',
          data: {
            sessid: BX.bitrix_sessid(),
            deleteSubscribe: 'Y',
            itemId: iProductId,
            listSubscribeId: $link.data('subscribe-id').length > 0 ? BX.parseJSON($link.data('subscribe-id')) : []
          },
          url: url,
          success: function(data) {
            data = BX.parseJSON(data);
            if (data.success) {
              location.reload();
            } else {
              
            }
          },
          error: function() {
            console.warn('deleteSubscribe - error responsed?');
          },
          complete:function() {
            $darkArea.rsToggleDark();
          }
        };

    if ($darkArea.length < 1) {
        $darkArea = $product;
    }

    $darkArea.rsToggleDark({progress: true});
    console.log(ajaxRequest);
    $.ajax(ajaxRequest);

  } else {
      console.warn('Product ID undefined');
  }
  return false;
});