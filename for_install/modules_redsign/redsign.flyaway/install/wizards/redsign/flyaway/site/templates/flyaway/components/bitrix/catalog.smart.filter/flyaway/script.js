var RSM_modefTimer = 0;

function ReFLY_SeachProp($inputObj) {
  var value = $inputObj.val();
  var $lvl1 = $inputObj.parents('.filter__item');

  if (value.length < 1) {
    $lvl1.find('.js-item-filter').css('display', 'block');
  } else {
    $lvl1.find('.js-item-filter').each(function() {
      var p_value = $(this).find('.js-name-filter').html().substr(0, value.length);

      if (value.toLowerCase() == p_value.toLowerCase()) {
        $(this).css('display', 'block');
      } else {
        $(this).css('display', 'none');
      }
    });
  }
}

function widthFilter() {
  if ($(window).width() > 740) {
    $('.aroundfilter').show();
  }
}

$(document).ready(function() {

  //width checkbox and radiobox
  $('.js-box-filter').each(function() {
    var max_width = 0;
    $(this).find('.js-item-filter').each(function() {
      var width_item = $(this).find('label').width();
      if (width_item > max_width) {
        max_width = width_item;
      }
    });
    if (max_width < 65) {
      $(this).addClass('element-line');
    }
    console.log('width:' + max_width);
  });

  // search
  $(document).off('click', '.f_search');
  $(document).on('keyup', '.f_search', function() {
    var $inputObj = $(this);
    ReFLY_SeachProp($inputObj);
  });

  $('.JS_tip').click(function(e) {

    e.stopPropagation();
    var $message = $(this).siblings('.bx_tip_text');

    if ($message.css('display') != 'block') {
      $message.show();

      $(document).on('click.myEvent', function(e) {
        if ($(e.target).closest('.bx_tip_text').length == 0) {
          $message.hide();
          $(document).off('click.myEvent');
        } else {
          e.stopPropagation();
        }
      });
    }

    e.preventDefault();
  });

  $('.bx_tip_text').on('click', function(e) {
    e.stopPropagation();
    console.log(e.target);
  });

  $('.fa-close').on('click', function() {
    $('.bx_tip_text').hide();
    $('.JS_tip').removeClass('active');
  });

  $('.js_mobile-button').on('click', function() {
    $('.aroundfilter').toggle();
    $(this).toggleClass('active');
  });

  $(window).resize(debounce(widthFilter, 250));
  widthFilter();
});

function JCSmartFilter(ajaxURL, viewMode, params) {
  console.log(params);
  this.ajaxURL = ajaxURL;
  this.form = null;
  this.timer = null;
  this.cacheKey = '';
  this.cache = [];
  this.viewMode = viewMode;
  this.sef = false;

  if (params && params.SEF_SET_FILTER_URL) {
    this.bindUrlToButton('set_filter', params.SEF_SET_FILTER_URL);
    this.sef = true;
  }

  if (params && params.SEF_DEL_FILTER_URL) {
    this.bindUrlToButton('del_filter', params.SEF_DEL_FILTER_URL);
  }

}

JCSmartFilter.prototype.bindUrlToButton = function(buttonId, url) {
  var button = BX(buttonId);
  if (button) {
    var proxy = function(j, func) {
      return function() {
        return func(j);
      }
    };

    if (button.type == 'submit')
      button.type = 'button';

    BX.bind(button, 'click', proxy(url, function(url) {
      window.location.href = url;
      return false;
    }));
  }
};

JCSmartFilter.prototype.keyup = function(input) {
  if (!!this.timer) {
    clearTimeout(this.timer);
  }
  this.timer = setTimeout(BX.delegate(function() {
    this.reload(input);
  }, this), 500);
};

JCSmartFilter.prototype.click = function(checkbox) {
  if (!!this.timer) {
    clearTimeout(this.timer);
  }

  this.timer = setTimeout(BX.delegate(function() {
    this.reload(checkbox);
  }, this), 500);
};

