/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 12.10.2017
 * Time: 16:50
 */
(function () {

    var ajaxPagerLoadingClass = 'ajax-pager-loading',
        ajaxPagerWrapClass = 'ajax-pager-wrap',
        ajaxPagerLinkClass = 'ajax-pager-link',
        ajaxWrapAttribute = 'wrapper-class',
        ajaxPagerLoadingTpl = ['<span class="' + ajaxPagerLoadingClass + '">',
            'Загрузка…',
            '</span>'].join(''),
        busy = false,

        attachPagination = function () {
            var $window = $(window);
            $window.on('click', function () {
                busy = true;
            });

        },

        ajaxPagination = function (e) {
            e.preventDefault();
            busy = true;
            var wrapperClass = $('.' + ajaxPagerLinkClass).data(ajaxWrapAttribute),
                $wrapper = $('.' + wrapperClass),
                $link = $(this);

            if ($wrapper.length) {
                $('.' + ajaxPagerWrapClass).append(ajaxPagerLoadingTpl);
                $.get($link.attr('href'), {'AJAX_PAGE': 'Y'}, function (data) {
                    $('.' + ajaxPagerWrapClass).remove();
                    $wrapper.append(data);
                    attachPagination(wrapperClass);
                    busy = false;
                });
            }
        };

    $(function () {
        attachPagination($('.' + ajaxPagerLinkClass).data(ajaxWrapAttribute));
        $(document).on('click', '.' + ajaxPagerLinkClass, ajaxPagination);
    });

})();