/**
 * Created with JetBrains PhpStorm.
 * User: anton
 * Date: 31.10.13
 * Time: 19:34
 * To change this template use File | Settings | File Templates.
 */
// фукнция вовращает длину всех li у #menu-top
function getSummLiWidth() {
    var widthLi = 0;
    $('#menu-top li.root').each(function(index, element) {
        widthLi = widthLi + $(element).width()
    });
    return widthLi;
}
// проверка выезжает ли меню
function checkWidth() {
    var ulWidth = $("#menu-top").width();
    var liWidth = getSummLiWidth();
    if (liWidth > ulWidth) {
        return true;
    }
    return false;
}

function fontSize() {
    if (checkWidth()) {

// код, который высчитывает fontSize
        var currentFontSize = $("#menu-top li").css("font-size").replace('px', '');
        var j=1;
        while (j<15 && checkWidth() == true && currentFontSize > 3) {
            currentFontSize = currentFontSize-1;
            $('#menu-top li').css({fontSize: currentFontSize+'px'});
            j++;
        }
    }
}
$(function() { fontSize();});

function checkImgAttrbs(){
    $("#workarea img").each(function(){
        if($.trim($(this).attr("src"))==""){
            $(this).attr("src","/local/templates/demoshop/images/nophoto.png");
        }
    });
}
setInterval(checkImgAttrbs, 500);