JCSmartFilter.prototype.reload = function(input) {
  if (this.cacheKey !== '') {
    //Postprone backend query
    if (!!this.timer) {
      clearTimeout(this.timer);
    }
    this.timer = setTimeout(BX.delegate(function() {
      this.reload(input);
    }, this), 1000);
    return;
  }
  this.cacheKey = '|';

  this.position = BX.pos(input, true);
  this.form = BX.findParent(input, {
    'tag': 'form'
  });
  if (this.form) {
    var values = [];
    values[0] = {
      name: 'ajax',
      value: 'y'
    };
    this.gatherInputsValues(values, BX.findChildren(this.form, {
      'tag': new RegExp('^(input|select)$', 'i')
    }, true));

    for (var i = 0; i < values.length; i++)
      this.cacheKey += values[i].name + ':' + values[i].value + '|';

    if (this.cache[this.cacheKey]) {
      this.curFilterinput = input;
      this.postHandler(this.cache[this.cacheKey], true);
    } else {
      this.curFilterinput = input;
      BX.ajax.loadJSON(
        this.ajaxURL,
        this.values2post(values),
        BX.delegate(this.postHandler, this)
      );
    }
  }
};

JCSmartFilter.prototype.updateItem = function(PID, arItem) {
  if (arItem.PROPERTY_TYPE === 'N' || arItem.PRICE) {
    var trackBar = window['trackBar' + PID];
    if (!trackBar && arItem.ENCODED_ID)
      trackBar = window['trackBar' + arItem.ENCODED_ID];

    if (trackBar && arItem.VALUES) {
      if (arItem.VALUES.MIN && arItem.VALUES.MIN.FILTERED_VALUE) {
        trackBar.setMinFilteredValue(arItem.VALUES.MIN.FILTERED_VALUE);
      }

      if (arItem.VALUES.MAX && arItem.VALUES.MAX.FILTERED_VALUE) {
        trackBar.setMaxFilteredValue(arItem.VALUES.MAX.FILTERED_VALUE);
      }
    }
  } else if (arItem.VALUES) {
    for (var i in arItem.VALUES) {
      if (arItem.VALUES.hasOwnProperty(i)) {
        var value = arItem.VALUES[i];
        var control = BX(value.CONTROL_ID);

        if (!!control) {
          var label = document.querySelector('[data-role="label_' + value.CONTROL_ID + '"]');
          if (value.DISABLED) {
            if (label) {
              BX.addClass(label, 'disabled');
              $(label).find("input").prop('disabled', true);
            } else
              BX.addClass(control.parentNode, 'disabled');
          } else {
            if (label) {
              BX.removeClass(label, 'disabled');
              $(label).find("input").removeAttr('disabled', true);
            } else
              BX.removeClass(control.parentNode, 'disabled');
          }

          if (value.hasOwnProperty('ELEMENT_COUNT')) {
            label = document.querySelector('[data-role="count_' + value.CONTROL_ID + '"]');
            if (label)
              label.innerHTML = value.ELEMENT_COUNT;
          }
        }
      }
    }
  }
};

