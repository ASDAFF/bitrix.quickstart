
$(document).ready(function() {
    'use strict';

    $(".js-owl").owlCarousel({
        items: 1,
        nav: false,
        dots: true,
        dotsData: true,
        dotsContainer: '.js-owl__dots',
        onRefreshed: function() {
            setTransform(0);
        }
    });

    var cx = 0,
        sx,
        isCanMove = false;

    $(".js-owl__dots").on("touchstart", function(e) {
        touchStartDots(e.originalEvent.touches[0].pageX);
    });
    var isMove = false;
    $(".js-owl__dots").on("mousedown", function(e) {
        e.preventDefault();
        touchStartDots(e.originalEvent.pageX);

        $(".js-owl__dots").on("mousemove.owl.dots", function(e) {
            touchMoveDots(e.originalEvent.pageX);
        });
        $(window).one("mouseup", function(e) {
            touchEndDots(e.originalEvent.pageX);
            $(".js-owl__dots").off("mousemove.owl.dots");
            isMove = false;
        });

    });

    $(".js-owl__dots").on("touchmove", function(e) {
         touchMoveDots(e.originalEvent.touches[0].pageX);
    });

    $(".js-owl__dots").on("touchend", function(e) {
         touchEndDots(e.originalEvent.changedTouches[0].pageX);
    });

    function calculateDotsWidth() {
        var $dots = $(".js-owl__dots"),
            width = 0;

        $dots.find(".owl-dot").each(function(index, item) {
            width += $(item).outerWidth();
        });
        return width;
    }

    function touchStartDots(pageX) {
        sx = pageX - cx;
    }
    function touchMoveDots(pageX) {
        setTransform(pageX - sx);
    }
    function touchEndDots(pageX) {
        var diff = pageX - sx,
            negDotsWidth =  -calculateDotsWidth() +  $(".js-owl__dots").parent().outerWidth();

        if(diff > 0 || calculateDotsWidth() < $(".js-owl__dots").width()) {
            cx = 0;
            setTransform(0);
        } else if(diff <= negDotsWidth) {
            setTransform(negDotsWidth);
            cx = negDotsWidth;
        } else {
            cx =  pageX-sx;
        }
    }

    function setTransform(translateX) {
        $(".js-owl__dots").css({
            '-webkit-transform': 'translateX(' + translateX + 'px)',
            '-webkit-transform': 'translateX(' + translateX + 'px)',
            '-moz-transform': 'translateX(' + translateX + 'px)',
            '-ms-transform': 'translateX(' + translateX + 'px)',
            '-o-transform': 'translateX(' + translateX + 'px)',
            'transform': 'translateX(' + translateX + 'px)'
        });
    }
});
