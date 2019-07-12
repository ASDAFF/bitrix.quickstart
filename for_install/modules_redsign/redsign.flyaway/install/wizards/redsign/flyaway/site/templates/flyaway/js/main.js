/*--GLOBAL--*/
var rsFlyaway = rsFlyaway || {};

rsFlyaway.stocks = rsFlyaway.stocks || {};
rsFlyaway.breakpoints = {
    xs: 0,
    sm: 728,
    md: 1007
};

rsFlyaway.parseData = function parseData(data) {
    try {
        data = JSON.parse(data.replace(/'/gim, '"'));
    } catch (e) {
        data = {};
    }
    return data;
};

rsFlyaway.stopEvent = function(event, mode) {
    if (arguments.length == 0 || !event || !jQuery) {
        return;
    } else {
        if ((arguments.length == 1 || ('' + mode).toLowerCase().search(/(^|\|)propagation($|\|)/) != -1) && jQuery.isFunction(event.stopPropagation)) {
            event.stopPropagation();
        }
        if ((arguments.length == 1 || ('' + mode).toLowerCase().search(/(^|\|)default($|\|)/) != -1) && jQuery.isFunction(event.preventDefault)) {
            event.preventDefault();
        }
        if ((arguments.length == 2 && ('' + mode).toLowerCase().search(/(^|\|)immediate($|\|)/) != -1) && jQuery.isFunction(event.stopImmediatePropagation)) {
            event.stopImmediatePropagation();
        }
    }
};

// Darken
rsFlyaway.darken = function darken(object) {
        object.toggleClass('area2darken');
    }
    /*--/global--*/

/*-- Adaptive Main Menu --*/
function rsFlyawayAdaptiveMainMenu() {
    var $menu = $(".mainJS"),
        maxMenuWidth = $($menu).outerWidth(),
        $other = $menu.find(".other"),
        lastVisibleIndex = 0,
        itemsWidth = $other.outerWidth() + 15;

    if ($menu.length === 0) {
        return;
    }

    $menu.css('visibility', 'hidden');

    if ($(window).width <= rsFlyaway.breakpoints.sm) {
        $menu.find(".lvl1").removeClass('invisible');
        $other.addClass("invisible").find(".main").remove();
        return;
    }

    $menu.find(".lvl1").removeClass('invisible').each(function(index, item) {
        if (itemsWidth + $(item).outerWidth() <= maxMenuWidth) {
            itemsWidth += $(item).outerWidth();
            lastVisibleIndex++;
        } else {
            return false;
        }
    });

    $other.find(".mainmenu__other-item").remove();
    $menu.find(".lvl1:gt(" + --lastVisibleIndex + ")").addClass("invisible").each(function(index, item) {
        $other.find("ul.mainmenu__other-list").append(
            $("<li>")
            .addClass("mainmenu__other-item")
            .attr("id", $(item).attr('id'))
            .html($(item).html())
        );
    });

    if ($other.find(".mainmenu__other-item").length > 0) {
        $other.removeClass("invisible");
    } else {
        $other.addClass("invisible");
    }

    $menu.css('visibility', 'visible');
}
/*-- /Adaptive Main Menu --*/

/*--INIT--*/
function initMainMenu() {
    // main menu
    $(document).on('click', '.main-nav .dropdown a > span', function() {
        $(this).parent().parent().toggleClass('open');
        return false;
    });

    // click at first main menu
    $(document).on('show.bs.dropdown', '.main-nav li.dropdown, .main-nav li.dropdown > a', function(e) {
        console.warn('script.js -> preventDefault');
        e.preventDefault();
    });
}

function initSidebarMenu() {
    // click at sidebar menu
    $(document).on('shown.bs.collapse', '.nav-side', function(e) {
        $(e.target).parent().addClass('active');
    }).on('hidden.bs.collapse', '.nav-sidebar', function(e) {
        $(e.target).parent().removeClass('active');
    });
}

function RSFLYAWAY_PopupGallerySetHeight() {
    if ($('.popupgallery').length > 0) {
        if ($(document).width() > 767) {
            var innerHeight = parseInt($('.popupgallery').parents('.fancybox-inner').height()),
                h1 = innerHeight - 55,
                h2 = h1 - 30,
                h3 = innerHeight - 55 - parseInt($('.popupgallery').find('.preview').height());
            $('.popupgallery').find('.thumbs.style1').css('maxHeight', h3);
        } else {
            var fullrightHeight = parseInt($('.popupgallery').find('.fullright').height());
            var innerHeight = parseInt($('.popupgallery').parents('.fancybox-inner').height()),
                h1 = innerHeight - 55 - fullrightHeight - 25,
                h2 = h1 - 30 - fullrightHeight - 25,
                h3 = innerHeight - 55 - parseInt($('.popupgallery').find('.preview').height());
            $('.popupgallery').find('.thumbs.style1').css('maxHeight', 100);
        }
        $('.popupgallery').find('.changeit').css('height', h1);
        $('.popupgallery').find('.changeit').find('img').css('maxHeight', h2);
    }
}

function RSFLYAWAY_PopupGallerySetPicture() {
    if ($('.popupgallery').length > 0) {
        $('.js-gallery').find('.thumbs').find('a[href="' + $('.changeFromSlider:not(.cantopen)').find('img').attr('src') + '"]').trigger('click');
    }
}

/*--FANCYBOX--*/
rsFlyaway.fancybox = rsFlyaway.fancybox || {};
rsFlyaway.fancybox.common = rsFlyaway.fancybox.common || {};
rsFlyaway.fancybox.common.fitToView = true;
rsFlyaway.fancybox.common.tpl = rsFlyaway.fancybox.common.tpl || {};

rsFlyaway.fancybox.popup = rsFlyaway.fancybox.popup || {};
rsFlyaway.fancybox.popup.wrapCSS = 'fancy-popup';
rsFlyaway.fancybox.popup.maxWidth = 400;
rsFlyaway.fancybox.popup.maxHeight = 750;
rsFlyaway.fancybox.popup.minWidth = 200;
rsFlyaway.fancybox.popup.minHeight = 100;
rsFlyaway.fancybox.popup.openEffect = 'none';
rsFlyaway.fancybox.popup.closeEffect = 'none';
rsFlyaway.fancybox.popup.padding = [20, 24, 24, 24];
rsFlyaway.fancybox.popup.helpers = {
    title: {
        type: 'inside',
        position: 'top'
    }
};
rsFlyaway.fancybox.popup.beforeShow = function() {
    var $element = $(this.element);
    if ($element.data('insertdata') != '' && (typeof($element.data('insertdata')) == 'object')) {
        setTimeout(function() {
            var obj = $element.data('insertdata');
            for (fieldName in obj) {
                $('.fancybox-inner').find('[name="' + fieldName + '"]').val(obj[fieldName]);
            }
        }, 50);
    }
    $(document).trigger('RSFLYAWAY_fancyBeforeShow');
};
rsFlyaway.fancybox.popup.afterShow = function() {
    setTimeout(function() {
        $.fancybox.toggle();
        RSFLYAWAY_PopupGallerySetHeight();
        RSFLYAWAY_PopupGallerySetPicture();
        $(document).trigger('RSFLYAWAY_fancyAfterShow');
    }, 50);
};
rsFlyaway.fancybox.popup.onUpdate = function() {
    setTimeout(function() {
        RSFLYAWAY_PopupGallerySetHeight();
        $(document).trigger('RSFLYAWAY_fancyOnUpdate');
    }, 50);
};

rsFlyaway.fancybox.ajax = rsFlyaway.fancybox.ajax || {};
rsFlyaway.fancybox.ajax.type = 'ajax';
rsFlyaway.fancybox.ajax.cache = false;
rsFlyaway.fancybox.ajax.data = {
    'AJAX_CALL': 'Y',
    'POPUP_GALLERY': 'Y'
};

rsFlyaway.fancybox.wide = rsFlyaway.fancybox.wide || {};
delete rsFlyaway.fancybox.wide.maxWidth;
rsFlyaway.fancybox.wide.maxWidth = 1091;
rsFlyaway.fancybox.wide.minWidth = 600;
rsFlyaway.fancybox.wide.width = '90%';
rsFlyaway.fancybox.wide.autoSize = false;
rsFlyaway.fancybox.wide.autoHeight = true;

function initPopup() {
    if (!jQuery.fancybox) {
        return;
    }

    jQuery('.JS-Popup').fancybox(jQuery.extend({}, rsFlyaway.fancybox.common, rsFlyaway.fancybox.popup, {}));

    jQuery('.JS-Popup-Ajax').fancybox(jQuery.extend({}, rsFlyaway.fancybox.common, rsFlyaway.fancybox.popup, rsFlyaway.fancybox.ajax, {}));

    jQuery('.JS-Popup-Wide').fancybox(jQuery.extend({}, rsFlyaway.fancybox.common, rsFlyaway.fancybox.popup, rsFlyaway.fancybox.ajax, rsFlyaway.fancybox.wide, {}));
}

function openPopup(popup, mode, params) {
    jQuery.fancybox(jQuery(popup), jQuery.extend({}, rsFlyaway.fancybox.common, rsFlyaway.fancybox[mode] || rsFlyaway.fancybox.popup, params || {}));
}
/*--/fancybox--*/

function initCompare() {
    if (typeof(Compare) === 'undefined' || !jQuery.isFunction(Compare)) {
        return false;
    }

    var common = {};

    jQuery('.js-compare').not('.js-compare-ready').each(function() {
        var local = rsFlyaway.parseData(jQuery(this).data('compare'));
        new Compare(this, jQuery.extend({}, common, local));
    });
}

function initToggle() {
    if (typeof(Toggle) === 'undefined' || !jQuery.isFunction(Toggle)) {
        return false;
    }

    var common = {};

    jQuery('.js-toggle').not('.js-toggle-ready').each(function() {
        var local = rsFlyaway.parseData(jQuery(this).data('toggle'));
        new Toggle(this, jQuery.extend({}, common, local));
    });
}

// select city
function RSMSHOPSelectCity(input, city_id) {
    var $input = $(input),
        $form = $input.closest('form');
    $form.find('input[name="' + city_id + '"]').val($input.val());
    $form.find('input[type="submit"]').trigger('click');
}

function initFix() {
    if (typeof(Fix) === 'undefined' || !jQuery.isFunction(Fix)) {
        return false;
    }

    var common = {};

    jQuery('.js-fix').not('.js-fix-ready').each(function() {
        var local = rsFlyaway.parseData(jQuery(this).data('fix'));
        new Fix(this, jQuery.extend({}, common, local));
    });
}

function initViews() {
    if (typeof(Views) === 'undefined' || !jQuery.isFunction(Views)) {
        return false;
    }

    var common = {};

    jQuery('.js-views').not('.js-views-ready').each(function() {
        var local = rsFlyaway.parseData(jQuery(this).data('views'));
        new Views(this, jQuery.extend({}, common, local));
    });
}

// UPDATE BASKET AND FAVORITE
function ajaxBasket(obj, classPaste) {
    url = SITE_DIR;
    if (obj == 'fav') {
        action = 'REFRESH_FAVORITE';
        data = {
            'AJAX': 'Y',
            'REFRESH_FAVORITE': 'Y'
        };
    } else if (obj == 'basket') {
        data = {
            'AJAX': 'Y',
            'REFRESH_BASKET_PC': 'Y'
        };
    }
    $.post(url, data, function(data) {
        classPaste.html(data);

        if (obj == 'fav') {
            jQuery('.js-informer-switcher').on('click', function(e) {
                rsFlyaway.stopEvent(e);
            });

            initCompare();
        }
    });
    return false;
}

function initSlider() {
    // pictures slider
    $(document).on('click', '.thumbs .thumb a', function() {
        var $link = $(this);
        var $thumbs = $link.parents('.thumbs');
        $thumbs.find('.thumb').removeClass('checked');
        $thumbs.find('.thumb.pic' + $link.data('index')).addClass('checked');
        $($thumbs.data('changeto')).attr('src', $(this).attr('href'));

        $(document).trigger('RSMONOPOLY_changePicture', {
            id: $(this).data("index")
        });
        return false;
    });
    $(document).on('click', '.js-nav', function() {
        var $btn = $(this),
            $gallery = $(this).parents('.js-gallery'),
            $curPic = $gallery.find('.thumb.checked'),
            $prev = ($curPic.prev().hasClass('thumb') ? $curPic.prev() : $gallery.find('.thumb:last')),
            $next = ($curPic.next().hasClass('thumb') ? $curPic.next() : $gallery.find('.thumb:first'));
        if ($btn.hasClass('prev')) {
            $prev.find('a').trigger('click');
        } else {
            $next.find('a').trigger('click');
        }
        return false;
    }).on('mouseenter mouseleave', '.js-nav', function() {
        $('html').toggleClass('disableSelection');
    });
    $(document).on('click', '.popupgallery .changeit img', function() {
        $('.popupgallery').find('.js-nav.next').trigger('click');
    });
}

function mobileForm() {
    if ($(document).width() < rsFlyaway.breakpoints.sm) {
        $('.JS-Popup-Ajax').removeClass('JS-Popup-Ajax').addClass('fancyajaxwait');
        $('.JS-Popup').removeClass('JS-Popup').addClass('fancyajaxwaitP');
        $('.JS-Popup-Wide').removeClass('JS-Popup-Wide').addClass('fancyajaxwaitW');
        $('#reviews .form-title').addClass('reviews_mob');

        $(document).on('click.fancyajaxwait', 'a.fancyajaxwait, a.fancyajaxwaitP, a.fancyajaxwaitW', function(e) {
            var $element = $(this),
                data = $element.data('insertdata');

            BX.localStorage.set('ajax_data', data);
        });
    } else {
        $('.fancyajaxwait').removeClass('fancyajaxwait').addClass('JS-Popup-Ajax');
        $('.fancyajaxwaitP').removeClass('fancyajaxwaitP').addClass('JS-Popup');
        $('.fancyajaxwaitW').removeClass('fancyajaxwaitW').addClass('JS-Popup-Wide');
        $('#reviews .form-title').removeClass('reviews_mob');

        $(document).off('click.fancyajaxwait');
    }
}

function initReviews() {
    $(window).resize(function() {
        mobileForm();
    });

    $('.js-reviews__link').on('click', function() {
        var thisL = $(this).attr('data-id');
        $(this).hide();
        $(this).parent().find('.js-reviews__link-close').show();
        //$('.reviews__link-close').attr('data-ids', thisL).show();
        $('.js-reviews__detail-info').each(function() {
            if ($(this).attr('id') == thisL) {
                $(this).find('.js-reviews__detail-text').show();
                $(this).find('.js-reviews__detail-content').hide();
            }
        });
    });

    $('.js-reviews__link-close').on('click', function() {
        var thisC = $(this).attr('data-ids');
        $(this).hide();
        $(this).parent().find('.js-reviews__link').show();
        //$('.reviews__link').attr('data-id', thisC).show();
        $('.js-reviews__detail-info').each(function() {
            if ($(this).attr('id') == thisC) {
                $(this).find('.js-reviews__detail-text').hide();
                $(this).find('.js-reviews__detail-content').show();
            }
        });
    });
}

// AjaxPages
function RSFlyAwayPutJSon(json, put) {
    console.log('RSFlyAwayPutJSon');
    console.log(json);
    console.log( 'put = ' + put );
    if (json.HTMLBYID) {
        for (var key in json.HTMLBYID) {
            if ($('#' + key)) {
                console.log(key);
                if (put && put == key+'__append') {
                    console.log('RSFlyAwayPutJSon - append - ' + '#' + key);
                    $('#' + key).append(json.HTMLBYID[key]);
                } else if (put && put == key+'__prepend') {
                    console.log('RSFlyAwayPutJSon - prepend - ' + '#' + key);
                    $('#' + key).prepend(json.HTMLBYID[key]);
                } else {
                    console.log('RSFlyAwayPutJSon - html - ' + '#' + key);
                    $('#' + key).html(json.HTMLBYID[key]);
                }
            }
        }
    }
}

function initAjaxpages() {
    // AJAXPAGES
    $(document).on('click', '.ajaxpages a', function() {
        var $linkObj = $(this);
        var ajaxurl = $linkObj.data('ajaxurl');
        var ajaxpagesid = $linkObj.data('ajaxpagesid');
        var navpagenomer = $linkObj.data('navpagenomer');
        var navpagecount = $linkObj.data('navpagecount');
        var navnum = $linkObj.data('navnum');
        var nextpagenomer = parseInt(navpagenomer) + 1;
        var url = "";

        if ($('#' + ajaxpagesid).length > 0 && navpagenomer < navpagecount && parseInt(navnum) > 0 && ajaxurl != "") {

            if (ajaxurl.indexOf("?") < 1) {
                url = ajaxurl + '?isAjax=Y&action=updateElements&ajaxpagesid=' + ajaxpagesid + '&PAGEN_' + navnum + '=' + nextpagenomer;
            } else {
                url = ajaxurl + '&isAjax=Y&action=updateElements&ajaxpagesid=' + ajaxpagesid + '&PAGEN_' + navnum + '=' + nextpagenomer;
            }

            $linkObj.button('loading');

            $.getJSON(url, {}, function(json) {
                RSFlyAwayPutJSon(json, ajaxpagesid+'__append');
            }).fail(function(json) {
                console.warn('ajaxpages - error responsed');
            }).always(function() {
                setTimeout(function() { // fix for slow shit
                    window.history.replaceState({}, '', ajaxurl + '?PAGEN_' + navnum + '=' + nextpagenomer);
                }, 50);
            });
        } else {
            if (!($('#' + ajaxpagesid).length > 0)) {
                console.warn('AJAXPAGES: ajaxpages -> empty DOM element');
            }

            if (!(navpagenomer < navpagecount)) {
                console.warn('AJAXPAGES: ajaxpages -> navpagenomer !< navpagecount');
            }

            if (!(parseInt(navnum) > 0)) {
                console.warn('AJAXPAGES: ajaxpages -> parseInt(navnum)!>0');
            }

            if (!(ajaxurl != "")) {
                console.warn('AJAXPAGES: ajaxpages -> ajaxurl is empty');
            }
        }
        return false;
    });
}
/*--/ajax_pages--*/

function initTop() {
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('.js-top').fadeIn();
        } else {
            $('.js-top').fadeOut();
        }
    });

    $('.js-top').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 600);
        return false;
    });
}

