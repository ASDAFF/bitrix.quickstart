/* ===================================================
 * bootstrap-transition.js v2.1.1
 * ========================================================== */
!function ($) {

  $(function () {

    "use strict"; // jshint ;_;


    /* CSS TRANSITION SUPPORT (http://www.modernizr.com/)
     * ======================================================= */

    $.support.transition = (function () {

      var transitionEnd = (function () {

        var el = document.createElement('bootstrap')
          , transEndEventNames = {
               'WebkitTransition' : 'webkitTransitionEnd'
            ,  'MozTransition'    : 'transitionend'
            ,  'OTransition'      : 'oTransitionEnd otransitionend'
            ,  'transition'       : 'transitionend'
            }
          , name

        for (name in transEndEventNames){
          if (el.style[name] !== undefined) {
            return transEndEventNames[name]
          }
        }

      }())

      return transitionEnd && {
        end: transitionEnd
      }

    })()

  })

}(window.jQuery);
!function ($) {

  "use strict"; // jshint ;_;


 /* ALERT CLASS DEFINITION
  * ====================== */

  var dismiss = '[data-dismiss="alert"]'
    , Alert = function (el) {
        $(el).on('click', dismiss, this.close)
      }

  Alert.prototype.close = function (e) {
    var $this = $(this)
      , selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = $(selector)

    e && e.preventDefault()

    $parent.length || ($parent = $this.hasClass('alert') ? $this : $this.parent())

    $parent.trigger(e = $.Event('close'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      $parent
        .trigger('closed')
        .remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent.on($.support.transition.end, removeElement) :
      removeElement()
  }


 /* ALERT PLUGIN DEFINITION
  * ======================= */

  $.fn.alert = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('alert')
      if (!data) $this.data('alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.alert.Constructor = Alert


 /* ALERT DATA-API
  * ============== */

  $(function () {
    $('body').on('click.alert.data-api', dismiss, Alert.prototype.close)
  })

}(window.jQuery);/* ============================================================
 * bootstrap-button.js v2.1.1
 * http://twitter.github.com/bootstrap/javascript.html#buttons
 * ============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {

  "use strict"; // jshint ;_;


 /* BUTTON PUBLIC CLASS DEFINITION
  * ============================== */

  var Button = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.button.defaults, options)
  }

  Button.prototype.setState = function (state) {
    var d = 'disabled'
      , $el = this.$element
      , data = $el.data()
      , val = $el.is('input') ? 'val' : 'html'

    state = state + 'Text'
    data.resetText || $el.data('resetText', $el[val]())

    $el[val](data[state] || this.options[state])

    // push to event loop to allow forms to submit
    setTimeout(function () {
      state == 'loadingText' ?
        $el.addClass(d).attr(d, d) :
        $el.removeClass(d).removeAttr(d)
    }, 0)
  }

  Button.prototype.toggle = function () {
    var $parent = this.$element.closest('[data-toggle="buttons-radio"]')

    $parent && $parent
      .find('.active')
      .removeClass('active')

    this.$element.toggleClass('active')
  }


 /* BUTTON PLUGIN DEFINITION
  * ======================== */

  $.fn.button = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('button')
        , options = typeof option == 'object' && option
      if (!data) $this.data('button', (data = new Button(this, options)))
      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  $.fn.button.defaults = {
    loadingText: 'loading...'
  }

  $.fn.button.Constructor = Button


 /* BUTTON DATA-API
  * =============== */

  $(function () {
    $('body').on('click.button.data-api', '[data-toggle^=button]', function ( e ) {
      var $btn = $(e.target)
      if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
      $btn.button('toggle')
    })
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;


 /* CAROUSEL CLASS DEFINITION
  * ========================= */

  var Carousel = function (element, options) {
    this.$element = $(element)
    this.$indicators = this.$element.find('.carousel-indicators')
    this.options = options
    this.options.pause == 'hover' && this.$element
      .on('mouseenter', $.proxy(this.pause, this))
      .on('mouseleave', $.proxy(this.cycle, this))
  }

  Carousel.prototype = {

    cycle: function (e) {
      if (!e) this.paused = false
      if (this.interval) clearInterval(this.interval);
      this.options.interval
        && !this.paused
        && (this.interval = setInterval($.proxy(this.next, this), this.options.interval))
      return this
    }

  , getActiveIndex: function () {
      this.$active = this.$element.find('.item.active')
      this.$items = this.$active.parent().children()
      return this.$items.index(this.$active)
    }

  , to: function (pos) {
      var activeIndex = this.getActiveIndex()
        , that = this

      if (pos > (this.$items.length - 1) || pos < 0) return

      if (this.sliding) {
        return this.$element.one('slid', function () {
          that.to(pos)
        })
      }

      if (activeIndex == pos) {
        return this.pause().cycle()
      }

      return this.slide(pos > activeIndex ? 'next' : 'prev', $(this.$items[pos]))
    }

  , pause: function (e) {
      if (!e) this.paused = true
      if (this.$element.find('.next, .prev').length && $.support.transition.end) {
        this.$element.trigger($.support.transition.end)
        this.cycle(true)
      }
      clearInterval(this.interval)
      this.interval = null
      return this
    }

  , next: function () {
      if (this.sliding) return
      return this.slide('next')
    }

  , prev: function () {
      if (this.sliding) return
      return this.slide('prev')
    }

  , slide: function (type, next) {
      var $active = this.$element.find('.item.active')
        , $next = next || $active[type]()
        , isCycling = this.interval
        , direction = type == 'next' ? 'left' : 'right'
        , fallback  = type == 'next' ? 'first' : 'last'
        , that = this
        , e

      this.sliding = true

      isCycling && this.pause()

      $next = $next.length ? $next : this.$element.find('.item')[fallback]()

      e = $.Event('slide', {
        relatedTarget: $next[0]
      , direction: direction
      })

      if ($next.hasClass('active')) return

      if (this.$indicators.length) {
        this.$indicators.find('.active').removeClass('active')
        this.$element.one('slid', function () {
          var $nextIndicator = $(that.$indicators.children()[that.getActiveIndex()])
          $nextIndicator && $nextIndicator.addClass('active')
        })
      }

      if ($.support.transition && this.$element.hasClass('slide')) {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $next.addClass(type)
        $next[0].offsetWidth // force reflow
        $active.addClass(direction)
        $next.addClass(direction)
        this.$element.one($.support.transition.end, function () {
          $next.removeClass([type, direction].join(' ')).addClass('active')
          $active.removeClass(['active', direction].join(' '))
          that.sliding = false
          setTimeout(function () { that.$element.trigger('slid') }, 0)
        })
      } else {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $active.removeClass('active')
        $next.addClass('active')
        this.sliding = false
        this.$element.trigger('slid')
      }

      isCycling && this.cycle()

      return this
    }

  }


 /* CAROUSEL PLUGIN DEFINITION
  * ========================== */

  var old = $.fn.carousel

  $.fn.carousel = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('carousel')
        , options = $.extend({}, $.fn.carousel.defaults, typeof option == 'object' && option)
        , action = typeof option == 'string' ? option : options.slide
      if (!data) $this.data('carousel', (data = new Carousel(this, options)))
      if (typeof option == 'number') data.to(option)
      else if (action) data[action]()
      else if (options.interval) data.pause().cycle()
    })
  }

  $.fn.carousel.defaults = {
    interval: 5000
  , pause: 'hover'
  }

  $.fn.carousel.Constructor = Carousel


 /* CAROUSEL NO CONFLICT
  * ==================== */

  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old
    return this
  }

 /* CAROUSEL DATA-API
  * ================= */

  $(document).on('click.carousel.data-api', '[data-slide], [data-slide-to]', function (e) {
    var $this = $(this), href
      , $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      , options = $.extend({}, $target.data(), $this.data())
      , slideIndex

    $target.carousel(options)

    if (slideIndex = $this.attr('data-slide-to')) {
      $target.data('carousel').pause().to(slideIndex).cycle()
    }

    e.preventDefault()
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;


 /* COLLAPSE PUBLIC CLASS DEFINITION
  * ================================ */

  var Collapse = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.collapse.defaults, options)

    if (this.options.parent) {
      this.$parent = $(this.options.parent)
    }

    this.options.toggle && this.toggle()
  }

  Collapse.prototype = {

    constructor: Collapse

  , dimension: function () {
      var hasWidth = this.$element.hasClass('width')
      return hasWidth ? 'width' : 'height'
    }

  , show: function () {
      var dimension
        , scroll
        , actives
        , hasData

      if (this.transitioning) return

      dimension = this.dimension()
      scroll = $.camelCase(['scroll', dimension].join('-'))
      actives = this.$parent && this.$parent.find('> .accordion-group > .in')

      if (actives && actives.length) {
        hasData = actives.data('collapse')
        if (hasData && hasData.transitioning) return
        actives.collapse('hide')
        hasData || actives.data('collapse', null)
      }

      this.$element[dimension](0)
      this.transition('addClass', $.Event('show'), 'shown')
      $.support.transition && this.$element[dimension](this.$element[0][scroll])
    }

  , hide: function () {
      var dimension
      if (this.transitioning) return
      dimension = this.dimension()
      this.reset(this.$element[dimension]())
      this.transition('removeClass', $.Event('hide'), 'hidden')
      this.$element[dimension](0)
    }

  , reset: function (size) {
      var dimension = this.dimension()

      this.$element
        .removeClass('collapse')
        [dimension](size || 'auto')
        [0].offsetWidth

      this.$element[size !== null ? 'addClass' : 'removeClass']('collapse')

      return this
    }

  , transition: function (method, startEvent, completeEvent) {
      var that = this
        , complete = function () {
            if (startEvent.type == 'show') that.reset()
            that.transitioning = 0
            that.$element.trigger(completeEvent)
          }

      this.$element.trigger(startEvent)

      if (startEvent.isDefaultPrevented()) return

      this.transitioning = 1

      this.$element[method]('in')

      $.support.transition && this.$element.hasClass('collapse') ?
        this.$element.one($.support.transition.end, complete) :
        complete()
    }

  , toggle: function () {
      this[this.$element.hasClass('in') ? 'hide' : 'show']()
    }

  }


 /* COLLAPSIBLE PLUGIN DEFINITION
  * ============================== */

  $.fn.collapse = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('collapse')
        , options = typeof option == 'object' && option
      if (!data) $this.data('collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.collapse.defaults = {
    toggle: true
  }

  $.fn.collapse.Constructor = Collapse


 /* COLLAPSIBLE DATA-API
  * ==================== */

  $(function () {
    $('body').on('click.collapse.data-api', '[data-toggle=collapse]', function (e) {
      var $this = $(this), href
        , target = $this.attr('data-target')
          || e.preventDefault()
          || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') //strip for ie7
        , option = $(target).data('collapse') ? 'toggle' : $this.data()
      $this[$(target).hasClass('in') ? 'addClass' : 'removeClass']('collapsed')
      $(target).collapse(option)
    })
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;


 /* DROPDOWN CLASS DEFINITION
  * ========================= */

  var toggle = '[data-toggle=dropdown]'
    , Dropdown = function (element) {
        var $el = $(element).on('click.dropdown.data-api', this.toggle)
        $('html').on('click.dropdown.data-api', function () {
          $el.parent().removeClass('open')
        })
      }

  Dropdown.prototype = {

    constructor: Dropdown

  , toggle: function (e) {
      var $this = $(this)
        , $parent
        , isActive

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('open')

      clearMenus()

      if (!isActive) {
        $parent.toggleClass('open')
        $this.focus()
      }

      return false
    }

  , keydown: function (e) {
      var $this
        , $items
        , $active
        , $parent
        , isActive
        , index

      if (!/(38|40|27)/.test(e.keyCode)) return

      $this = $(this)

      e.preventDefault()
      e.stopPropagation()

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('open')

      if (!isActive || (isActive && e.keyCode == 27)) return $this.click()

      $items = $('[role=menu] li:not(.divider) a', $parent)

      if (!$items.length) return

      index = $items.index($items.filter(':focus'))

      if (e.keyCode == 38 && index > 0) index--                                        // up
      if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
      if (!~index) index = 0

      $items
        .eq(index)
        .focus()
    }

  }

  function clearMenus() {
    getParent($(toggle))
      .removeClass('open')
  }

  function getParent($this) {
    var selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = $(selector)
    $parent.length || ($parent = $this.parent())

    return $parent
  }


  /* DROPDOWN PLUGIN DEFINITION
   * ========================== */

  $.fn.dropdown = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('dropdown')
      if (!data) $this.data('dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.dropdown.Constructor = Dropdown


  /* APPLY TO STANDARD DROPDOWN ELEMENTS
   * =================================== */

  $(function () {
    $('html')
      .on('click.dropdown.data-api touchstart.dropdown.data-api', clearMenus)
    $('body')
      .on('click.dropdown touchstart.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
      .on('click.dropdown.data-api touchstart.dropdown.data-api'  , toggle, Dropdown.prototype.toggle)
      .on('keydown.dropdown.data-api touchstart.dropdown.data-api', toggle + ', [role=menu]' , Dropdown.prototype.keydown)
  })

}(window.jQuery);


!function ($) {

	"use strict"; // jshint ;_;

	/* MODAL CLASS DEFINITION
	* ====================== */

	var Modal = function (element, options) {
	this.init(element, options);
	};

	Modal.prototype = {

	constructor: Modal,

	init: function (element, options) {
	this.options = options;

	this.$element = $(element)
	.delegate('[data-dismiss="modal"]', 'click.dismiss.modal', $.proxy(this.hide, this));

	this.options.remote && this.$element.find('.modal-body').load(this.options.remote);

	var manager = typeof this.options.manager === 'function' ?
	this.options.manager.call(this) : this.options.manager;

	manager = manager.appendModal ?
	manager : $(manager).modalmanager().data('modalmanager');

	manager.appendModal(this);
	},

	toggle: function () {
	return this[!this.isShown ? 'show' : 'hide']();
	},

	show: function () {
	var e = $.Event('show');

	if (this.isShown) return;

	this.$element.trigger(e);

	if (e.isDefaultPrevented()) return;

	this.escape();

	this.tab();

	this.options.loading && this.loading();
	},

	hide: function (e) {
	e && e.preventDefault();

	e = $.Event('hide');

	this.$element.trigger(e);

	if (!this.isShown || e.isDefaultPrevented()) return (this.isShown = false);

	this.isShown = false;

	this.escape();

	this.tab();

	this.isLoading && this.loading();

	$(document).off('focusin.modal');

	this.$element
	.removeClass('in')
	.removeClass('animated')
	.removeClass(this.options.attentionAnimation)
	.removeClass('modal-overflow')
	.attr('aria-hidden', true);

	$.support.transition && this.$element.hasClass('fade') ?
	this.hideWithTransition() :
	this.hideModal();
	},

	layout: function () { 
	var prop = this.options.height ? 'height' : 'max-height',
	value = this.options.height || this.options.maxHeight;

	if (this.options.width){
	this.$element.css('width', this.options.width);

	var that = this;
	this.$element.css('margin-left', function () {
	if (/%/ig.test(that.options.width)){
	return -(parseInt(that.options.width) / 2) + '%';
	} else {
	return -($(this).width() / 2) + 'px';
	}
	});
	} else {
	this.$element.css('width', '');
	this.$element.css('margin-left', '');
	}

	this.$element.find('.modal-body')
	.css('overflow', '')
	.css(prop, '');

	var modalOverflow = $(window).height() - 10 < this.$element.height();

	if (value){
	this.$element.find('.modal-body')
	.css('overflow', 'auto')
	.css(prop, value);
	}
	
	if (modalOverflow || this.options.modalOverflow) {
	this.$element
	.css('margin-top', 0)
	.addClass('modal-overflow');
	} else {
	this.$element
	.css('margin-top', 0 - this.$element.height() / 2)
	.removeClass('modal-overflow');
	}
	if (this.options.marginLeft>0) {
		//alert(' yes '+this.options.marginLeft);
		this.$element.css('margin-left', '-'+this.options.marginLeft+'px');
		//this.$element.css('width', '300px');
	} 
	//alert(getProps(this.options));
	},

	tab: function () {
	var that = this;

	if (this.isShown && this.options.consumeTab) {
	this.$element.on('keydown.tabindex.modal', '[data-tabindex]', function (e) {
	if (e.keyCode && e.keyCode == 9){
	var $next = $(this),
	$rollover = $(this);

	that.$element.find('[data-tabindex]:enabled:not([readonly])').each(function (e) {
	if (!e.shiftKey){
	$next = $next.data('tabindex') < $(this).data('tabindex') ?
	$next = $(this) :
	$rollover = $(this);
	} else {
	$next = $next.data('tabindex') > $(this).data('tabindex') ?
	$next = $(this) :
	$rollover = $(this);
	}
	});

	$next[0] !== $(this)[0] ?
	$next.focus() : $rollover.focus();

	e.preventDefault();
	}
	});
	} else if (!this.isShown) {
	this.$element.off('keydown.tabindex.modal');
	}
	},

	escape: function () {
	var that = this;
	if (this.isShown && this.options.keyboard) {
	if (!this.$element.attr('tabindex')) this.$element.attr('tabindex', -1);

	this.$element.on('keyup.dismiss.modal', function (e) {
	e.which == 27 && that.hide();
	});
	} else if (!this.isShown) {
	this.$element.off('keyup.dismiss.modal')
	}
	},

	hideWithTransition: function () {
	var that = this
	, timeout = setTimeout(function () {
	that.$element.off($.support.transition.end);
	that.hideModal();
	}, 500);

	this.$element.one($.support.transition.end, function () {
	clearTimeout(timeout);
	that.hideModal();
	});
	},

	hideModal: function () {
	this.$element
	.hide()
	.trigger('hidden');

	var prop = this.options.height ? 'height' : 'max-height';
	var value = this.options.height || this.options.maxHeight;

	if (value){
	this.$element.find('.modal-body')
	.css('overflow', '')
	.css(prop, '');
	}

	},

	removeLoading: function () {
	this.$loading.remove();
	this.$loading = null;
	this.isLoading = false;
	},

	loading: function (callback) {
	callback = callback || function () {};

	var animate = this.$element.hasClass('fade') ? 'fade' : '';

	if (!this.isLoading) {
	var doAnimate = $.support.transition && animate;

	this.$loading = $('<div class="loading-mask ' + animate + '">')
	.append(this.options.spinner)
	.appendTo(this.$element);

	if (doAnimate) this.$loading[0].offsetWidth; // force reflow

	this.$loading.addClass('in');

	this.isLoading = true;

	doAnimate ?
	this.$loading.one($.support.transition.end, callback) :
	callback();

	} else if (this.isLoading && this.$loading) {
	this.$loading.removeClass('in');

	var that = this;
	$.support.transition && this.$element.hasClass('fade')?
	this.$loading.one($.support.transition.end, function () { that.removeLoading() }) :
	that.removeLoading();

	} else if (callback) {
	callback(this.isLoading);
	}
	},

	focus: function () {
	var $focusElem = this.$element.find(this.options.focusOn);

	$focusElem = $focusElem.length ? $focusElem : this.$element;

	$focusElem.focus();
	},

	attention: function (){
	// NOTE: transitionEnd with keyframes causes odd behaviour

	if (this.options.attentionAnimation){
	this.$element
	.removeClass('animated')
	.removeClass(this.options.attentionAnimation);

	var that = this;

	setTimeout(function () {
	that.$element
	.addClass('animated')
	.addClass(that.options.attentionAnimation);
	}, 0);
	}


	this.focus();
	},


	destroy: function () {
	var e = $.Event('destroy');
	this.$element.trigger(e);
	if (e.isDefaultPrevented()) return;

	this.teardown();
	},

	teardown: function () {
	if (!this.$parent.length){
	this.$element.remove();
	this.$element = null;
	return;
	}

	if (this.$parent !== this.$element.parent()){
	this.$element.appendTo(this.$parent);
	}

	this.$element.off('.modal');
	this.$element.removeData('modal');
	this.$element
	.removeClass('in')
	.attr('aria-hidden', true);
	}
	};


	/* MODAL PLUGIN DEFINITION
	* ======================= */

	$.fn.modal = function (option, args) {
	return this.each(function () {
	var $this = $(this),
	data = $this.data('modal'),
	options = $.extend({}, $.fn.modal.defaults, $this.data(), typeof option == 'object' && option);

	if (!data) $this.data('modal', (data = new Modal(this, options)));
	if (typeof option == 'string') data[option].apply(data, [].concat(args));
	else if (options.show) data.show()
	})
	};

	$.fn.modal.defaults = {
	keyboard: true,
	backdrop: true,
	loading: false,
	show: true,
	width: null,
	height: null,
	maxHeight: null,
	modalOverflow: false,
	consumeTab: true,
	focusOn: null,
	replace: false,
	resize: false,
	attentionAnimation: 'shake',
	manager: 'body',
	spinner: '<div class="loading-spinner" style="width: 200px; margin-left: -100px;"><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div>'
	};

	$.fn.modal.Constructor = Modal;


	/* MODAL DATA-API
	* ============== */

	$(function () {
	$(document).off('click.modal').on('click.modal.data-api', '[data-toggle="modal"]', function ( e ) {
	var $this = $(this),
	href = $this.attr('href'),
	$target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))), //strip for ie7
	option = $target.data('modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data());

	e.preventDefault();
	$target
	.modal(option)
	.one('hide', function () {
	$this.focus();
	})
	});
	});

	}(window.jQuery);


	/* ===========================================================
	 * bootstrap-tooltip.js v2.3.2
	 * http://twitter.github.com/bootstrap/javascript.html#tooltips
	 * Inspired by the original jQuery.tipsy by Jason Frame
	 * ===========================================================
	 * Copyright 2012 Twitter, Inc.
	 *
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 * http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 * ========================================================== */


	!function ($) {

	  "use strict"; // jshint ;_;


	 /* TOOLTIP PUBLIC CLASS DEFINITION
	  * =============================== */

	  var Tooltip = function (element, options) {
	    this.init('tooltip', element, options)
	  }

	  Tooltip.prototype = {

	    constructor: Tooltip

	  , init: function (type, element, options) {
	      var eventIn
	        , eventOut
	        , triggers
	        , trigger
	        , i

	      this.type = type
	      this.$element = $(element)
	      this.options = this.getOptions(options)
	      this.enabled = true

	      triggers = this.options.trigger.split(' ')

	      for (i = triggers.length; i--;) {
	        trigger = triggers[i]
	        if (trigger == 'click') {
	          this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
	        } else if (trigger != 'manual') {
	          eventIn = trigger == 'hover' ? 'mouseenter' : 'focus'
	          eventOut = trigger == 'hover' ? 'mouseleave' : 'blur'
	          this.$element.on(eventIn + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
	          this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
	        }
	      }

	      this.options.selector ?
	        (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
	        this.fixTitle()
	    }

	  , getOptions: function (options) {
	      options = $.extend({}, $.fn[this.type].defaults, this.$element.data(), options)

	      if (options.delay && typeof options.delay == 'number') {
	        options.delay = {
	          show: options.delay
	        , hide: options.delay
	        }
	      }

	      return options
	    }

	  , enter: function (e) {
	      var defaults = $.fn[this.type].defaults
	        , options = {}
	        , self

	      this._options && $.each(this._options, function (key, value) {
	        if (defaults[key] != value) options[key] = value
	      }, this)

	      self = $(e.currentTarget)[this.type](options).data(this.type)

	      if (!self.options.delay || !self.options.delay.show) return self.show()

	      clearTimeout(this.timeout)
	      self.hoverState = 'in'
	      this.timeout = setTimeout(function() {
	        if (self.hoverState == 'in') self.show()
	      }, self.options.delay.show)
	    }

	  , leave: function (e) {
	      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

	      if (this.timeout) clearTimeout(this.timeout)
	      if (!self.options.delay || !self.options.delay.hide) return self.hide()

	      self.hoverState = 'out'
	      this.timeout = setTimeout(function() {
	        if (self.hoverState == 'out') self.hide()
	      }, self.options.delay.hide)
	    }

	  , show: function () {
	      var $tip
	        , pos
	        , actualWidth
	        , actualHeight
	        , placement
	        , tp
	        , e = $.Event('show')

	      if (this.hasContent() && this.enabled) {
	        this.$element.trigger(e)
	        if (e.isDefaultPrevented()) return
	        $tip = this.tip()
	        this.setContent()

	        if (this.options.animation) {
	          $tip.addClass('fade')
	        }

	        placement = typeof this.options.placement == 'function' ?
	          this.options.placement.call(this, $tip[0], this.$element[0]) :
	          this.options.placement

	        $tip
	          .detach()
	          .css({ top: 0, left: 0, display: 'block' })

	        this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

	        pos = this.getPosition()

	        actualWidth = $tip[0].offsetWidth
	        actualHeight = $tip[0].offsetHeight

	        switch (placement) {
	          case 'bottom':
	            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}
	            break
	          case 'top':
	            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}
	            break
	          case 'left':
	            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}
	            break
	          case 'right':
	            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}
	            break
	        }

	        this.applyPlacement(tp, placement)
	        this.$element.trigger('shown')
	      }
	    }

	  , applyPlacement: function(offset, placement){
	      var $tip = this.tip()
	        , width = $tip[0].offsetWidth
	        , height = $tip[0].offsetHeight
	        , actualWidth
	        , actualHeight
	        , delta
	        , replace

	      $tip
	        .offset(offset)
	        .addClass(placement)
	        .addClass('in')

	      actualWidth = $tip[0].offsetWidth
	      actualHeight = $tip[0].offsetHeight

	      if (placement == 'top' && actualHeight != height) {
	        offset.top = offset.top + height - actualHeight
	        replace = true
	      }

	      if (placement == 'bottom' || placement == 'top') {
	        delta = 0

	        if (offset.left < 0){
	          delta = offset.left * -2
	          offset.left = 0
	          $tip.offset(offset)
	          actualWidth = $tip[0].offsetWidth
	          actualHeight = $tip[0].offsetHeight
	        }

	        this.replaceArrow(delta - width + actualWidth, actualWidth, 'left')
	      } else {
	        this.replaceArrow(actualHeight - height, actualHeight, 'top')
	      }

	      if (replace) $tip.offset(offset)
	    }

	  , replaceArrow: function(delta, dimension, position){
	      this
	        .arrow()
	        .css(position, delta ? (50 * (1 - delta / dimension) + "%") : '')
	    }

	  , setContent: function () {
	      var $tip = this.tip()
	        , title = this.getTitle()

	      $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
	      $tip.removeClass('fade in top bottom left right')
	    }

	  , hide: function () {
	      var that = this
	        , $tip = this.tip()
	        , e = $.Event('hide')

	      this.$element.trigger(e)
	      if (e.isDefaultPrevented()) return

	      $tip.removeClass('in')

	      function removeWithAnimation() {
	        var timeout = setTimeout(function () {
	          $tip.off($.support.transition.end).detach()
	        }, 500)

	        $tip.one($.support.transition.end, function () {
	          clearTimeout(timeout)
	          $tip.detach()
	        })
	      }

	      $.support.transition && this.$tip.hasClass('fade') ?
	        removeWithAnimation() :
	        $tip.detach()

	      this.$element.trigger('hidden')

	      return this
	    }

	  , fixTitle: function () {
	      var $e = this.$element
	      if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') {
	        $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
	      }
	    }

	  , hasContent: function () {
	      return this.getTitle()
	    }

	  , getPosition: function () {
	      var el = this.$element[0]
	      return $.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : {
	        width: el.offsetWidth
	      , height: el.offsetHeight
	      }, this.$element.offset())
	    }

	  , getTitle: function () {
	      var title
	        , $e = this.$element
	        , o = this.options

	      title = $e.attr('data-original-title')
	        || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

	      return title
	    }

	  , tip: function () {
	      return this.$tip = this.$tip || $(this.options.template)
	    }

	  , arrow: function(){
	      return this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow")
	    }

	  , validate: function () {
	      if (!this.$element[0].parentNode) {
	        this.hide()
	        this.$element = null
	        this.options = null
	      }
	    }

	  , enable: function () {
	      this.enabled = true
	    }

	  , disable: function () {
	      this.enabled = false
	    }

	  , toggleEnabled: function () {
	      this.enabled = !this.enabled
	    }

	  , toggle: function (e) {
	      var self = e ? $(e.currentTarget)[this.type](this._options).data(this.type) : this
	      self.tip().hasClass('in') ? self.hide() : self.show()
	    }

	  , destroy: function () {
	      this.hide().$element.off('.' + this.type).removeData(this.type)
	    }

	  }


	 /* TOOLTIP PLUGIN DEFINITION
	  * ========================= */

	  var old = $.fn.tooltip

	  $.fn.tooltip = function ( option ) {
	    return this.each(function () {
	      var $this = $(this)
	        , data = $this.data('tooltip')
	        , options = typeof option == 'object' && option
	      if (!data) $this.data('tooltip', (data = new Tooltip(this, options)))
	      if (typeof option == 'string') data[option]()
	    })
	  }

	  $.fn.tooltip.Constructor = Tooltip

	  $.fn.tooltip.defaults = {
	    animation: true
	  , placement: 'top'
	  , selector: false
	  , template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
	  , trigger: 'hover focus'
	  , title: ''
	  , delay: 0
	  , html: false
	  , container: false
	  }


	 /* TOOLTIP NO CONFLICT
	  * =================== */

	  $.fn.tooltip.noConflict = function () {
	    $.fn.tooltip = old
	    return this
	  }

	}(window.jQuery);



