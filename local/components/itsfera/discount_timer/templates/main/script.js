
    function defer_action_timer() {
        if (window.jQuery && (document.getElementById("clockdiv") !== null)) {

               $(document).ready(function() {
                $('.dsc_slider').slick({
                    infinite: true,
                    arrows: true,
                    dots: false,
                    autoplay: false,
                    speed: 500,
                    nextArrow: '<a href="javascript:void(0);" class="slick-prev slick-arrow"></a>',
                    prevArrow: '<a href="javascript:void(0);" class="slick-next slick-arrow"></a>',
                    slidesToShow: 6,
                    slidesToScroll: 6,

                });

                $('.dsc_item').hover(function() {
                    $(this).parent().find('.item-title').css('color', '#e6320a');
                }, function() {
                    $(this).parent().find('.item-title').css('color', '#000000');
                })


                $(".item-actions-buy").click(function(){
                    var $this = $(this),
                        href = $this.attr('href');
                    mht.animateToBasket($this.closest(".dsc_product").find(".dsc_image img"));
                    $.post(href, function(){
                       mht.updateBasket();
                    });
                    return false;
                });

            });


            function getTimeRemaining(endtime) {
                var t = Date.parse(endtime) - Date.parse(new Date());
                var seconds = Math.floor((t / 1000) % 60);
                var minutes = Math.floor((t / 1000 / 60) % 60);
                var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
                var days = Math.floor(t / (1000 * 60 * 60 * 24));
                return {
                    'total': t,
                    'days': days,
                    'hours': hours,
                    'minutes': minutes,
                    'seconds': seconds
                };
            }

            function initializeClock(id, endtime) {
                var clock = document.getElementById(id);

                var daysSpan = clock.querySelector('.days');
                var hoursSpan = clock.querySelector('.hours');
                var minutesSpan = clock.querySelector('.minutes');
                var secondsSpan = clock.querySelector('.seconds');

                var sm_days = clock.querySelector('.sm_days');
                var sm_hours = clock.querySelector('.sm_hours');
                var sm_minutes = clock.querySelector('.sm_minutes');
                var sm_seconds = clock.querySelector('.sm_seconds');

                function updateClock() {
                    var t = getTimeRemaining(endtime);

                    daysSpan.innerHTML = ('0' + t.days).slice(-2);
                    hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
                    minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
                    secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);


                    if (t.total <= 0) {
                        clearInterval(timeinterval);
                    }
                }

                updateClock();
                var timeinterval = setInterval(updateClock, 1000);
            }

            var deadline = new Date(Date.parse($('div.chrono').data('end')));

            initializeClock("clockdiv", deadline);


        } else {
            setTimeout(function () {
                defer_action_timer()
            }, 200);
        }

    }

    defer_action_timer();


