// jQuery http://jquery.com/
// imgLiquid https://github.com/karacas/imgLiquid
// Magnific Popup http://dimsemenov.com/plugins/magnific-popup/
;(function(window){

if (!BX)
	return;

BX.ready(function(){

    function InitGallery(){
        BX.loadScript("//cdn.jsdelivr.net/jquery.magnific-popup/0.8.9/jquery.magnific-popup.min.js", function(){
        BX.loadCSS("//cdn.jsdelivr.net/jquery.magnific-popup/0.8.9/magnific-popup.css");
        $(document).ready(function() {      
            $('.popup-gallery').magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery:{
                    enabled:true,
                    preload: [0,2],
                    navigateByImgClick: true,
                },
                image: {
                    titleSrc: 'title'
                }
            });
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
