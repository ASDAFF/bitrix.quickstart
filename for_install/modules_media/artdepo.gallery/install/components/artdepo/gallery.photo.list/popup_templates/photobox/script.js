// jQuery http://jquery.com/
// imgLiquid https://github.com/karacas/imgLiquid
// Photobox http://dropthebit.com/demos/photobox/
;(function(window){

if (!BX)
	return;

BX.ready(function(){

    function InitGallery(){
        BX.loadScript("/bitrix/components/artdepo/gallery.photo.list/popup_templates/photobox/photobox.js", function(){
            BX.loadCSS("/bitrix/components/artdepo/gallery.photo.list/popup_templates/photobox/photobox.css");
            if ( BX.browser.IsIE() ) {
                BX.loadCSS("/bitrix/components/artdepo/gallery.photo.list/popup_templates/photobox/photobox.ie.css");
            }
            $(document).ready(function() {
                $(".popup-gallery").photobox('a',{ time:0 });
            });
        });
    }
    
    var jQueryNotFoud = (typeof jQuery == 'undefined');
    if(jQueryNotFoud){
        BX.loadScript("http://yandex.st/jquery/1.9.1/jquery.min.js", function(){
            InitGallery();
        });
    } else {
        InitGallery();
    }
});

})(window);
