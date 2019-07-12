$(document).ready(function(){
    $("a[rel=pv_image_group_" + idVarPv + "]").fancybox({
        'transitionIn'		: 'none',
        'transitionOut'		: 'none',
        'titlePosition' 	: 'over',
        'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
            return '<span id="fancybox-title-over">' + tplImage + ' ' + (currentIndex + 1) + ' / ' + currentArray.length + ($("#img_" + $(currentArray[currentIndex]).attr('id')).attr("title").length ? '&nbsp;- ' + $("#img_" + $(currentArray[currentIndex]).attr('id')).attr("title") : '') + '</span>';
        }
    });
    
    $(".preview-gal-img").each(function(){$(this).stop().animate({opacity:'0.5'})});
    $(".preview-gal-img").hover(function(){
        $(this).stop().animate({opacity:'1.0'})
    },
        function(){$(this).stop().animate({opacity:'0.5'})}
    );
});