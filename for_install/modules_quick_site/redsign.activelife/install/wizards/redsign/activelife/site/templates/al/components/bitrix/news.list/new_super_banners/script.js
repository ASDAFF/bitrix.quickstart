$(function() {

    var timeoutChange = parseInt($(".js-mainbanners").data('timeout'), 10);

    var progressline = {

        timeout: timeoutChange - 1100,
        backSpeed: 1000,
        startTime: undefined,
        stopTime: undefined,
        stopTimePassed: 0,


        $el: $(".js-mainbanners-progressline"),

        start: function(onCompleteCallback) {
            onCompleteCallback = $.isFunction(onCompleteCallback) ? onCompleteCallback : function() {};

/**
            var leftTime = this.timeout;
            var currentTime = new Date().getTime();
            if(this.stopTime) {
                this.stopTimePassed = this.stopTimePassed + currentTime - this.stopTime;
                leftTime = leftTime - (currentTime - this.startTime) + this.stopTimePassed;
            } else {
                this.startTime = new Date().getTime();
            }
**/
            this.$el.find(".js-progress").stop().animate({'width': '100%'}, this.timeout, 'linear', onCompleteCallback);
        },
        reset: function(onCompleteCallback) {
            onCompleteCallback = $.isFunction(onCompleteCallback) ? onCompleteCallback : function() {};

            this.stopTime = undefined;
            this.startTime = undefined;
            this.stopTimePassed = 0;

            this.$el.find(".js-progress").stop().animate({'width': '0'}, this.backSpeed, 'linear', onCompleteCallback);
        },
        stop: function() {
            this.$el.find(".js-progress").stop();
            this.stopTime = new Date().getTime();
        },
        restart: function() {
            this.reset($.proxy(this.start, this));
        }
    };

    $(".js-mainbanners").owlCarousel({
        items: 1,
        loop: true,
        mouseDrag: false,
        touchDrag: false,
        animateIn: BX.browser.IsIE9() ?  false : 'fadeIn',
        animateOut: BX.browser.IsIE9() ?  false : 'fadeOut',
        autoplay: true,
        autoplayTimeout: timeoutChange,
        autoplaySpeed: 2000,
        smartSpeed: 2000,

        onInitialize: function() {

            setTimeout($.proxy(function() {
                this.$element.addClass("is-initialized");
                progressline.start();
            }, this), 0);


            $(document).on('mouseenter', ".js-additionals a", function() {
                progressline.stop();
                $(".js-mainbanners").trigger("stop.owl.autoplay");

                $(".js-additionals a").one('mouseleave', function() {
                    progressline.start();
                    $(".js-mainbanners").trigger("play.owl.autoplay");
                });
            });
        },

        onTranslate: function() {
            progressline.restart();
        }
    });

    $(window).blur(function() {
        progressline.stop();
        $(".js-mainbanners").trigger("stop.owl.autoplay");
    });
    $(window).focus(function() {
        progressline.start();
        $(".js-mainbanners").trigger("play.owl.autoplay");
    });

});
