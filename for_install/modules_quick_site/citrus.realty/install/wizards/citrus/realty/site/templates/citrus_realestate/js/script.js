(function($) {
    $(function() {

        $('.toggle-link')
            .click(function () {
                $(this).find('a').toggleClass("on");
                var e = $(this).attr("id");
                $(e).toggle('fast');
            })
            .each(function () {
                var e = $(this).attr("id");
                $(e).hide();
            });

        $('.fancybox').fancybox();
        $('.detail-layouts a').each(function () {
            var $this = $(this),
                href = $this.attr('href'),
                filename = $this.data('filename');
            $this.fancybox({
                title: $this.attr('title') + '<p><a href="' + href + '" download="' + ('undefined' !== typeof(filename) ? filename : '') + '">' + BX.message('CITRUS_REALTY_DOWNLOAD_ORIGINAL') + '</a></p>'
            });
        });
        $('a.ajax-popup').each(function () {
            var $this = $(this),
                href = $this.attr('href');

            $this.fancybox({
                'padding': 20,
                'margin' : 0,
                'frameWidth' : 400,
                'frameHeight' : 450,
                'hideOnContentClick'	:false,
                'overlayShow'			:true,
                'overlayOpacity'		:0.6,
                'autoScale'				:true,
                'scrolling'             : 'no',
                'titleShow'             : false,
                'type'                  : 'ajax',
                'href'                  : href + (href.indexOf('?') == -1 ? '?' : '&') + 'ajax&from=' + encodeURIComponent(window.location.href)
            });
        });
        /*$('.shareViaEmail').click(function () {
            window.location.href = 'mailto:?subject=' + encodeURIComponent(document.title) + '&body=' + encodeURIComponent(window.location.href);
        });*/

        $('.detail-menu').on('click', 'li:not(.selected)', function() {
            $('.detail-menu li.selected').trigger('tabHide');
            $(this).trigger('tabShow');
            $(this).addClass('selected').siblings().removeClass('selected')
                .parents('div.content').find('div.detail-text').eq($(this).index()).fadeIn(150).siblings('div.detail-text').hide();
        })
        var hash = document.location.hash;
        if (hash.length > 1) {
            $('.detail-menu a[href=' + hash + ']').parents('li').each(function () {
                var $this = $(this);
                window.setTimeout(function () { $this.trigger('tabShow'); }, 100);
                $this.addClass('selected').siblings().removeClass('selected')
                    .parents('div.content').find('div.detail-text').eq($this.index()).fadeIn(150).siblings('div.detail-text').hide();
            });
        }

    })
})(jQuery)