JCSmartFilter.prototype.postHandler = function(result, fromCache) {
  var hrefFILTER, url, curProp;
  var modef = BX('modef');
  var modef_num = BX('modef_num');

  if (!!result && !!result.ITEMS) {
    for (var PID in result.ITEMS) {
      if (result.ITEMS.hasOwnProperty(PID)) {
        this.updateItem(PID, result.ITEMS[PID]);
      }
    }

    if (!!modef && !!modef_num) {
      modef_num.innerHTML = result.ELEMENT_COUNT;
      hrefFILTER = BX.findChildren(modef.parentNode, {
        tag: 'A'
      }, true);

      if (result.FILTER_URL && hrefFILTER) {
        hrefFILTER[0].href = BX.util.htmlspecialcharsback(result.FILTER_URL);
      }

      if (result.FILTER_AJAX_URL && result.COMPONENT_CONTAINER_ID) {
        BX.bind(hrefFILTER[0], 'click', function(e) {
          url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
          BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
          return BX.PreventDefault(e);
        });
      }

      if (result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID) {
        url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
        var $container = $("#" + result.COMPONENT_CONTAINER_ID);
        var $sorter = $("#" + result.COMPONENT_CONTAINER_ID + "_sorter");
        rsFlyaway.darken($container);
        BX.ajax({
          url: url,
          method: 'POST',
          dataType: 'json',
          data: {
            isAjax: 'Y',
            action: "updateElements",
          },
          onsuccess: function(json) {
            $(".js-no-products").remove();
            history.pushState(null, null, url);
            RSFlyAwayPutJSon(json);
            rsFlyaway.darken($container);
            /* init section */
            initCompare();
            rsFlyaway_SetInFavorite();
            initTimer();
            initViews();
            initSelect();
          },
          onfailure: function() {
            rsFlyaway.darken();
          }
        });
      } else {
        if ($(document).width() > 993) {
          if (modef.style.display === 'none') {
            clearTimeout(RSM_modefTimer);
            modef.style.display = 'inline-block';
            RSM_modefTimer = setTimeout(function() {
              modef.style.display = 'none';
            }, 4000);
          }
          if (this.viewMode != "ftype2") {
            curProp = BX.findChild(BX.findParent(this.curFilterinput, {
              'class': 'bx_filter_parameters_box'
            }), {
              'class': 'bx_filter_container_modef'
            }, true, false);
            curProp.appendChild(modef);
          }
        }

      }
      if (result.SEF_SET_FILTER_URL) {
        this.bindUrlToButton('set_filter', result.SEF_SET_FILTER_URL);
      }
    }

  }

  if (!fromCache && this.cacheKey !== '') {
    this.cache[this.cacheKey] = result;
  }
  this.cacheKey = '';
};

JCSmartFilter.prototype.gatherInputsValues = function(values, elements) {
  if (elements) {
    for (var i = 0; i < elements.length; i++) {
      var el = elements[i];
      if (el.disabled || !el.type)
        continue;

      switch (el.type.toLowerCase()) {
        case 'text':
        case 'textarea':
        case 'password':
        case 'hidden':
        case 'select-one':
          if (el.value.length)
            values[values.length] = {
              name: el.name,
              value: el.value
            };
          break;
        case 'radio':
        case 'checkbox':
          if (el.checked)
            values[values.length] = {
              name: el.name,
              value: el.value
            };
          break;
        case 'select-multiple':
          for (var j = 0; j < el.options.length; j++) {
            if (el.options[j].selected)
              values[values.length] = {
                name: el.name,
                value: el.options[j].value
              };
          }
          break;
        default:
          break;
      }
    }
  }
};

JCSmartFilter.prototype.values2post = function(values) {
  var post = [];
  var current = post;
  var i = 0;

  while (i < values.length) {
    var p = values[i].name.indexOf('[');
    if (p == -1) {
      current[values[i].name] = values[i].value;
      current = post;
      i++;
    } else {
      var name = values[i].name.substring(0, p);
      var rest = values[i].name.substring(p + 1);
      if (!current[name])
        current[name] = [];

      var pp = rest.indexOf(']');
      if (pp == -1) {
        //Error - not balanced brackets
        current = post;
        i++;
      } else if (pp == 0) {
        //No index specified - so take the next integer
        current = current[name];
        values[i].name = '' + current.length;
      } else {
        //Now index name becomes and name and we go deeper into the array
        current = current[name];
        values[i].name = rest.substring(0, pp) + rest.substring(pp + 1);
      }
    }
  }
  return post;
};

JCSmartFilter.prototype.hideFilterProps = function(element) {
  var easing;
  var obj = element.parentNode;

  if (BX.hasClass(obj, "active")) {
    BX.removeClass(obj, "active");
  } else {
    $('.smartfilter.ftype2').find('.bx_filter_prop').removeClass('active');
    BX.addClass(obj, "active");
  }
};

JCSmartFilter.prototype.showDropDownPopup = function(element, popupId) {
  var contentNode = element.querySelector('[data-role="dropdownContent"]');
  BX.PopupWindowManager.create("smartFilterDropDown" + popupId, element, {
    autoHide: true,
    offsetLeft: 0,
    offsetTop: -10,
    overlay: false,
    draggable: {
      restrict: true
    },
    closeByEsc: true,
    className: 'smartFilterSelectbox',
    content: contentNode
  }).show();
};

