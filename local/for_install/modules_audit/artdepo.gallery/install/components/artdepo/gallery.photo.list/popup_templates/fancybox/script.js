// jQuery http://jquery.com/
// imgLiquid https://github.com/karacas/imgLiquid
// fancyBox http://fancyapps.com/fancybox/
;(function(window){

if (!BX)
	return;

BX.ready(function(){

    function InitGallery(){
        BX.loadScript("//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.4/jquery.fancybox.pack.min.js", function(){
        BX.loadCSS("//cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.4/jquery.fancybox.min.css");
        $(document).ready(function() {      
            $(".popup-gallery a").fancybox({
                'loop'              : false,
		        'transitionIn'		: 'fade',
		        'transitionOut'		: 'fade',
		        'prevEffect'		: 'fade',
		        'nextEffect'		: 'fade',
		        'helpers'           : {
		            title	: { type : 'inside' }
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
