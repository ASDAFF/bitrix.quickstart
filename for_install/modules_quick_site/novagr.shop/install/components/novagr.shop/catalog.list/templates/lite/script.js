/**
 * Created with JetBrains PhpStorm.
 * User: anton
 * Date: 23.07.13
 * Time: 18:10
 * To change this template use File | Settings | File Templates.
 */
$(window).on('load', function() {

    $('#workarea .selectpicker').selectpicker({
        'selectedText' : 'cat'
    });

    $('#workarea .selectpicker').selectpicker('hide');
});
$(document).ajaxComplete(function() {
    $('#workarea .selectpicker').selectpicker({
        'selectedText' : 'cat'
    });
});