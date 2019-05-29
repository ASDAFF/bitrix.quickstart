$(document).ready(function(){
    var requestUri = location.pathname;
    $.ajax({
        dataType: "json",
        url: '/bitrix/tools/step2use.redirects/s2u_composite_ajax.php',
        data: {
            requestUri: requestUri
        },
        success: function (response) {
            if(response.newUrl){
                location.href = response.newUrl;
            }
        }
    });

});