function owlInit($owl, params) {

    var defaultParams = {
        items: 4,
        margin: 30,
        loop: true,
        autoplay: false,
        merge: true,
        nav: true,
        navText: ['<span></span>', '<span></span>'],
        navClass: ['prev', 'next'],
        responsive: {},

        onInitialize: function(e) {
            $owl.addClass('owl-carousel owl-theme');
            if (this.$element.children().length <= this.settings.items) {
                this.settings.loop = false;
            }
        },
        onResize: function(e) {
            if ($owl.data('loop') != "false" || $owl.data('loop')) {
                var responsiveItems = this.settings.items,
                    windowWidth = $(window).width();

                if (this.options.responsive) {
                    var currentBreakpoint = -1,
                        breakpoint;

                    for (breakpoint in this.options.responsive) {
                        if (breakpoint <= windowWidth && breakpoint >= currentBreakpoint) {
                            if (this.options.responsive[breakpoint].items) {
                                responsiveItems = this.options.responsive[breakpoint].items;
                            }
                        }
                    }
                }

                if (this.items().length <= responsiveItems) {
                    this.options.loop = false;
                } else {
                    this.options.loop = true;
                }
            }
        },
    };

    params = $.extend({}, defaultParams, params);

    return $owl.owlCarousel(params);
}

