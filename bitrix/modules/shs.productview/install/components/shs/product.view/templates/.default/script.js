$(document).ready(function(){

    //console.log(ShsProductview);
    //$("#shs-ordered").html();
    //alert($("#shs-ordered").html());
    shsObj = $("#shs-productview").clone();
    $("#shs-productview").remove();
    $("body").append(shsObj);

    $("#shs-productview img").click(function(){
        $(this).parent().fadeOut(1000);
    });
    setTimeout(showOrdered, ShsProductview.timezaderzh*1000);

    function showOrdered()
    {
        $("#shs-productview").fadeIn(1000);
        $("#shs-productview").animate({bottom:'20px'}, 500);
        setTimeout(hideOrdered, ShsProductview.timeshow*1000);
    }

    function hideOrdered()
    {

        $("#shs-productview").animate({bottom:'150px', opacity:0}, 1500, function(){$("#shs-productview").remove()});
        //$("#shs-ordered").fadeOut(2000);
    }

})