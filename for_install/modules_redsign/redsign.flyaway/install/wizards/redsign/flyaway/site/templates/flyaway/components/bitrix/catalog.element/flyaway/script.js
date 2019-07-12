function updateDelivery($element) {
  var $quantityInput = $element.find(".js-quantity"),
    productId = $quantityInput.closest(".js-buyform").find(".js-add2basketpid").val(),
    quantity = $quantityInput.val(),
    $deliveryBlock = $element.find(".product-delivery");

  var beforeAfterFn = function() {
    rsFlyaway.darken($deliveryBlock);
  }

  BX.onCustomEvent('rs_delivery_update', [productId, quantity, beforeAfterFn, beforeAfterFn]);
}

var updateDeliveryDebounce =  BX.debounce(updateDelivery, 5000);

// SKU
function rsFlyawayOfferChange($element) {
  var elementId = $element.data('elementid'),
    ce = new CatalogElement(elementId),
    $carousel = $element.find(".js-detail-carousel"),
    $picture,
    i,
    length;

  $carousel.find(".js-detail-carousel-nav").css('transform', '');;

  /* Pictures */
  var pictures = (rsFlyaway.products[elementId]) ? rsFlyaway.products[elementId].pictures : [];

  for (i = 0, length = $carousel.find(".owl-item:not(.cloned)").length; i < length; i++) {
    $carousel.trigger('remove.owl.carousel', i);
  }

  pictures.forEach(function(picture) {
    if (
      picture.DATA.OFFER_ID == ce.selectedOffer ||
      picture.DATA.OFFER_ID == 0
    ) {
      $picture = $('<div></div>')
        .addClass("preview-wrap")
        .attr('data-dot', "<img class='owl-preview' data-picture-id='" + picture.PIC.ID + "' src='" + picture.PIC.SRC + "'>")
        .append(
          $("<a></a>")
          .addClass("js-open_popupgallery")
          .attr('href', window.location.pathname)
          /**.append(
              $("<div>")
              .addClass("preview")
              .css("background-image", 'url(' + picture.PIC.SRC + ')')
          )**/
          .append(
            $('<img>')
            .attr('src', picture.PIC.SRC)
          )
        );

      $carousel.trigger("add.owl.carousel", $picture).trigger('refresh.owl.carousel').resize();

    }

  });

  $carousel.trigger('refresh.owl.carousel');
  setMaxHeightDetailImages();

  /* Stocks */
  var stocks = (rsFlyaway.stocks[elementId].SKU) ? rsFlyaway.stocks[elementId].SKU[ce.selectedOffer] : undefined,
    $stores = $element.find(".js-stores .stores-table"),
    $store,
    stockId,
    quantity;

  if (stocks) {
    for (stockId in stocks) {
      $store = $stores.find(".store_" + stockId);
      quantity = stocks[stockId];

      if (quantity > 0 && quantity > rsFlyaway.stocks[elementId].LOW_QUANTITY) {

        $store.find(".stores-icon").removeClass('stores-mal').addClass("stores-full");
        $store.find(".genamount")
          .removeClass("empty").addClass("isset")
          .html(rsFlyaway.stocks[elementId].MESSAGES.MESSAGE_ISSET);

      } else if (quantity > 0) {
        $store.find(".stores-icon").removeClass('stores-full').addClass("stores-mal");
        $store.find(".genamount")
          .removeClass("empty isset")
          .html(rsFlyaway.stocks[elementId].MESSAGES.MESSAGE_LOW);
      } else {
        $store.find(".stores-icon").removeClass("stores-full stores-mal");
        $store.find(".genamount")
          .removeClass("isset").addClass("empty")
          .html(rsFlyaway.stocks[elementId].MESSAGES.MESSAGE_EMPTY);
      }
    }
  }

  updateDeliveryDebounce($element);
}

function setMaxHeightDetailImages(maxHeight) {

  var maxHeight = maxHeight || $(".product-detail-carousel__images").css('height');

  $(".js-detail-carousel img").css({
    'max-height': maxHeight
  });
}

