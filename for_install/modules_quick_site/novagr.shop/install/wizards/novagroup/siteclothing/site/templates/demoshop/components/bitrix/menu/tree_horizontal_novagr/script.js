/**
 * Created with JetBrains PhpStorm.
 * User: anton
 * Date: 19.08.13
 * Time: 12:53
 * To change this template use File | Settings | File Templates.
 */

function extractTopPosition() {
    var position = $('div.header').offset();
    var data = {}; data.offset = {};
    data.offset.top = position.top+140;
    $('#affix_menu').affix(data);
}

$(document).ready(function () {
    extractTopPosition();
});

$(window).scroll(function () {
    extractTopPosition();
});