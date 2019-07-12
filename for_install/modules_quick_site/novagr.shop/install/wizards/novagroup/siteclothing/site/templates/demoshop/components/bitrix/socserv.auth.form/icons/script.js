/**
 * Created with JetBrains PhpStorm.
 * User: anton
 * Date: 05.08.13
 * Time: 21:20
 * To change this template use File | Settings | File Templates.
 */
$(window).on('load', function () {

    $('.bx-system-auth-form .selectpicker').selectpicker({
        'selectedText': 'cat'
    });

    $('.bx-system-auth-form .selectpicker').selectpicker('hide');
});

function choiseAuthSocNetwork(SUFFIX)
{
    $('.bx-system-auth-form .selectpicker').change(function(){
        initAuthSocNetwork(SUFFIX);
    })
    $('.authbtn-socserv-login').click(function(){
        initAuthSocNetwork(SUFFIX);
    })
}

function initAuthSocNetwork(SUFFIX)
{
    var val = $('select[name=soc-auth-list].selectpicker').val();
    if(val!="user")
    {
        $('.authbtn').addClass('authbtn-bth-hide'); $('.authbtn-socserv-login').removeClass('authbtn-bth-hide');
        BxShowAuthFloat(val,  SUFFIX);
    } else {
        $('.authbtn').removeClass('authbtn-bth-hide'); $('.authbtn-socserv-login').addClass('authbtn-bth-hide');
    }
}