$(function () {
    $('.object-item')
        .on('mouseover', function () {
            $(this).addClass('big-object-item');
            $(this).removeClass('object-item');
        })
        .on('mouseout', function () {
            $(this).addClass('object-item');
            $(this).removeClass('big-object-item');
        });
});