function initOwl() {
    var $owlProducts = $(".news-owl-slider");
    owlInit($owlProducts, {
        margin: 15,
        items: 5,
        nav: true,
        navigation: true,
        responsive: {
            "0": {
                "items": "1"
            },
            "500": {
                "items": "2"
            },
            "850": {
                "items": "3"
            },
            "956": {
                "items": "4"
            },
            "1550": {
                "items": "5"
            }
        }
    });

    var $owlProducts = $(".owlslider.products-owl");
    owlInit($owlProducts, {
        margin: 20,
        items: 5,
        navigation: true,
        responsive: {
            "0": {
                "items": "1"
            },
            "500": {
                "items": "2"
            },
            "850": {
                "items": "3"
            },
            "956": {
                "items": "4"
            },
            "1550": {
                "items": "5"
            }
        }
    });

    var $owlProducts = $(".owlslider.products-owl-new");
    owlInit($owlProducts, {
        margin: 20,
        items: 6,
        navigation: true,
        responsive: $owlProducts.data('sidebar') ? {
            "0": {
                "items": "1"
            },
            "320": {
                "items": "2"
            },
            "768": {
                "items": "3"
            },
            "1550": {
                "items": "4"
            }
        } : {
            "0": {
                "items": "1"
            },
            "500": {
                "items": "2"
            },
            "756": {
                "items": "3"
            },
            "956": {
                "items": "4"
            },
            "1550": {
                "items": "6"
            }
        }
    });
    var $owlProducts = $(".owlslider.productsmini-owl-slider");
    owlInit($owlProducts, {
        margin: 20,
        items: 6,
        navigation: true,
        responsive: {
            "0": {
                "items": "1"
            },
            "480": {
                "items": "2"
            },
            "956": {
                "items": "3"
            },
            "1550": {
                "items": "4"
            }
        }
    });

    var $owlCollection = $(".js-collection-items");
    owlInit($owlCollection, {
        margin: 20,
        items: 6,
        navigation: true,
        responsive: {
            "0": {
                "items": "1"
            },
            "320": {
                "items": "2"
            },
            "480": {
                "items": "3"
            },
            "720": {
                "items": "4"
            },
            "1200": {
                "items": "5"
            },
            "1550": {
                "items": "6"
            }
        },
        onInitialized: function() {
            if(!this.$element .closest(".tab-pane").hasClass("active")) {
                this.$element.find(".rs-collection-item").css('visibility', 'hidden');
            }
        },
        onRefreshed: function() {
            this.$element.find(".rs-collection-item").css('visibility', 'visible');
        }
    });

    var $owlProducts = $(".owlslider");
    owlInit($owlProducts, {
        margin: 15,
        items: 5,
        navigation: true,
        responsive: $owlProducts.data('responsive') || {
            "0": {
                "items": "1"
            },
            "768": {
                "items": "3"
            },
            "956": {
                "items": "5"
            }
        }
    });
}

