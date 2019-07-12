$(document).on('ready', function(){
    var loading = false;
    $(window).scroll(function() {
        if ($('#infinity-next-page').size() && !loading) {
            if ($(window).scrollTop()+100 >= $(document).height()-$(window).height()) {
                loading = true;
                $.get($('#infinity-next-page').attr('href'), {is_ajax: 'y'}, function(data){
                    $('#infinity-next-page').after(data);
                    $('#infinity-next-page').remove();
                    loading = false;
                });
            }
        }
    });
});