JCSmartFilter.prototype.selectDropDownItem = function(element, controlId) {
  this.keyup(BX(controlId));

  var wrapContainer = BX.findParent(BX(controlId), {
    className: "bx_filter_select_container"
  }, false);

  var currentOption = wrapContainer.querySelector('[data-role="currentOption"]');
  currentOption.innerHTML = element.innerHTML + '<i class="fa fa-angle-down hidden-xs icon-angle-down"></i>';
  BX.PopupWindowManager.getCurrentPopup().close();
};

JCSmartFilter.prototype.ftype1ShowOnSM = function() {
  var $smartFilter = $('.smartfilter');
  if (this.viewMode == 'ftype1' && $(document).width() < 992) {
    if (!$smartFilter.hasClass('fromftype1')) {
      $smartFilter
        .addClass('ftype2 fromftype1')
        .children('form')
        .children('ul')
        .addClass('row')
        .children('li:not(.buttons)')
        .addClass('col col-xs-12')
        .children('div')
        .removeClass('active');
      $smartFilter.find('li.buttoins').addClass('col col-xs-12');
    }
    $smartFilter.css('top', (parseInt($('.showfilter').offset().top) - parseInt($('.aroundfilter').offset().top) + 40));
  } else {
    if ($smartFilter.hasClass('fromftype1')) {
      $smartFilter
        .removeClass('ftype2 fromftype1')
        .removeAttr('style')
        .children('form')
        .children('ul')
        .removeClass('row')
        .children('li:not(.buttons)')
        .removeClass('col col-xs-12');
      $smartFilter.find('li.buttoins').removeClass('col col-xs-12');
    }
  }
};