// TIMER
function timerCanDelete(timer) {
    $(timer).hide();
}

function initTimer() {
    $('.js-timer').timer({
        days: ".days",
        hours: ".hour",
        minute: ".minute",
        second: ".second",
        blockTime: ".timer__item",
        linePercent: ".progress-bar__indicator",
        textLinePercent: ".num_percent"
    });
}
/*--/timer--*/

function initSelect() {
    if (typeof(Select) === 'undefined' || !jQuery.isFunction(Select)) {
        return false;
    }

    var common = {
        onDisabledField: function($input) {
            $input.parents('.products__item').removeClass('products__item_visible');
        },
        onUndisabledField: function($input) {
            $input.closest('.products__item').addClass('products__item_visible');
        }
    };

    jQuery('.js-select').not('.js-select-ready').each(function() {
        var local = rsFlyaway.parseData(jQuery(this).data('select'));
        new Select(this, jQuery.extend({}, common, local));
    });

    $(document).on('mouseout', '.products_showcase > .products__item > .products__in', function() {
        console.log('out');
    });
}

/* basket*/
function initBasket() {

    rsFlyaway_SetInBasket();

    $(document).on("click", ".js-basket-box:not(.active)", function() {
        $(this).closest(".js-element").find(".js-add2basketlink").click();
    });

    $(document).on("click", ".js-add2basketlink", function(e) {
        e.preventDefault();

        var $this = $(this);
        $this.button('loading');
        rsFlyaway.darken($this.closest(".js-element").find(".js-basket-box"));

        Basket.add($this.parents('.add2basketform'))
            .then(function(data) {
                var jsonData = BX.parseJSON(data);

                if (jsonData.STATUS == 'OK') {

                    BX.onCustomEvent('OnBasketChange');

                    if ($(window).width() < 768 || $this.data('popup') == "N") {
                        return;
                    }

                    openPopup(
                        $this,
                        'wide',
                        $.extend({}, {
                            type: 'ajax',
                            width: 700,
                            cache: false,
                            title: BX.message('RSFLYAWAY_PRODUCT_ADDING2BASKET'),
                            ajax: {
                                dataType: 'html',
                                headers: {
                                    'X-fancyBox': true
                                },
                                data: {
                                    AJAX_CALL: 'Y',
                                    action: 'add2basket',
                                    element_id: $this.closest('.js-element').find('.js-add2basketpid').val()
                                }
                            },
                            helpers: {
                                title: {
                                    type: 'inside',
                                    position: 'top'
                                }
                            },
                            minHeight: 230,
                            href: $this.closest('.js-element').data('detailpageurl'),
                            afterShow: function() {
                                initSelect();
                            }
                        })
                    );
                }

            })
            .always(function() {
                $this.button('reset');
                rsFlyaway.darken($this.closest(".js-element").find(".js-basket-box"));
            });
    });

    $(document).on("change.rs_flyaway.inbasket", function() {
        rsFlyaway_SetInBasket();
    });
}

