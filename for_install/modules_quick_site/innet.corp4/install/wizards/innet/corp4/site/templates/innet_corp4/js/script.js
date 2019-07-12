$(document).ready(function () {
    if ($(".order-block").length > 0) {
        $(function () {
            var sidebar = $('.order-block');
            var top = sidebar.offset().top - parseFloat(sidebar.css('margin-top'));

            $(window).scroll(function (event) {
                var y = $(this).scrollTop();
                if (y >= top - 100) {
                    sidebar.addClass('fixed_side_bar').css('width', $('.cols2>.col1').width());
                } else {
                    sidebar.removeClass('fixed_side_bar');
                }
            });
        });
    }

    $("a[data-gal^='prettyPhoto']").prettyPhoto({
        social_tools: false,
        deeplinking: false
    });

    $('.colors:not(.multi) a').click(function () {
        var that = $(this);
        var par = $(this).parent('.colors');
        $(par).find('a').removeClass('active');
        $(this).addClass('active');
    });

    $('.item-size a').click(function () {
        var that = $(this);
        var par = $(this).parent('.item-size');
        $(par).find('a').removeClass('active');
        $(this).addClass('active');
    });

    $('.question-js').on('click', function () {
        $(this).toggleClass('active');
        $(this).parent().children('p').slideToggle();
    });

    $('.owl-carousel').owlCarousel({
        // loop: true,
        margin: 10,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                nav: true
            },
            500: {
                items: 2,
                nav: true
            },
            820: {
                items: 3,
                nav: true
            }
        }
    });

    $('.owl-carousel2').owlCarousel({
        margin: -1,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
                nav: true
            },
            400: {
                items: 2,
                nav: false
            },
            600: {
                items: 3,
                nav: false
            },
            1000: {
                items: 5,
                nav: true,
                loop: false
            }
        }
    });

    $(".btn-search").click(function () {
        $(".header .lvl3").addClass('active')
    });

    $(".header .lvl3 .close, .slider1, .advantages, .blocks1, .header .lvl1, .header .lvl2 .col1, .content").click(function () {
        $(".header .lvl3").removeClass('active')
    });

    $("body").prepend("<div class='mask'></div>");

    (function ($) {
        $(function () {
            var popwindow = $('.popwindow');
            var popbutton = $('.popbutton');

            function preparewindow(windowobject) {
                var winwidth = windowobject.data("width");
                var winheight = windowobject.data("height");
                var winmargin = winwidth / 2;
                var wintitle = windowobject.data("title");

                windowobject.wrap("<div class='box_window'></div>");
                windowobject.addClass("box_window_in");
                windowobject.parent(".box_window").prepend("<div class='bw_close'>�������</div>");
                windowobject.css("cursor", "pointer");

                windowobject.parent(".box_window").prepend("<div class='box_title'>" + wintitle + "</div>");
                windowobject.parent(".box_window").css({
                    'width': winwidth,
                    'height': winheight,
                    'margin-left': '-' + winmargin
                });
                windowobject.css({'height': winheight})
            }

            if (popwindow.length) {
                preparewindow(popwindow);
                popbutton.click(function () {
                    var idwind = $(this).data("window");
                    $("#" + idwind).parent(".box_window").fadeIn().addClass("windactiv");
                    $(".mask").fadeIn();
                    $("body").css("overflow", "hidden");
                    $(".windactiv").css("overflow-y", "scroll");
                    $(".to_blur").addClass("blur");
                });
            }
            $(".mask, .bw_close").click(function () {
                $(".windactiv").fadeOut();
                $(".windactiv").removeClass("windactiv");
                $(".mask").fadeOut();
                $("body").css("overflow", "visible");
                $(".to_blur").removeClass("blur");
            });
        });
    })(jQuery);

    var $menu = $(".top");
    var fixed = $(".mob-nav-btn").data("fixed");
    if (fixed == "fixed" && !INNET_ADMIN) {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 200 && $menu.hasClass("default")) {
                $menu.fadeOut(0, function () {
                    $(this).removeClass("default")
                        .addClass(fixed)
                        .fadeIn(0);
                });
                //$('.toogle-block' ).css("display", "none");
                if ($(window).width() > 960) {
                    $('.header .toogle-block').css("display", "none");
                    $('.header .toogle-block').fadeOut(0);
                }
            } else if ($(this).scrollTop() <= 200 && $menu.hasClass(fixed)) {
                $menu.fadeOut(0, function () {
                    $(this).removeClass(fixed)
                        .addClass("default")
                        .fadeIn(0);
                });
                if ($(window).width() > 960) {
                    //$menu.next('.toogle-block').css("display", "block");
                    $('.header .toogle-block').fadeIn(0);
                }
                //$('.toogle-block' ).css("display", "block");
            }
        });
    }

    if (fixed == "fixed2" && !INNET_ADMIN) {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 200 && $menu.hasClass("default")) {
                $menu.fadeOut(0, function () {
                    $(this).removeClass("default")
                        .addClass(fixed)
                        .fadeIn(0);
                });
                //$('.toogle-block' ).css("display", "none");
                if ($(window).width() > 960) {
                    //$('.header .toogle-block').css("display", "none");
                    //  $('.header .toogle-block').fadeOut(0);
                }
            } else if ($(this).scrollTop() <= 200 && $menu.hasClass(fixed)) {
                $menu.fadeOut(0, function () {
                    $(this).removeClass(fixed)
                        .addClass("default")
                        .fadeIn(0);
                });
                if ($(window).width() > 960) {
                    //$menu.next('.toogle-block').css("display", "block");
                    //  $('.header .toogle-block').fadeIn(0);
                }
                //$('.toogle-block' ).css("display", "block");
            }
        });
    }

    $('.header .toogle-block-title').click(function (event) {
        //$('.toogle-block').not($(this).closest('.inner').find('.toogle-block')).hide("slow");
        $(this).next('.toogle-block').fadeToggle('fast');
        var firstClick = true;
        $(document).bind('click.Event', function (e) {
            if (!firstClick && $(e.target).closest('.toogle-block-title').length == 0) {
                $('.toogle-block').fadeOut('fast');
                $(document).unbind('click.Event');
            }
            else firstClick = false;
        });
    });

    $('.content .toogle-block-title').click(function () {
        $(this).parent().find(".toogle-block").slideToggle("slow", function () {
            if ($(this).parent().hasClass("opened")) $(this).parent().removeClass("opened"); else $(this).parent().addClass("opened");
        });
    });

    $('.toogle-block-title2').click(function () {
        if ($(window).width() > 960) {
            $(this).parent().find(".toogle-block2").slideToggle("slow", function () {
                if ($(this).parent().hasClass("opened")) $(this).parent().removeClass("opened"); else $(this).parent().addClass("opened");
            });
        } else {
            $(this).parent().find(".toogle-block2").slideToggle("slow", function () {
                if ($(this).parent().hasClass("opened")) $(this).parent().removeClass("opened"); else $(this).parent().addClass("opened");
            });
            return false;
        }
    });

    $('.toogle-title span[data-action="open-panel"]').click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        $(this).parent().parent().find(".toogle-main").slideToggle("slow", function () {
            $(this).parent().toggleClass("opened");
        });
    });

    $('.toogle-title2 span[data-action="open-panel"]').click(function (e) {
        e.preventDefault();
        e.stopPropagation();

        $(this).parent().parent().find(".toogle-main2").slideToggle("slow", function () {
            $(this).parent().toggleClass("opened2");
        });
    });

    $('.toogle-title3').click(function (e) {
        $(this).parent().find(".toogle-main3").slideToggle("slow", function () {
            $(this).parent().toggleClass("opened3");
        });
    });

    /*$('.toogle-title').click(function () {

     });*/

    $(".toogle-title-2").click(function () {
        $(".toogle-main-2").toggleClass("opened");
        $(".toogle-title-2").toggleClass("opened");
        return false;
    });

    $(".open-toogle").click(function () {
        $(".border").toggleClass("opened");
        return false;
    });

    $(".open-toogle2").click(function () {
        $(".border2").toggleClass("opened");
        return false;
    });

    /*Slide block*/
    (function () {
        function SlideToggler(options) {
            this._listenedBlock = options.listenedBlock || 'body';
            this._activeClass = options.activeClass || 'active';
            this._activeBlocks = options.activeBlocks || ['self']; // self = clicked button;  parent = mutual parent block;
            this._mutualParentLevel = options.mutualParentLevel || 0;
            this._preventDefault = options.preventDefault || true;
            this._stopPropagation = options.stopPropagation || false;
            this._dataAction = options.dataAction || 'slide-toggler';
        }

        SlideToggler.prototype.init = function () {
            $(this._listenedBlock).click(this.toggler.bind(this));
        };

        SlideToggler.prototype.toggler = function (e) {
            var self = this;
            var elem = e.target;
            var toggleBtn = elem.closest('[data-action="' + this._dataAction + '"]');

            if (!toggleBtn) return;
            if (this._preventDefault) e.preventDefault();
            if (this._stopPropagation) e.stopPropagation();

            var parent = toggleBtn;

            for (var i = 0; i < this._mutualParentLevel; i++) {
                parent = parent.parentNode;
            }

            $(toggleBtn.getAttribute('data-target'), $(parent)).slideToggle(function () {
                self._activeBlocks.forEach(function (elem) {
                    if (elem === 'parent') {
                        $(parent).toggleClass(self._activeClass);
                        return;
                    }
                    if (elem === 'self') {
                        $(toggleBtn).toggleClass(this._activeClass);
                    }

                    $(elem, $(parent)).toggleClass(self._activeClass);

                });
            });
        };

        var slidingFilters = new SlideToggler({
            listenedBlock: '.toogle.toogle2',
            activeClass: 'opened',
            activeBlocks: ['parent'],
            mutualParentLevel: 1
        });
        slidingFilters.init();
    })();

    var slider1 = $("#range_01").ionRangeSlider({
        type: "double",
        min: 0,
        max: 75000,
        from: 0,
        to: 75000,
        step: 1000,
        max_interval: 75000
    });

    $(".custom-scroll").customScrollbar({
        skin: "default-skin",
        hScroll: false,
        updateOnWindowResize: true
    });

    $('.select').fancySelect();

    $('.view-style a').click(function () {
        $('.view-style a.active').removeClass('active');
        $(this).addClass('active');
        var itemClass = $(this).attr('rel');
        $('.items').attr('class', 'items ' + itemClass);
    });

    $('.view-style a').click(function () {
        $('.view-style a.active').removeClass('active');
        $(this).addClass('active');
        var itemClass = $(this).attr('rel');
        $('.blocks8').attr('class', 'blocks8 ' + itemClass);
    });

    var slider = new MasterSlider();

    slider.control('arrows');
    slider.control('lightbox');
    slider.control('thumblist', {
        autohide: false,
        dir: 'h',
        align: 'bottom',
        width: 130,
        height: 85,
        margin: 5,
        space: 5,
        hideUnder: 400
    });

    slider.setup('masterslider', {
        width: 750,
        height: 440,
        space: 5,
        loop: true,
        view: 'fade'
    });

    var slider2 = new MasterSlider();

    slider2.control('arrows');
    slider2.control('lightbox');
    slider2.control('thumblist', {
        autohide: false,
        dir: 'h',
        align: 'bottom',
        width: 130,
        height: 85,
        margin: 5,
        space: 5,
        hideUnder: 400
    });

    slider2.setup('masterslider2', {
        width: 750,
        height: 300,
        space: 5,
        loop: true,
        view: 'fade'
    });

    //services key
    var mh_services_key = 0;
    $(".blocks1 .in-row>a").each(function () {
        var h_block = parseInt($(this).height());
        if (h_block > mh_services_key) {
            mh_services_key = h_block;
        }
    });
    $(".blocks1 .in-row>a").height(mh_services_key);

    //services main
    var mh_services_main = 0;
    $(".blocks2>a").each(function () {
        var h_block = parseInt($(this).height());
        if (h_block > mh_services_main) {
            mh_services_main = h_block;
        }
    });
    $(".blocks2>a").height(mh_services_main);

    //index products
    var mh_products = 0;
    $(".blocks4>div").each(function () {
        var h_block = parseInt($(this).height());
        if (h_block > mh_products) {
            mh_products = h_block;
        }
    });
    $(".blocks4>div").height(mh_products);

    //msslide click
    $('.gallery-owl .ms-slide img').click(function () {
        $(this).closest(".ms-slide").find("a").trigger("click");
    });

    $(".topTabs a, #tabs .tabs li a").click(function (event) {
        event.preventDefault();

        var correct = 100;
        if (!$('.header.top').hasClass('fixed')) {
            correct = correct + $('.header.top').outerHeight();
        }

        $(this).parent().addClass("current");
        $('li,a').removeClass("current");

        var tab = $(this).attr("href");
        $("#tabs .tab").not(tab).css("display", "none");
        $(tab).fadeIn();
        var scroll = $('#tabs').offset().top;
        $("html, body").animate({
                scrollTop: scroll - correct
            },
            500
        );

        var nowID = $("#tabs .tab:visible").attr('id');
        $('#tabs a[href*="#' + nowID + '"]').parent('li').addClass('current');
        $('.topTabs a[href*="#' + nowID + '"]').addClass('current');
    });

    $(".header .arrow .toogle-block-title2 span").on("touchstart", function (e) {
        $(this).closest("ul").find(".toogle-block2").not($(this).parent().next(".toogle-block2")).css("display", "none");
        $(this).parent().next(".toogle-block2").slideToggle("slow", function () {
            if ($(this).parent().parent().hasClass("opened")) $(this).parent().parent().removeClass("opened"); else $(this).parent().parent().addClass("opened");
        });
        $(this).closest(".toogle-block").css('height', 'auto');
        e.preventDefault();
    });

    $('.header .lvl0 .pull-left.showmenu span').click(function (e) {
        e.preventDefault();
        $(this).toggleClass("active_menu");
        $(this).closest(".showmenu").find('.menu').not($(this).closest(".showmenu").find('.menu')).hide("slow");
        $(this).closest(".showmenu").find('.menu').fadeToggle('fast');
        var firstClick = true;
        $(document).bind('click.Event', function (e) {
            if (!firstClick && $(e.target).closest('.header .lvl0 .pull-left.showmenu').length == 0) {
                $('.header .lvl0 .pull-left.showmenu .menu').fadeOut('fast');
                $(document).unbind('click.Event');
            }
            else firstClick = false;
        });
    });

    /*$(".toogle-title").click(function () {
     var elem = $(this).siblings(".toogle-main");

     if ($(elem).css('display') == 'none') {
     $(this).siblings(".toogle-main").fadeIn(500);
     $(this).parent().toggleClass("opened");
     } else {
     $(this).siblings(".toogle-main").fadeOut(500);
     $(this).parent().toggleClass("opened");
     }

     return false;
     });*/
});

$(document).mouseup(function (e) {
    var container = $(".header.fixed .lvl2 .col1");
    var container2 = $(".popup-window");

    if (container.has(e.target).length === 0) {
        //container.hide();
    }

    if (container2.has(e.target).length === 0) {
        container2.fadeOut();
        $(".popup-window-overlay").fadeOut();
    }
});