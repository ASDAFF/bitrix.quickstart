// jQuery http://jquery.com/
// imgLiquid https://github.com/karacas/imgLiquid
// Swipebox http://brutaldesign.github.io/swipebox/
;(function(window){

if (!BX)
	return;

BX.ready(function(){

    function InitGallery(){
        BX.loadScript("/bitrix/components/artdepo/gallery.photo.list/popup_templates/swipebox/jquery.swipebox.min.js", function(){
        BX.loadCSS("/bitrix/components/artdepo/gallery.photo.list/popup_templates/swipebox/swipebox.css");
        $(document).ready(function() {      
            $(".popup-gallery a").swipebox();
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
