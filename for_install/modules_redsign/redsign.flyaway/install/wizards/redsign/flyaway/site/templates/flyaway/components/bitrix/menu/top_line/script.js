$(document).ready(function () {
    'use strict';

    var $topLineMenu = $("ul.top_line_menu"),
            $moreLink = $topLineMenu.children(".top_line_menu__more"),
            $links = $topLineMenu.children("li"),
            $moreList = $(".top_line_menu__more-list");

    setTimeout(function () {
        resizeTopLineMenu();
        $topLineMenu.css('visibility', 'visible');
    }, 100);

    $(window).resize(function () {
        if($(document).width() > 960) {
            resizeTopLineMenu();
        }
    });

    function resizeTopLineMenu() {
        var topLineMenuWidth = $topLineMenu.width(),
            width = $moreLink.width(),
            invisibleElements = [];

        $links.each(function (key, el) {

            var $el = $(el);

            width += $el.width() + 16; // 16 = margin-right

            if(width < topLineMenuWidth || el == $moreLink[0]) {
                    $el.show();
            }
            else {
                invisibleElements.push($el.clone().show().css('display', 'block'));
                $el.hide();
            }

            $moreList.html(invisibleElements);
        });

        if($moreList.find("li").length === 0) {
            $moreLink.hide();
        } else {
            $moreLink.show();
        }
    }

});
