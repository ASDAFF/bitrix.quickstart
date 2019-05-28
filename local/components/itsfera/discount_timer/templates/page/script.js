function defer_action_timer() {
    if (window.jQuery && $.fn.TimeCircles) {


        if (window.location.hash == '#stock-2') {
            $("li").removeClass('stock-active');
            $("li.stock-2").addClass('stock-active');

            $('.stock-stage>div').hide();
            $('div#stock-2').show();
        }

        $("#CountDownTimer").TimeCircles({time: {Days: {show: true}, Hours: {show: true}}});

        $(document).ready(function () {

             var isLoadNewPage = false;

            $('.dsc_item').hover(function () {
                $(this).parent().find('.item-title').css('color', '#e6320a');
            }, function () {
                $(this).parent().find('.item-title').css('color', '#000000');
            })


            $(".item-actions-buy").click(function () {
                var $this = $(this),
                    href = $this.attr('href');
                mht.animateToBasket($this.closest(".dsc_product").find(".dsc_image img"));
                $.post(href, function () {
                    mht.updateBasket();
                });
                return false;
            });

            $(window).scroll(function () {

                var $loader = $("a.page-loader");

                if ($loader.offset()) {
                    var PosScroll = $(window).height() + $(window).scrollTop();
                    var PosAnchor = $loader.offset().top - 400;

                    if ((PosScroll > PosAnchor) && (isLoadNewPage == false)) {
                        isLoadNewPage = true;
                        $loader.trigger('click');
                    }

                    if ((PosScroll < PosAnchor) && (isLoadNewPage == true)) {
                        isLoadNewPage = false;
                    }
                }

            });

        });

    } else {
        setTimeout(function () {
            defer_action_timer()
        }, 200);
    }

}

defer_action_timer();


