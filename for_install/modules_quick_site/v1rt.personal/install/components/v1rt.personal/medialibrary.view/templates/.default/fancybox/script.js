$(document).ready(function(){
    $("a[rel=image_group_" + idVar + "]").fancybox({
        'transitionIn'		: 'none',
        'transitionOut'		: 'none',
        'titlePosition' 	: 'over',
        'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
            return '<span id="fancybox-title-over">' + tplImage + ' ' + (currentIndex + 1) + ' / ' + currentArray.length + ($("#img_" + $(currentArray[currentIndex]).attr('id')).attr("title").length ? '&nbsp;- ' + $("#img_" + $(currentArray[currentIndex]).attr('id')).attr("title") : '') + '</span>';
        }
    });
});