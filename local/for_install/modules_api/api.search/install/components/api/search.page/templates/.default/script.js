(function ($) {

    var defaults = {};

    var methods = {

        init: function (params) {

            var options = $.extend({}, defaults, params);

            if (!this.data('apiSearchPage')) {
                this.data('apiSearchPage', options);

                //clear_icon
                if ($(options.container_id).find(options.input_id).val())
                    $.fn.apiSearchPage('showClearIcon',options);


                var tmr;
                $(options.container_id)
                    .find(options.input_id)
                    .keyup(function (e) {

                        //clear_icon
                        if ($(this).val())
                            $.fn.apiSearchPage('showClearIcon',options);
                        else
                            $.fn.apiSearchPage('hideClearIcon',options);


                        clearTimeout(tmr);
                        tmr = setTimeout(function () {

                            if(e.keyCode != 32) //backspace
                                $.fn.apiSearchPage('execAjax', '', options);

                        }, options.wait_time);

                    })
                    .keydown(function () {
                        clearTimeout(tmr);
                    });


                //clear_icon
                $(options.container_id).on('click', options.clear_icon_id, function () {
                    $(options.container_id).find(options.input_id).val('');
                    $(options.container_id).find(options.result_id).html('');
                    $(this).hide();
                });
            }

            return this;
        },
        execAjax: function (url, options) {

	          url = url || window.location.href;

            $(options.container_id).find(options.ajax_icon_id).show();
            $(options.container_id).find(options.ajax_preload_id).show();

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    sessid: options.sessid,
                    q: $(options.container_id).find(options.input_id).val(),
                    API_SEARCH_PAGE_AJAX: 'Y'
                },
		            error: function(jqXHR, textStatus, errorThrown){
			            console.log('textStatus: ' + textStatus);
			            console.log('errorThrown: ' + errorThrown);
			            /*$.each( jqXHR, function(k, v){
			             console.log('key: ' + k + ', value: ' + v );
			             });*/
		            },
		            success: function (data) {
                    $(options.container_id).find(options.result_id).html(data);
                    $(options.container_id).find(options.ajax_icon_id).hide();
                    $(options.container_id).find(options.ajax_preload_id).hide();

                    $('html, body').animate({
                        scrollTop: ($(options.container_id).offset().top - 50)
                    }, 500);

                    $(options.container_id)
                        .find(options.pagination_id + ' a')
                        .click(function () {

                            if ($(this).attr('href').length)
                                $.fn.apiSearchPage('execAjax', $(this).attr('href'), options);

                            return false;
                        });
                }
            });


        },
        showClearIcon: function (options) {
            $(options.container_id).find(options.clear_icon_id).fadeIn(200);
        },
        hideClearIcon: function (options) {
            $(options.container_id).find(options.clear_icon_id).hide();
        }
    };

    $.fn.apiSearchPage = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Error! Method "' + method + '" not found in plugin $.fn.apiSearchPage');
        }
    };

})(jQuery);