BX.namespace("BX.Iblock.SmartFilter");
BX.Iblock.SmartFilter = (function() {
  var SmartFilter = function(arParams) {
    if (typeof arParams === 'object') {
      this.leftSlider = BX(arParams.leftSlider);
      this.rightSlider = BX(arParams.rightSlider);
      this.tracker = BX(arParams.tracker);
      this.trackerWrap = BX(arParams.trackerWrap);

      this.minInput = BX(arParams.minInputId);
      this.maxInput = BX(arParams.maxInputId);

      this.minPrice = parseFloat(arParams.minPrice);
      this.maxPrice = parseFloat(arParams.maxPrice);

      this.curMinPrice = parseFloat(arParams.curMinPrice);
      this.curMaxPrice = parseFloat(arParams.curMaxPrice);

      this.fltMinPrice = arParams.fltMinPrice ? parseFloat(arParams.fltMinPrice) : parseFloat(arParams.curMinPrice);
      this.fltMaxPrice = arParams.fltMaxPrice ? parseFloat(arParams.fltMaxPrice) : parseFloat(arParams.curMaxPrice);

      this.precision = arParams.precision || 0;

      this.priceDiff = this.maxPrice - this.minPrice;

      this.leftPercent = 0;
      this.rightPercent = 0;

      this.fltMinPercent = 0;
      this.fltMaxPercent = 0;

      this.colorUnavailableActive = BX(arParams.colorUnavailableActive); //gray
      this.colorAvailableActive = BX(arParams.colorAvailableActive); //blue
      this.colorAvailableInactive = BX(arParams.colorAvailableInactive); //light blue

      this.isTouch = false;

      this.init();

      if ('ontouchstart' in document.documentElement) {
        this.isTouch = true;

        BX.bind(this.leftSlider, "touchstart", BX.proxy(function(event) {
          this.onMoveLeftSlider(event)
        }, this));

        BX.bind(this.rightSlider, "touchstart", BX.proxy(function(event) {
          this.onMoveRightSlider(event)
        }, this));
      } else {
        BX.bind(this.leftSlider, "mousedown", BX.proxy(function(event) {
          this.onMoveLeftSlider(event)
        }, this));

        BX.bind(this.rightSlider, "mousedown", BX.proxy(function(event) {
          this.onMoveRightSlider(event)
        }, this));
      }

      BX.bind(this.minInput, "keyup", BX.proxy(function(event) {
        this.onInputChange();
      }, this));

      BX.bind(this.maxInput, "keyup", BX.proxy(function(event) {
        this.onInputChange();
      }, this));
    }
  };

  SmartFilter.prototype.init = function() {
    var priceDiff;

    if (this.curMinPrice > this.minPrice) {
      priceDiff = this.curMinPrice - this.minPrice;
      this.leftPercent = (priceDiff * 100) / this.priceDiff;

      this.leftSlider.style.left = this.leftPercent + "%";
      this.colorUnavailableActive.style.left = this.leftPercent + "%";
    }

    this.setMinFilteredValue(this.fltMinPrice);

    if (this.curMaxPrice < this.maxPrice) {
      priceDiff = this.maxPrice - this.curMaxPrice;
      this.rightPercent = (priceDiff * 100) / this.priceDiff;

      this.rightSlider.style.right = this.rightPercent + "%";
      this.colorUnavailableActive.style.right = this.rightPercent + "%";
    }

    this.setMaxFilteredValue(this.fltMaxPrice);
  };

  SmartFilter.prototype.setMinFilteredValue = function(fltMinPrice) {
    this.fltMinPrice = parseFloat(fltMinPrice);
    if (this.fltMinPrice >= this.minPrice) {
      var priceDiff = this.fltMinPrice - this.minPrice;
      this.fltMinPercent = (priceDiff * 100) / this.priceDiff;

      if (this.leftPercent > this.fltMinPercent)
        this.colorAvailableActive.style.left = this.leftPercent + "%";
      else
        this.colorAvailableActive.style.left = this.fltMinPercent + "%";

      this.colorAvailableInactive.style.left = this.fltMinPercent + "%";
    } else {
      this.colorAvailableActive.style.left = "0%";
      this.colorAvailableInactive.style.left = "0%";
    }
  };

  SmartFilter.prototype.setMaxFilteredValue = function(fltMaxPrice) {
    this.fltMaxPrice = parseFloat(fltMaxPrice);
    if (this.fltMaxPrice <= this.maxPrice) {
      var priceDiff = this.maxPrice - this.fltMaxPrice;
      this.fltMaxPercent = (priceDiff * 100) / this.priceDiff;

      if (this.rightPercent > this.fltMaxPercent)
        this.colorAvailableActive.style.right = this.rightPercent + "%";
      else
        this.colorAvailableActive.style.right = this.fltMaxPercent + "%";

      this.colorAvailableInactive.style.right = this.fltMaxPercent + "%";
    } else {
      this.colorAvailableActive.style.right = "0%";
      this.colorAvailableInactive.style.right = "0%";
    }
  };

  SmartFilter.prototype.getXCoord = function(elem) {
    var box = elem.getBoundingClientRect();
    var body = document.body;
    var docElem = document.documentElement;

    var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
    var clientLeft = docElem.clientLeft || body.clientLeft || 0;
    var left = box.left + scrollLeft - clientLeft;

    return Math.round(left);
  };

  SmartFilter.prototype.getPageX = function(e) {
    e = e || window.event;
    var pageX = null;

    if (this.isTouch && event.targetTouches[0] != null) {
      pageX = e.targetTouches[0].pageX;
    } else if (e.pageX != null) {
      pageX = e.pageX;
    } else if (e.clientX != null) {
      var html = document.documentElement;
      var body = document.body;

      pageX = e.clientX + (html.scrollLeft || body && body.scrollLeft || 0);
      pageX -= html.clientLeft || 0;
    }

    return pageX;
  };

  SmartFilter.prototype.recountMinPrice = function() {
    var newMinPrice = (this.priceDiff * this.leftPercent) / 100;
    newMinPrice = (this.minPrice + newMinPrice).toFixed(this.precision);

    if (newMinPrice != this.minPrice) {
      this.minInput.value = newMinPrice;
      $('#dubl_' + this.minInput.id).val(newMinPrice);
    } else {
      this.minInput.value = "";
    }
    smartFilter.keyup(this.minInput);
  };

  SmartFilter.prototype.recountMaxPrice = function() {
    var newMaxPrice = (this.priceDiff * this.rightPercent) / 100;
    newMaxPrice = (this.maxPrice - newMaxPrice).toFixed(this.precision);

    if (newMaxPrice != this.maxPrice) {
      this.maxInput.value = newMaxPrice;
      $('#dubl_' + this.maxInput.id).val(newMaxPrice);
    } else {
      this.maxInput.value = "";
    }
    smartFilter.keyup(this.maxInput);
  };

  SmartFilter.prototype.onInputChange = function() {
    var priceDiff;
    if (this.minInput.value) {
      var leftInputValue = this.minInput.value;
      if (leftInputValue < this.minPrice)
        leftInputValue = this.minPrice;

      if (leftInputValue > this.maxPrice)
        leftInputValue = this.maxPrice;

      priceDiff = leftInputValue - this.minPrice;
      this.leftPercent = (priceDiff * 100) / this.priceDiff;

      this.makeLeftSliderMove(false);
    }

    if (this.maxInput.value) {
      var rightInputValue = this.maxInput.value;
      if (rightInputValue < this.minPrice)
        rightInputValue = this.minPrice;

      if (rightInputValue > this.maxPrice)
        rightInputValue = this.maxPrice;

      priceDiff = this.maxPrice - rightInputValue;
      this.rightPercent = (priceDiff * 100) / this.priceDiff;

      this.makeRightSliderMove(false);
    }
  };

  SmartFilter.prototype.makeLeftSliderMove = function(recountPrice) {
    recountPrice = (recountPrice === false) ? false : true;

    this.leftSlider.style.left = this.leftPercent + "%";
    $(this.leftSlider).parent().parent().parent().find('.dubl-min-price').css('left', this.leftPercent - 8 + "%");
    this.colorUnavailableActive.style.left = this.leftPercent + "%";

    var areBothSlidersMoving = false;
    if (this.leftPercent + this.rightPercent >= 100) {
      areBothSlidersMoving = true;
      this.rightPercent = 100 - this.leftPercent;
      this.rightSlider.style.right = this.rightPercent + "%";
      this.colorUnavailableActive.style.right = this.rightPercent + "%";
    }

    if (this.leftPercent >= this.fltMinPercent && this.leftPercent <= (100 - this.fltMaxPercent)) {
      this.colorAvailableActive.style.left = this.leftPercent + "%";
      if (areBothSlidersMoving) {
        this.colorAvailableActive.style.right = 100 - this.leftPercent + "%";
      }
    } else if (this.leftPercent <= this.fltMinPercent) {
      this.colorAvailableActive.style.left = this.fltMinPercent + "%";
      if (areBothSlidersMoving) {
        this.colorAvailableActive.style.right = 100 - this.fltMinPercent + "%";
      }
    } else if (this.leftPercent >= this.fltMaxPercent) {
      this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + "%";
      if (areBothSlidersMoving) {
        this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
      }
    }

    if (recountPrice) {
      this.recountMinPrice();
      if (areBothSlidersMoving)
        this.recountMaxPrice();
    }
  };

  SmartFilter.prototype.countNewLeft = function(event) {
    var pageX = this.getPageX(event);

    var trackerXCoord = this.getXCoord(this.trackerWrap);
    var rightEdge = this.trackerWrap.offsetWidth;

    var newLeft = pageX - trackerXCoord;

    if (newLeft < 0)
      newLeft = 0;
    else if (newLeft > rightEdge)
      newLeft = rightEdge;

    return newLeft;
  };

  SmartFilter.prototype.onMoveLeftSlider = function(e) {
    if (!this.isTouch) {
      this.leftSlider.ondragstart = function() {
        return false;
      };
    }
    if (!this.isTouch) {
      document.onmousemove = BX.proxy(function(event) {
        this.leftPercent = ((this.countNewLeft(event) * 100) / this.trackerWrap.offsetWidth);
        this.makeLeftSliderMove();
      }, this);

      document.onmouseup = function() {
        document.onmousemove = document.onmouseup = null;
      };
    } else {
      document.ontouchmove = BX.proxy(function(event) {
        this.leftPercent = ((this.countNewLeft(event) * 100) / this.trackerWrap.offsetWidth);
        this.makeLeftSliderMove();
      }, this);

      document.ontouchend = function() {
        document.ontouchmove = document.touchend = null;
      };
    }

    return false;
  };

  SmartFilter.prototype.makeRightSliderMove = function(recountPrice) {
    recountPrice = (recountPrice === false) ? false : true;

    this.rightSlider.style.right = this.rightPercent + "%";
    $(this.rightSlider).parent().parent().parent().find('.dubl-max-price').css('right', this.rightPercent - 6 + "%");
    this.colorUnavailableActive.style.right = this.rightPercent + "%";

    var areBothSlidersMoving = false;
    if (this.leftPercent + this.rightPercent >= 100) {
      areBothSlidersMoving = true;
      this.leftPercent = 100 - this.rightPercent;
      this.leftSlider.style.left = this.leftPercent + "%";
      this.colorUnavailableActive.style.left = this.leftPercent + "%";
    }

    if ((100 - this.rightPercent) >= this.fltMinPercent && this.rightPercent >= this.fltMaxPercent) {
      this.colorAvailableActive.style.right = this.rightPercent + "%";
      if (areBothSlidersMoving) {
        this.colorAvailableActive.style.left = 100 - this.rightPercent + "%";
      }
    } else if (this.rightPercent <= this.fltMaxPercent) {
      this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
      if (areBothSlidersMoving) {
        this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + "%";
      }
    } else if ((100 - this.rightPercent) <= this.fltMinPercent) {
      this.colorAvailableActive.style.right = 100 - this.fltMinPercent + "%";
      if (areBothSlidersMoving) {
        this.colorAvailableActive.style.left = this.fltMinPercent + "%";
      }
    }

    if (recountPrice) {
      this.recountMaxPrice();
      if (areBothSlidersMoving)
        this.recountMinPrice();
    }
  };

  SmartFilter.prototype.onMoveRightSlider = function(e) {
    if (!this.isTouch) {
      this.rightSlider.ondragstart = function() {
        return false;
      };
    }

    if (!this.isTouch) {
      document.onmousemove = BX.proxy(function(event) {
        this.rightPercent = 100 - (((this.countNewLeft(event)) * 100) / (this.trackerWrap.offsetWidth));
        this.makeRightSliderMove();
      }, this);

      document.onmouseup = function() {
        document.onmousemove = document.onmouseup = null;
      };
    } else {
      document.ontouchmove = BX.proxy(function(event) {
        this.rightPercent = 100 - (((this.countNewLeft(event)) * 100) / (this.trackerWrap.offsetWidth));
        this.makeRightSliderMove();
      }, this);

      document.ontouchend = function() {
        document.ontouchmove = document.ontouchend = null;
      };
    }

    return false;
  };

  return SmartFilter;
})();

$(document).ready(function() {

  // close by click outside
  $(document).on('click', function(e) {
    if ($(e.target).parents('.smartfilter').length > 0 || $(e.target).hasClass('smartfilter') || $(e.target).hasClass('showfilter')) {

    } else {
      $('.smartfilter').removeClass('open');
    }

    if (
      $(e.target).parents('.bx_filter_parameters_box').length > 0 ||
      $(e.target).hasClass('bx_filter_parameters_box') ||
      $(e.target).parents('.bx_filter_name').length > 0 ||
      $(e.target).hasClass('bx_filter_name')
    ) {

    } else {
      $('.smartfilter.ftype2').find('.bx_filter_prop').removeClass('active');
    }
  });

  $(document).on('show.bs.dropdown', function() {
    $('.smartfilter').removeClass('open');
  });

  $(document).on('click', '.showfilter', function() {
    if ($('.smartfilter').hasClass('open')) {
      $('.smartfilter').removeClass('open');
    } else {
      $('.catalogsorter').trigger('click');
      $('.smartfilter').addClass('open');
    }
    return false;
  });

  smartFilter.ftype1ShowOnSM();
  $(window).on('resize', function() {
    smartFilter.ftype1ShowOnSM();
  });

});