function rsFlyaway_SetInBasket() {

    rsFlyaway_RemoveFromBasketButtons($(".add2basketform"));
    $(".js-basket-box").removeClass('active');

    $.each(Basket.inbasket(), function(key, id) {
        rsFlyaway_SetInBasketButtons($(".js-add2basketpid[value=" + id + "]").parents('.add2basketform'));
        $(".js-add2basketpid[value=" + id + "]").closest(".js-element").find(".js-basket-box").addClass('active');
    });
}

function rsFlyaway_SetInBasketButtons($formObj) {
    $formObj.addClass("checked");
}

function rsFlyaway_RemoveFromBasketButtons($formObj) {
    $formObj.removeClass("checked");
}
/* /basket*/

/* debounce */
function debounce(callback, wait, immediate) {
    var timeout;

    return function() {
        var ctx = this,
            args = arguments,
            isCallNow = immediate && !timeout;

        !timeout || clearTimeout(timeout);
        timeout = setTimeout(run, wait);

        if (isCallNow) {
            callback.apply(ctx, args);
        }

        function run() {
            timeout = null;
            if (!immediate) {
                callback.apply(ctx, args);
            }
        }
    };
}
/*--/debounce--*/

// set favorite
function rsFlyaway_SetInFavorite() {
    $('.js-favorite').removeClass('active');
    for (var id in rsFlyaway_FAVORITE) {
        if (rsFlyaway_FAVORITE[id] == 'Y') {
            $(".js-favorite[data-elementid=" + id + "]").addClass("active");
        }
    }
}

