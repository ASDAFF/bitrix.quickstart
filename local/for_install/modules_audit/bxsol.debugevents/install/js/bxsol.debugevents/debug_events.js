/**
 * Mineev Aleksey (2016 ©)
 * alekseym@bxsolutions.ru
 */

$().ready(function () {

    function highlightWords(line, word) {
        var regex = new RegExp('(' + word + ')', 'gi');
        return line.replace(regex, '<span class="bxsol_events__ev--highlighted">$1</span>');
    }

    function fixSearchInput() {
        var $cache = $('#bxsol_events__search');
        if ($(window).scrollTop() > 100)
            $cache.addClass('bxsol_events__search--fixed');
        else
            $cache.removeClass('bxsol_events__search--fixed');
    }

    function search() {

        q_raw = $("#bxsol_events__search").val().toLowerCase();
        q = q_raw.toLowerCase();

        $('.bxsol_events__ev--highlighted').removeClass('bxsol_events__ev--highlighted');

        if (q != '' && q.length > 1) {

            $('.bxsol_events__event').addClass('bxsol_events__event--hidden');
            $('.bxsol_events').addClass('bxsol_events--hidden');

            for (i in events) {

                if (events[i].search(q) != -1) {

                    $('.ev__' + events[i]).each(function () {

                        var label = $(this).find('.bxsol_events__event_name').eq(0);

                        txt = highlightWords(label.text(), q_raw);

                        label.html(txt);

                        $(this).removeClass('bxsol_events__event--hidden');
                        $(this).closest('.bxsol_events').removeClass('bxsol_events--hidden');
                    });

                }

            }
        } else {
            $('.bxsol_events__event').removeClass('bxsol_events__event--hidden');
            $('.bxsol_events').removeClass('bxsol_events--hidden');
        }
    }

    var events = window.bxsol_debug_events;

    $("#bxsol_events__search").keyup(function () {
        search();
    });

    $("#bxsol_events__search").change(function () {
        search();
    });

    $(window).scroll(fixSearchInput);
    fixSearchInput();

});