!function ($) {

  "use strict"; // jshint ;_;


 /* POPOVER PUBLIC CLASS DEFINITION
  * =============================== */

  var Popover = function (element, options) {
    this.init('popover', element, options)
  }


  /* NOTE: POPOVER EXTENDS BOOTSTRAP-TOOLTIP.js
     ========================================== */

  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype, {

    constructor: Popover

  , setContent: function () {
      var $tip = this.tip()
        , title = this.getTitle()
        , content = this.getContent()

      $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title)
      $tip.find('.popover-content > *')[this.options.html ? 'html' : 'text'](content)

      $tip.removeClass('fade top bottom left right in')
    }

  , hasContent: function () {
      return this.getTitle() || this.getContent()
    }

  , getContent: function () {
      var content
        , $e = this.$element
        , o = this.options

      content = $e.attr('data-content')
        || (typeof o.content == 'function' ? o.content.call($e[0]) :  o.content)

      return content
    }

  , tip: function () {
      if (!this.$tip) {
        this.$tip = $(this.options.template)
      }
      return this.$tip
    }

  , destroy: function () {
      this.hide().$element.off('.' + this.type).removeData(this.type)
    }

  })


 /* POPOVER PLUGIN DEFINITION
  * ======================= */

  $.fn.popover = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('popover')
        , options = typeof option == 'object' && option
      if (!data) $this.data('popover', (data = new Popover(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.popover.Constructor = Popover

  $.fn.popover.defaults = $.extend({} , $.fn.tooltip.defaults, {
    placement: 'right'
  , trigger: 'click'
  , content: ''
  , template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;


 /* SCROLLSPY CLASS DEFINITION
  * ========================== */

  function ScrollSpy(element, options) {
    var process = $.proxy(this.process, this)
      , $element = $(element).is('body') ? $(window) : $(element)
      , href
    this.options = $.extend({}, $.fn.scrollspy.defaults, options)
    this.$scrollElement = $element.on('scroll.scroll-spy.data-api', process)
    this.selector = (this.options.target
      || ((href = $(element).attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      || '') + ' .nav li > a'
    this.$body = $('body')
    this.refresh()
    this.process()
  }

  ScrollSpy.prototype = {

      constructor: ScrollSpy

    , refresh: function () {
        var self = this
          , $targets

        this.offsets = $([])
        this.targets = $([])

        $targets = this.$body
          .find(this.selector)
          .map(function () {
            var $el = $(this)
              , href = $el.data('target') || $el.attr('href')
              , $href = /^#\w/.test(href) && $(href)
            return ( $href
              && $href.length
              && [[ $href.position().top, href ]] ) || null
          })
          .sort(function (a, b) { return a[0] - b[0] })
          .each(function () {
            self.offsets.push(this[0])
            self.targets.push(this[1])
          })
      }

    , process: function () {
        var scrollTop = this.$scrollElement.scrollTop() + this.options.offset
          , scrollHeight = this.$scrollElement[0].scrollHeight || this.$body[0].scrollHeight
          , maxScroll = scrollHeight - this.$scrollElement.height()
          , offsets = this.offsets
          , targets = this.targets
          , activeTarget = this.activeTarget
          , i

        if (scrollTop >= maxScroll) {
          return activeTarget != (i = targets.last()[0])
            && this.activate ( i )
        }

        for (i = offsets.length; i--;) {
          activeTarget != targets[i]
            && scrollTop >= offsets[i]
            && (!offsets[i + 1] || scrollTop <= offsets[i + 1])
            && this.activate( targets[i] )
        }
      }

    , activate: function (target) {
        var active
          , selector

        this.activeTarget = target

        $(this.selector)
          .parent('.active')
          .removeClass('active')

        selector = this.selector
          + '[data-target="' + target + '"],'
          + this.selector + '[href="' + target + '"]'

        active = $(selector)
          .parent('li')
          .addClass('active')

        if (active.parent('.dropdown-menu').length)  {
          active = active.closest('li.dropdown').addClass('active')
        }

        active.trigger('activate')
      }

  }


 /* SCROLLSPY PLUGIN DEFINITION
  * =========================== */

  $.fn.scrollspy = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('scrollspy')
        , options = typeof option == 'object' && option
      if (!data) $this.data('scrollspy', (data = new ScrollSpy(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.scrollspy.Constructor = ScrollSpy

  $.fn.scrollspy.defaults = {
    offset: 10
  }


 /* SCROLLSPY DATA-API
  * ================== */

  $(window).on('load', function () {
    $('[data-spy="scroll"]').each(function () {
      var $spy = $(this)
      $spy.scrollspy($spy.data())
    })
  })

}(window.jQuery);


!function ($) {

  "use strict"; // jshint ;_;


 /* TAB CLASS DEFINITION
  * ==================== */

  var Tab = function (element) {
    this.element = $(element)
  }

  Tab.prototype = {

    constructor: Tab

  , show: function () {
      var $this = this.element
        , $ul = $this.closest('ul:not(.dropdown-menu)')
        , selector = $this.attr('data-target')
        , previous
        , $target
        , e

      if (!selector) {
        selector = $this.attr('href')
        selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
      }

      if ( $this.parent('li').hasClass('active') ) return

      previous = $ul.find('.active a').last()[0]

      e = $.Event('show', {
        relatedTarget: previous
      })

      $this.trigger(e)

      if (e.isDefaultPrevented()) return

      $target = $(selector)

      this.activate($this.parent('li'), $ul)
      this.activate($target, $target.parent(), function () {
        $this.trigger({
          type: 'shown'
        , relatedTarget: previous
        })
      })
    }

  , activate: function ( element, container, callback) {
      var $active = container.find('> .active')
        , transition = callback
            && $.support.transition
            && $active.hasClass('fade')

      function next() {
        $active
          .removeClass('active')
          .find('> .dropdown-menu > .active')
          .removeClass('active')

        element.addClass('active')

        if (transition) {
          element[0].offsetWidth // reflow for transition
          element.addClass('in')
        } else {
          element.removeClass('fade')
        }

        if ( element.parent('.dropdown-menu') ) {
          element.closest('li.dropdown').addClass('active')
        }

        callback && callback()
      }

      transition ?
        $active.one($.support.transition.end, next) :
        next()

      $active.removeClass('in')
    }
  }


 /* TAB PLUGIN DEFINITION
  * ===================== */

  $.fn.tab = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('tab')
      if (!data) $this.data('tab', (data = new Tab(this)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.tab.Constructor = Tab


 /* TAB DATA-API
  * ============ */

  $(function () {
    $('body').on('click.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
      e.preventDefault()
      $(this).tab('show')
    })
  })

}(window.jQuery);
/* SELECT
 * ============ */
!function(b){var a=function(d,c,f){if(f){f.stopPropagation();f.preventDefault()}this.$element=b(d);this.$newElement=null;this.button=null;this.options=b.extend({},b.fn.selectpicker.defaults,this.$element.data(),typeof c=="object"&&c);if(this.options.title==null){this.options.title=this.$element.attr("title")}this.val=a.prototype.val;this.render=a.prototype.render;this.refresh=a.prototype.refresh;this.selectAll=a.prototype.selectAll;this.deselectAll=a.prototype.deselectAll;this.init()};a.prototype={constructor:a,init:function(d){if(!this.options.container){this.$element.hide()}else{this.$element.css("visibility","hidden")}this.multiple=this.$element.prop("multiple");var f=this.$element.attr("class")!==undefined?this.$element.attr("class").split(/\s+/):"";var h=this.$element.attr("id");this.$element.after(this.createView());this.$newElement=this.$element.next(".bootstrap-select");if(this.options.container){this.selectPosition()}this.button=this.$newElement.find("> button");if(h!==undefined){var g=this;this.button.attr("data-id",h);b('label[for="'+h+'"]').click(function(){g.$newElement.find("button[data-id="+h+"]").focus()})}for(var c=0;c<f.length;c++){if(f[c]!="selectpicker"){this.$newElement.addClass(f[c])}}if(this.multiple){this.$newElement.addClass("show-tick")}this.button.addClass(this.options.style);this.checkDisabled();this.checkTabIndex();this.clickListener();this.render();this.setSize()},createDropdown:function(){var c="<div class='btn-group bootstrap-select'><button type='button' class='btn dropdown-toggle' data-toggle='dropdown'><span class='filter-option pull-left'></span>&nbsp;<span class='caret'></span></button><ul class='dropdown-menu' role='menu'></ul></div>";return b(c)},createView:function(){var c=this.createDropdown();var d=this.createLi();c.find("ul").append(d);return c},reloadLi:function(){this.destroyLi();var c=this.createLi();this.$newElement.find("ul").append(c)},destroyLi:function(){this.$newElement.find("li").remove()},createLi:function(){var h=this;var e=[];var g=[];var c="";this.$element.find("option").each(function(){e.push(b(this).text())});this.$element.find("option").each(function(k){var l=b(this);var j=l.attr("class")!==undefined?l.attr("class"):"";var p=l.text();var n=l.data("subtext")!==undefined?'<small class="muted">'+l.data("subtext")+"</small>":"";var m=l.data("icon")!==undefined?'<i class="'+l.data("icon")+'"></i> ':"";if(l.is(":disabled")||l.parent().is(":disabled")){m="<span>"+m+"</span>"}p=m+'<span class="text">'+p+n+"</span>";if(h.options.hideDisabled&&(l.is(":disabled")||l.parent().is(":disabled"))){g.push('<a style="min-height: 0; padding: 0"></a>')}else{if(l.parent().is("optgroup")&&l.data("divider")!=true){if(l.index()==0){var o=l.parent().attr("label");var q=l.parent().data("subtext")!==undefined?'<small class="muted">'+l.parent().data("subtext")+"</small>":"";var i=l.parent().data("icon")?'<i class="'+l.parent().data("icon")+'"></i> ':"";o=i+'<span class="text">'+o+q+"</span>";if(l[0].index!=0){g.push('<div class="div-contain"><div class="divider"></div></div><dt>'+o+"</dt>"+h.createA(p,"opt "+j))}else{g.push("<dt>"+o+"</dt>"+h.createA(p,"opt "+j))}}else{g.push(h.createA(p,"opt "+j))}}else{if(l.data("divider")==true){g.push('<div class="div-contain"><div class="divider"></div></div>')}else{if(b(this).data("hidden")==true){g.push("")}else{g.push(h.createA(p,j))}}}}});if(e.length>0){for(var d=0;d<e.length;d++){var f=this.$element.find("option").eq(d);c+="<li rel="+d+">"+g[d]+"</li>"}}if(!this.multiple&&this.$element.find("option:selected").length==0&&!h.options.title){this.$element.find("option").eq(0).prop("selected",true).attr("selected","selected")}return b(c)},createA:function(d,c){return'<a tabindex="0" class="'+c+'">'+d+'<i class="icon-ok check-mark"></i></a>'},render:function(){var h=this;this.$element.find("option").each(function(i){h.setDisabled(i,b(this).is(":disabled")||b(this).parent().is(":disabled"));h.setSelected(i,b(this).is(":selected"))});var g=this.$element.find("option:selected").map(function(i,k){var j;if(h.options.showSubtext&&b(this).attr("data-subtext")&&!h.multiple){j=' <small class="muted">'+b(this).data("subtext")+"</small>"}else{j=""}if(b(this).attr("title")!=undefined){return b(this).attr("title")}else{return b(this).text()+j}}).toArray();var f=!this.multiple?g[0]:g.join(", ");if(h.multiple&&h.options.selectedTextFormat.indexOf("count")>-1){var c=h.options.selectedTextFormat.split(">");var e=this.options.hideDisabled?":not([disabled])":"";if((c.length>1&&g.length>c[1])||(c.length==1&&g.length>=2)){f=g.length+" of "+this.$element.find('option:not([data-divider="true"]):not([data-hidden="true"])'+e).length+" selected"}}if(!f){f=h.options.title!=undefined?h.options.title:h.options.noneSelectedText}var d;if(this.options.showSubtext&&this.$element.find("option:selected").attr("data-subtext")){d=' <small class="muted">'+this.$element.find("option:selected").data("subtext")+"</small>"}else{d=""}h.$newElement.find(".filter-option").html(f+d)},setSize:function(){if(this.options.container){this.$newElement.toggle(this.$element.parent().is(":visible"))}var j=this;var e=this.$newElement.find(".dropdown-menu");var l=e.find("li > a");var o=this.$newElement.addClass("open").find(".dropdown-menu li > a").outerHeight();this.$newElement.removeClass("open");var h=e.find("li .divider").outerHeight(true);var g=this.$newElement.offset().top;var k=this.$newElement.outerHeight();var c=parseInt(e.css("padding-top"))+parseInt(e.css("padding-bottom"))+parseInt(e.css("border-top-width"))+parseInt(e.css("border-bottom-width"));var d=this.options.hideDisabled?":not(.disabled)":"";var f;if(this.options.size=="auto"){var p=function(){var q=g-b(window).scrollTop();var u=window.innerHeight;var r=c+parseInt(e.css("margin-top"))+parseInt(e.css("margin-bottom"))+2;var t=u-q-k-r;var s;f=t;if(j.$newElement.hasClass("dropup")){f=q-r}if((e.find("li").length+e.find("dt").length)>3){s=o*3+r-2}else{s=0}e.css({"max-height":f+"px","overflow-y":"auto","min-height":s+"px"})};p();b(window).resize(p);b(window).scroll(p)}else{if(this.options.size&&this.options.size!="auto"&&e.find("li"+d).length>this.options.size){var n=e.find("li"+d+" > *").filter(":not(.div-contain)").slice(0,this.options.size).last().parent().index();var m=e.find("li").slice(0,n+1).find(".div-contain").length;f=o*this.options.size+m*h+c;e.css({"max-height":f+"px","overflow-y":"auto"})}}if(this.options.width=="auto"){this.$newElement.find(".dropdown-menu").css("min-width","0");var i=this.$newElement.find(".dropdown-menu").css("width");this.$newElement.css("width",i);if(this.options.container){this.$element.css("width",i)}}else{if(this.options.width){if(this.options.container){this.$element.css("width",this.options.width);this.$newElement.width(this.$element.outerWidth())}else{this.$newElement.css("width",this.options.width)}}else{if(this.options.container){this.$newElement.width(this.$element.outerWidth())}}}},selectPosition:function(){var e=b(this.options.container).offset();var d=this.$element.offset();if(e&&d){var f=d.top-e.top;var c=d.left-e.left;this.$newElement.appendTo(this.options.container);this.$newElement.css({position:"absolute",top:f+"px",left:c+"px"})}},refresh:function(){this.reloadLi();this.render();this.setSize();this.checkDisabled();if(this.options.container){this.selectPosition()}},setSelected:function(c,d){if(d){this.$newElement.find("li").eq(c).addClass("selected")}else{this.$newElement.find("li").eq(c).removeClass("selected")}},setDisabled:function(c,d){if(d){this.$newElement.find("li").eq(c).addClass("disabled").find("a").attr("href","#").attr("tabindex",-1)}else{this.$newElement.find("li").eq(c).removeClass("disabled").find("a").removeAttr("href").attr("tabindex",0)}},isDisabled:function(){return this.$element.is(":disabled")||this.$element.attr("readonly")},checkDisabled:function(){if(this.isDisabled()){this.button.addClass("disabled");this.button.click(function(c){c.preventDefault()});this.button.attr("tabindex","-1")}else{if(!this.isDisabled()&&this.button.hasClass("disabled")){this.button.removeClass("disabled");this.button.click(function(){return true});this.button.removeAttr("tabindex")}}},checkTabIndex:function(){if(this.$element.is("[tabindex]")){var c=this.$element.attr("tabindex");this.button.attr("tabindex",c)}},clickListener:function(){var c=this;b("body").on("touchstart.dropdown",".dropdown-menu",function(d){d.stopPropagation()});this.$newElement.on("click","li a",function(j){var g=b(this).parent().index(),i=b(this).parent(),d=i.parents(".bootstrap-select"),h=c.$element.val();if(c.multiple){j.stopPropagation()}j.preventDefault();if(c.$element.not(":disabled")&&!b(this).parent().hasClass("disabled")){if(!c.multiple){c.$element.find("option").prop("selected",false);c.$element.find("option").eq(g).prop("selected",true)}else{var f=c.$element.find("option").eq(g).prop("selected");if(f){c.$element.find("option").eq(g).prop("selected",false)}else{c.$element.find("option").eq(g).prop("selected",true)}}d.find(".filter-option").html(i.text());d.find("button").focus();if(h!=c.$element.val()){c.$element.trigger("change")}c.render()}});this.$newElement.on("click","li.disabled a, li dt, li .div-contain",function(f){f.preventDefault();f.stopPropagation();var d=b(this).parent().parents(".bootstrap-select");d.find("button").focus()});this.$element.on("change",function(d){c.render()})},val:function(c){if(c!=undefined){this.$element.val(c);this.$element.trigger("change");return this.$element}else{return this.$element.val()}},selectAll:function(){this.$element.find("option").prop("selected",true).attr("selected","selected");this.render()},deselectAll:function(){this.$element.find("option").prop("selected",false).removeAttr("selected");this.render()},keydown:function(n){var o,m,h,l,j,i,p,d,g;o=b(this);h=o.parent();m=b("[role=menu] li:not(.divider):visible a",h);if(!m.length){return}if(/(38|40)/.test(n.keyCode)){l=m.index(m.filter(":focus"));i=m.parent(":not(.disabled)").first().index();p=m.parent(":not(.disabled)").last().index();j=m.eq(l).parent().nextAll(":not(.disabled)").eq(0).index();d=m.eq(l).parent().prevAll(":not(.disabled)").eq(0).index();g=m.eq(j).parent().prevAll(":not(.disabled)").eq(0).index();if(n.keyCode==38){if(l!=g&&l>d){l=d}if(l<i){l=i}}if(n.keyCode==40){if(l!=g&&l<j){l=j}if(l>p){l=p}}m.eq(l).focus()}else{var f={48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",59:";",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z",96:"0",97:"1",98:"2",99:"3",100:"4",101:"5",102:"6",103:"7",104:"8",105:"9"};var c=[];m.each(function(){if(b(this).parent().is(":not(.disabled)")){if(b.trim(b(this).text().toLowerCase()).substring(0,1)==f[n.keyCode]){c.push(b(this).parent().index())}}});var k=b(document).data("keycount");k++;b(document).data("keycount",k);var q=b.trim(b(":focus").text().toLowerCase()).substring(0,1);if(q!=f[n.keyCode]){k=1;b(document).data("keycount",k)}else{if(k>=c.length){b(document).data("keycount",0)}}m.eq(c[k-1]).focus()}if(/(13)/.test(n.keyCode)){b(":focus").click();h.addClass("open");b(document).data("keycount",0)}}};b.fn.selectpicker=function(e,f){var c=arguments;var g;var d=this.each(function(){if(b(this).is("select")){var m=b(this),l=m.data("selectpicker"),h=typeof e=="object"&&e;if(!l){m.data("selectpicker",(l=new a(this,h,f)))}else{if(h){for(var j in h){l.options[j]=h[j]}}}if(typeof e=="string"){var k=e;if(l[k] instanceof Function){[].shift.apply(c);g=l[k].apply(l,c)}else{g=l.options[k]}}}});if(g!=undefined){return g}else{return d}};b.fn.selectpicker.defaults={style:null,size:"auto",title:null,selectedTextFormat:"values",noneSelectedText:"Nothing selected",width:null,container:false,hideDisabled:false,showSubtext:false};b(document).data("keycount",0).on("keydown","[data-toggle=dropdown], [role=menu]",a.prototype.keydown)}(window.jQuery);