function initFavorite() {
    rsFlyaway_SetInFavorite();

    // AJAX -> add2favorite
    $(document).on("click", ".js-favorite", function(e) {
        console.info('AJAX -> add2favorite ');

        e.preventDefault();

        var $this = $(this),
            url = $this.data('detailpageurl'),
            id = parseInt($this.data("elementid")),
            $elSelectors = $(".js-favorite[data-elementid=" + id + "]");

        if (!id) {
            return;
        }

        data = {
            "action": "UPDATE_FAVORITE",
            "element_id": id,
            "AJAX_CALL": "Y"
        };

        rsFlyaway.darken($elSelectors);

        $.getJSON(url, data, function(json) {
            if (json.TYPE == 'OK') {
                if (json.ACTION == 'ADD') {
                    rsFlyaway_FAVORITE[id] = "Y";
                } else {
                    delete rsFlyaway_FAVORITE[id];
                }
                console.log(json.HTMLBYID.favorinfo);
                $(".js-favorinfo").html(json.HTMLBYID.favorinfo);
                $(document).trigger("updateFavorite.rs.flyaway");
            } else {
                console.warn('add2favorite - error responsed');
            }
        }).fail(function() {
            console.warn('add2favorite - can\'t load json');
        }).always(function() {
            rsFlyaway.darken($elSelectors);
            rsFlyaway_SetInFavorite();
        });
    });
}
/*--/init--*/

