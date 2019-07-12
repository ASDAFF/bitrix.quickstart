function preloader(images,callback){
    var length = images.length;
    var count = 0;
    $.each(images,function(i,src){
        var image = new Image();
        image.src = src;
        image.onload= function(){
            count++;
            if (count>=length){
                callback();
            }
        }
    })
}

function get_max_height(elements){
    var max = 0;
    $(elements).css({
        "height": "auto"
    })
    elements.each(function(i,element){
        if( max < $(element).height()){ 
            max = $(element).height();    
        }
    })
    return max;
}
(function($){
    $.fn.extend({
        transition: function(prop, val){
            var prefix = ['-webkit-', '-moz-', '-o-', 'o-',''];
            $(this).each(function(){
                var self = $(this);
                for (var i = prefix.length - 1, pref; i >= 0; i--) {
                    self.css(prefix[i]+'transition'+prop, val);
                };
            })
            return $(this);
        }
    })
})(jQuery)

jQuery(document).ready(function($) {
   /* $('#map').css({
        'visibility': 'hidden',
        'height':1
    });*/

    $('.pokazat a').on('click', function()
        {
            /*if ($(this).hasClass('active')) {
                $('#map').css({
                    'visibility': 'visible',
                    'height': 400,
                    'margin-bottom':40
                });
            } else {
                $('#map').css({
                    'visibility': 'hidden',
                    'height':1,
                    'margin-bottom':0
                });
            }*/
            $("#map").toggleClass('active');
        }
    );

	$('#myTab a').on('click', function(){		
		if ($(this).attr('href') == '#quick')
			$('.no_quickly').css('display', 'none');
		else
			$('.no_quickly').css('display', 'block');
	});

    
    $('.form_stars li').on('click', function(){
        $('.form_stars .star').removeClass('active');
        $(this).addClass('active');
        $('#rating_value').val($(this).index()+1);  
    });

    var button = $('button[name=iblock_submit]');
    var submit = $('input[name=iblock_submit]');
    button.toggle();
    submit.toggle();
	$("#submit_anketa").toggle();
    button.on('click', function(){submit.click()});


	$('.colorbox_form').colorbox({
	    	iframe: true, 
            width: '90%', 
	    	maxWidth: 562, 
	    	height: 385,
	    	autoScale: true,
	    	autoDimensions: true,
	    	onComplete: function(){
	    		$('#cboxClose').hide();
                $(window).off('resize.colorbox_form')
                $(window).on('resize.colorbox_form',function(){
                    $.colorbox.resize({
                        width: ($(window).width()<562)?'90%':562,
                        height: 445
                    })
                }).trigger('resize.colorbox_form')
	    	}
	    });
    // var max = 0;  
    var images = []
    var thumbs =  $(".thumbnails .thumbnail");
    thumbs.find("img").each(function(indx, element){  
        images.push($(this).attr('src'));
    })
    preloader(images, function(){
        thumbs.transition('-duration', '0s').css({
            "height": get_max_height(thumbs)
        })
    })

    $(window).on('resize',function(){
        thumbs.css({
            "height": get_max_height(thumbs)
        })
    })

    var maks = 0;  
    $(".in .serv").each(function(indx, element){  
        if( maks < $(element).height()){ 
            maks = $(element).height();    
        }
    });
    $(".in .serv").height(maks);
});