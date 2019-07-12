$(function(){
    $('.carousel-container').each(function(){
        $(this).cycle({
            'prev': '.carousel-banner .carousel-prev',
            'next': '.carousel-banner .carousel-next',
            'timeout': 4000,
            'speed': 1000,
            'pause': 1,
            'width': 940,
            'height': 298
        });
    });
});