$(document).ready(function() {

  setMaxHeightDetailImages();

  function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
  }

  var offerId = getURLParameter('offer_id');
  if (offerId) {
    var ce = new CatalogElement($('.js-detail').data('elementid'));
    ce.selectOfferById(offerId)
    if(ce.getOffer()) {
      updateProductDataAfterOffersChanged($('.js-detail'));
    }
  }

  // add this element to viewed list
  $(window).load(function() {
    setTimeout(function() {
      var viewedUrl = '/bitrix/components/bitrix/catalog.element/ajax.php';
      var viewedData = {
        AJAX: 'Y',
        SITE_ID: SITE_ID,
        PARENT_ID: $('.js-detail').data('elementid'),
        PRODUCT_ID: $('.js-detail').find('.js-add2basketpid').val()
      };
      $.ajax({
        type: 'POST',
        url: viewedUrl,
        data: viewedData
      }).done(function(response) {
        console.warn('Element add to viewed');
      }).fail(function() {
        console.warn('Element can\'t add to viewed');
      });
    }, 500);
  });

  $(document).on("changeOffer.rs.flyaway", function(event, $element) {
    rsFlyawayOfferChange($element);
  });

  $(".product-bar").closest(".maincontent").css("min-height", $(".product-bar").height() + 150);
  $(window).resize(debounce(function() {
    if ($(window).width() > rsFlyaway.breakpoints.md) {
      $(".product-bar").closest(".maincontent").css("min-height", $(".product-bar").height() + 150);
    } else {
      $(".product-bar").closest(".maincontent").css("min-height", "");
    }

    setMaxHeightDetailImages();
  }, 250));

  var $detailCarousel = $(".js-detail-carousel");

  $.expr[':'].visibleDotFilter = function(elem, index, match) {
    var $nav = $(elem).closest(".js-detail-carousel-nav").parent();

    if ($nav.length === 0) {
      return false;
    }

    var navOffsetTop = $nav.offset().top;

    return $(elem).offset().top >= navOffsetTop && $(elem).offset().top + $(elem).outerHeight() <= $nav.outerHeight() + navOffsetTop;
  };

  var addTransformValue = function($el, val) {
    var currentTransformValue,
      translateY;

    currentTransformValue = $el.css('transform');

    if (!currentTransformValue || currentTransformValue == 'none') {
      translateY = val;
    } else if (currentTransformValue) {
      translateY = parseInt(currentTransformValue.split(',')[5], 10) + val;
    } else {
      translateY = 0;
    }

    $el.css('transform', 'translateY(' + translateY + 'px)');
  };

  $detailCarousel.owlCarousel({
    items: 1,
    nav: false,
    dots: true,
    dotsData: true,
    dotsContainer: '.js-detail-carousel-nav',
    onRefreshed: function() {
      var $dotsContainer = $(this.settings.dotsContainer);
      $dotsContainer.css('transform', '');
    },
    onChanged: function() {
      /* Переделать когда нибудь */
      var $dotsContainer = $(this.settings.dotsContainer),
        $dots = $dotsContainer.find(".owl-dot"),
        $activeDot = $dots.filter(".active:eq(0)"),
        translateY = 0;

      if (
        $activeDot.next(".owl-dot").length > 0 &&
        !$dots.filter(":visibleDotFilter").is($activeDot.next(".owl-dot"))
      ) {
        translateY -= ($activeDot.next('.owl-dot').offset().top - $activeDot.next('.owl-dot').parent().offset().top) - ($activeDot.offset().top - $activeDot.parent().offset().top);
      } else if (
        $activeDot.prev(".owl-dot").length > 0 &&
        !$dots.filter(":visibleDotFilter").is($activeDot.prev(".owl-dot"))
      ) {
        translateY -= ($activeDot.prev('.owl-dot').offset().top - $activeDot.prev('.owl-dot').parent().offset().top) - ($activeDot.offset().top - $activeDot.parent().offset().top);
      }

      addTransformValue($dotsContainer, translateY);
      /* /Переделать когда нибудь */
    }
  });

  $("#product-detail-tabs").tabCollapse({
    tabsClass: 'hidden-sm hidden-xs',
    accordionClass: 'visible-sm visible-xs mobile-props'
  });
  $("#product-detail-tabs .tabs-item:eq(0) a").click();

  $(document).on('shown.bs.collapse', ".panel-collapse", function() {
    window.location.hash = this.id;
  });

  $('.reviews_mob').on('click', function() {
    $('#form_reviews').toggle();
  });

  $(document).on('click', '.js-open_popupgallery', function(e) {
    e.preventDefault();

    var $this = $(this),
      elementId = $this.closest(".js-element").data('elementid'),
      ce = new CatalogElement(elementId),
      popupAjaxData = {
        AJAX_CALL: 'Y',
        POPUP_GALLERY: 'Y'
      };

    if (ce.selectedOffer) {
      popupAjaxData['OFFER_ID'] = ce.selectedOffer;
    } else {
      popupAjaxData['OFFER_ID'] = $this.closest(".js-element").data('curerntofferid');
    }

    openPopup(
      $this,
      'wide',
      $.extend({}, {
        type: 'ajax',
        cache: false,
        title: $this.attr('title'),
        ajax: {
          dataType: 'html',
          headers: {
            'X-fancyBox': true
          },
          data: popupAjaxData
        },
        helpers: {
          title: {
            type: 'inside',
            position: 'top'
          }
        },
        href: $this.closest('.js-element').data('detailpageurl'),
        afterShow: function() {
          var pictureId = $(".js-detail-carousel-nav .owl-dot.active img").data('pictureId');

          if (pictureId) {
            this.inner.find(".thumbs .pic" + pictureId + " a").click();
          }

          this.inner.find(".thumbs .thumb a").on("click.popupgallery_change_image", function(e) {
            var pictureId = $(this).data("index");
            console.log(pictureId)
            $this.closest(".js-element").find(".js-detail-carousel-nav .owl-dot img[data-picture-id=" + pictureId + "]").click();
          });
        },
        beforeClose: function() {
          console.log(this.inner);
        }
      })
    );
  });

  /* Update delivery block */
  $(".js-select-input.js-quantity").on('change', function() {
    updateDelivery($(this).closest(".js-element"));

    var addMeasureFactor = $(this).data('add-measure-factor'),
        $jsDetail,
        addMeasurePrice;

    if (addMeasureFactor) {
      $jsDetail = $('.js-detail');
      $jsDetail.find('.js-additional-factor').text(this.value * addMeasureFactor);
      addMeasurePrice = $('.js-detail').find('.js-add-measure__price').data('price');
      $jsDetail.find('.js-add-measure__total').text(BX.Currency.currencyFormat(this.value * addMeasurePrice, BX.Currency.defaultCurrency, true));
    }
  });
  $('.js-calc_delivery').on('click', function() {
    updateDelivery($(this).closest(".js-element"));
  })
});
