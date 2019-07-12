/**
 * Created with JetBrains PhpStorm.
 * User: anton
 * Date: 04.10.13
 * Time: 14:21
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function(){
    $('input[data-hide-value-by-click]').click(function(){
        var value = $(this).val();
        var hideValue = $(this).attr('data-hide-value-by-click');
        if(hideValue == value) {
            $(this).val('');
        }
    })
});