/* Mobile Menu */
function RsFlyawayToggleMobileMenu() {
    var $menu = $(".js-mobile-menu");

    if ($menu.length === 0) {
        return;
    }

    $menu.parent().toggleClass("opened-mobile-menu");

    if($menu.parent().hasClass('opened-mobile-menu')) {
        $menu.removeAttr('style');

        if($("#bx-panel").length > 0 && $("#fly-header-sticky-wrapper.is-sticky").length == 0) {
            $menu.css('padding-top', parseInt($menu.css('padding-top'), 10)  + $("#bx-panel").outerHeight());
        }

        $menu.css({
            'top': $(window).scrollTop(),
            'height': $(window).outerHeight()
        });
        $(".is-sticky .fly-header-wrap ").css({
            'position': 'absolute',
            'top': $(window).scrollTop() - $("#panel").outerHeight()
        });

    } else {

        $menu.parent().css('overflow-x', 'hidden');
        setTimeout(function() {
            $menu.parent().removeAttr('style');
            //$(".is-sticky .fly-header-wrap ").css('top', 0);
            $(".is-sticky .fly-header-wrap ").css({
                'top': 0,
                'position': 'fixed'
            })
        }, 450)
    }

    if(!$menu.parent().hasClass("opened-mobile-menu")) {

    } else {

    }

    /**if(!$menu.parent().hasClass("opened-mobile-menu")) {
        $menu.parent().css('overflow', 'hidden');

        setTimeout(function() {
            $menu.parent().removeAttr('style');
        }, 400)
    }**/
}
/* /Mobile Menu */

