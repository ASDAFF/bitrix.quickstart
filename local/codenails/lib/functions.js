function drawPlacemark(arShopsItem,rsPlacemark) {
    arShopsItem.each(function(){
        if($(this).hasClass('cityempty') || $(this).hasClass('typeempty')){
            rsPlacemark[$(this).data('id')].options.set('visible', false);
        } else {
            rsPlacemark[$(this).data('id')].options.set('visible', true);
        }
    });
}

