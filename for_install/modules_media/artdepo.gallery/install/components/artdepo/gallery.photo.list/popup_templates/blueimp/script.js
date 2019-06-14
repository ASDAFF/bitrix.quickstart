// jQuery http://jquery.com/
// imgLiquid https://github.com/karacas/imgLiquid
// blueimp Gallery http://blueimp.github.io/Gallery/
;(function(window){

if (!BX)
	return;

BX.ready(function(){

    function InitGallery(){
        BX.loadScript("/bitrix/components/artdepo/gallery.photo.list/popup_templates/blueimp/js/jquery.blueimp-gallery.min.js", function(){
        BX.loadCSS("/bitrix/components/artdepo/gallery.photo.list/popup_templates/blueimp/css/blueimp-gallery.min.css");
        $(document).ready(function() {
            $(".popup-gallery").after('\
                <div id="blueimp-gallery" class="blueimp-gallery" style="display:none">\
                    <div class="slides"></div>\
                    <h3 class="title"></h3>\
                    <a class="prev">&lsaquo;</a>\
                    <a class="next">&rsaquo;</a>\
                    <a class="close">&#739;</a>\
                    <a class="play-pause"></a>\
                    <ol class="indicator"></ol>\
                </div>');
        });
        });
    }
    
    var jQueryNotFoud = (typeof jQuery == 'undefined');
    if(jQueryNotFoud){
        BX.loadScript("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js", function(){
            InitGallery();
        });
    } else {
        InitGallery();
    }
});

})(window);
