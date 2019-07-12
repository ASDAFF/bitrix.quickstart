$(function() {


    $(".js-filter-button").on('click', function() {
        var $button = $(this),
            filter = $(this).data('filter');

        if($button.hasClass('active')) return;

        $(".js-filter-button.active").removeClass('active');
        $button.addClass('active');

        $(".faq-page__answers .panel-group .item .panel-title a").addClass('collapsed');
        $(".faq-page__answers .panel-group .panel-collapse").removeClass('in');

        if(!filter) {
            $(".faq-page__answers .panel-group .item").show();
        } else {
            $(".faq-page__answers .panel-group .item").hide();
            $(".faq-page__answers .panel-group .item").filter(".filter" + filter).show();
        }

    });

});
