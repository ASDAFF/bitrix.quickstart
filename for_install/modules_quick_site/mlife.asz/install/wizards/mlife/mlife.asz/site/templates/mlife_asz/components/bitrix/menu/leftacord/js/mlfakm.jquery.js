;(function($){
    $.fn.mlfakm = function(options){
        var p = $.extend({
            currentClass:'active',
            onlyOne:true,
            speed:500    
        }, options);
        return this.each(function(){
            var
            el = $(this).addClass('mlfakm'),
            linkItem = $('ul',el).prev('a');
            el.children(':last').addClass('last');
            $('ul',el).each(function(){
                $(this).children(':last').addClass('last');
            });
            $('ul',el).prev('a').addClass('harFull');
            el.find('.'+p.currentClass).parents('ul').show().prev('a').addClass(p.currentClass).addClass('harOpen');
			
            linkItem.on('click',function(){
                if($(this).next('ul').is(':hidden')){
                    $(this).addClass('harOpen');
                }else{
                    $(this).removeClass('harOpen');
                }
                if(p.onlyOne){
                    $(this).closest('ul').closest('ul').find('ul').not($(this).next('ul')).slideUp(p.speed).prev('a').removeClass('harOpen');
                    $(this).next('ul').slideToggle(p.speed);
                }else{
                    $(this).next('ul').stop(true).slideToggle(p.speed);  
                }
                return false;  
            });
        });
    };
})(jQuery);