$(document).ready(function(){
    var PagenationContainer = $('#pagination-container');
    PagenationContainer.on('click','a.more_goods', function () {
        var ajaxurl = PagenationContainer.find('div.bx-pagination  ul li.bx-pag-next a').attr('href');
        var thatTxt = $(this).html();
        var that = this;
        $(this).html('...');
        if(ajaxurl!==undefined){
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {'ajax_get_page': 'y'},
                dataType: "html",
                success: function (data) {
                    var AppendLi = $(data).find('.pagination-items .paginator-item');
                    var Pagination = $(data).filter('.pagination_wrap').html();
                    PagenationContainer.find('.pagination-items').append(AppendLi);
                    PagenationContainer.find('.pagination_wrap').html(Pagination);
                    history.pushState('', '', ajaxurl);
                    $(that).html(thatTxt);
                }
            });
        }
        return false;
    });
});