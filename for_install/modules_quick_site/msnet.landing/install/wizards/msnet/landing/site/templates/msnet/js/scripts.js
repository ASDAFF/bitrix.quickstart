$(document).ready(function () {
    $(document).on("click", ".js-scroll-to-element", function (e) {
        var $this = $(this),
            id = $this.attr("href"),
            $el = $(id),
            hight = $('.header').height(),
            top = $el.offset().top - hight;
        $("html, body").animate({scrollTop: top}, 500, function () {
            location.hash = $el.attr("id");
        });
        e.preventDefault();
    });
});