$(document).ready(function() {
    mobileForm();
    initMainMenu();
    initSidebarMenu();
    initPopup();
    initCompare();
    initFavorite();
    initToggle();
    initFix();
    initViews();
    initSlider();
    initReviews();
    initAjaxpages();
    initTop();
    initOwl();
    initTimer();
    initSelect();
    initBasket();

    $('.owl').each(function() {
        var $owl = $(this),

            RSFLYAWAY_change_speed = 2000,
            RSFLYAWAY_change_delay = 8000,
            RSFLYAWAY_margin = 0,
            RSFLYAWAY_responsive = {
                0: {
                    items: 1
                },
                768: {
                    items: 1
                },
                1200: {
                    items: 1
                }
            };
        if (parseInt($owl.data('changespeed')) > 0) {
            RSFLYAWAY_change_speed = $owl.data('changespeed');
        }
        if (parseInt($owl.data('changedelay')) > 0) {
            RSFLYAWAY_change_delay = $owl.data('changedelay');
        }
        if (parseInt($owl.data('margin')) > 0) {
            RSFLYAWAY_margin = $owl.data('margin');
        }
        if ($owl.data('responsive') != '' && (typeof($owl.data('responsive')) == 'object')) {
            RSFLYAWAY_responsive = $owl.data('responsive');
        }
        if ($owl.find('.item').length > 1) {
            $owl.owlCarousel({
                items: 4,
                margin: RSFLYAWAY_margin,
                loop: true,
                autoplay: false,
                nav: true,
                navText: ['<span></span>', '<span></span>'],
                navClass: ['prev', 'next'],
                autoplaySpeed: RSFLYAWAY_change_speed,
                autoplayTimeout: RSFLYAWAY_change_delay,
                smartSpeed: RSFLYAWAY_change_speed,
                onInitialize: function(e) {
                    $owl.addClass('owl-carousel owl-theme');
                    if (this.$element.children().length <= this.settings.items) {
                        this.settings.loop = false;
                    }
                },
                onResize: function(e) {
                    if (this._items.length <= this.settings.items) {
                        this.settings.loop = false;
                    }
                },
                onRefreshed: function() {
                    $owl.removeClass('noscroll');
                    if ($owl.find('.cloned').length < 1) {
                        $owl.addClass('noscroll');
                    }
                },
                responsive: RSFLYAWAY_responsive
            });
        }
    });

    /* Main Menu */
    rsFlyawayAdaptiveMainMenu();
    $(window).resize(rsFlyawayAdaptiveMainMenu);

    $(document).on("click", ".navbar-toggle, .js-toggle-mainmenu", function() {
        RsFlyawayToggleMobileMenu();
    });
    $(document).on("click", ".mobile-menu-nav__link", function(event) {
        var $self = $(this);

        if ($self.hasClass("back")) {

            $self.closest(".js-mobile-menu-nav").toggleClass("open")
                .closest("li").removeClass("openelement")
                .siblings("li").show();
            return false;

        } else if ($self.siblings(".js-mobile-menu-nav").length === 0) {
            return true;
        }

        event.preventDefault();

        $self.closest(".mobile-menu-nav__submenu").scrollTop(0);
        $self.siblings(".mobile-menu-nav__submenu").toggleClass("open").closest("li").addClass("openelement");
        setTimeout(function() {
            $self.closest(".js-mobile-menu-nav").children(".mobile-menu-nav__element:not(.openelement)").hide(0);
        }, 250);
    });

    $(document).on("click", ".js-userlogin-toggle", function(event) {
        event.preventDefault();
        $(this).toggleClass("open")
            .siblings(".js-mobile-userpersonal").toggleClass("open")
            .find(".mobile-menu__userpersonal-list").css(
                'height', '100%'
            );
    });

    /* Search */
    $('.header-search .fa').on('click', function() {
        if ($('.header-search .fa.fa-search').length > 0) {
            $(this).parent().find('.fa').removeClass('fa-search');
            $(this).parent().find('.fa').addClass('fa-times');
        } else {
            $(this).parent().find('.fa').removeClass('fa-times');
            $(this).parent().find('.fa').addClass('fa-search');
        }
        $('.header-search').find('.header-search__form').toggle();
    });
    $(".js-fly-header__search-icon").on('click', function() {
        var $searchBlock = $(this).closest(".fly-header__search");

        $searchBlock.addClass("open-search");

        $searchBlock.find(".js-fly-header__search-close").one("click", function() {
            $searchBlock.removeClass("open-search");
        });
    });

    /*Sticky menu*/
    var $jsStickyHeader = $(".js-sticky-header");

    if($jsStickyHeader.length > 0) {
        var headerHeight = $jsStickyHeader.outerHeight();

        $jsStickyHeader.sticky({
            topSpacing:0,
            zIndex: 999,
            wrapperClassName: 'fly-header-sticky-wrapper'
        });

        $(window).resize(debounce(function() {
            $jsStickyHeader.parent().css("height",  $jsStickyHeader.outerHeight());
        }, 0));


        $jsStickyHeader.on('sticky-start', function() {

            var $header = $(this),
            isSimpleHeader = false;

            if(!$('body').hasClass('is--sidenav')) {

                $header.addClass('__simple');
                isSimpleHeader = true;

            } else {

                $(window).on('scroll.sticky-header', function() {

                    if($(window).scrollTop() > headerHeight / 2) {
                        $header.addClass('__simple');
                        isSimpleHeader = true;
                    } else if(isSimpleHeader) {
                        $header.removeClass('__simple');
                        isSimpleHeader = false;
                    }

                });
            }

            $jsStickyHeader.one('sticky-end', function() {
                $(window).off('scroll.sticky-header');
                $header.removeClass('__simple');
                isSimpleHeader = false;

                if(headerHeight != $header.parent().outerHeight()) {
                    $header.parent().css('height', headerHeight);
                }
            });
        });

    }


    /*Quantity only digits*/
    $(document).on("keydown", ".js-quantity", function(e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
});
