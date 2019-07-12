var needOffset = 0;
var elementsWidth = 0;

function RSMONOPOLY_hideLis(resize) {
    //style 2 hidden li`s
    if( $('.main-menu-nav').length>0 ) {
        var $menu = $('.main-menu-nav');
		$menu.find('.other').removeAttr('style');
    	if($(document).width()>=970) {
    		element = $menu.find('.lvl1')[0];
    		needOffset = $(element).offset();
    		needOffset = needOffset.top;
    		$menu.find('.lvl1').each(function(index) {
    			offset = $(this).offset();
    			offset = offset.top;
    			if(offset!=needOffset) {
    				$(this).addClass('invisible');
    				$menu.find('.other').removeClass('invisible');
    				if($menu.find('.other #element'+$(this).attr('id')).length>=1) {
    					$menu.find('.other #element'+$(this).attr('id')).show();
    				} else {
						if(resize) {
							$menu.find('.other ul.dropdown-menu').prepend('<li class="other_li" id="element'+$(this).attr('id')+'">'+$(this).html()+'</li>');
						} else {
							$menu.find('.other ul.dropdown-menu').append('<li class="other_li" id="element'+$(this).attr('id')+'">'+$(this).html()+'</li>');
						}
    				}
    			} else {
    				$(this).removeClass('invisible');
    				if($menu.find('.other #element'+$(this).attr('id')).length>=1) {
    					$menu.find('.other #element'+$(this).attr('id')).hide();
    				}
    			}
    		});
    	} else {
    		$menu.find('.lvl1').each(function(index) {
    			$(this).removeClass('invisible');
    			$menu.find('.other').addClass('invisible');
    		});
    	}
		elementsWidth = 0;
    	$menu.find('li.lvl1').each(function(index){
    		if (!$(this).hasClass('invisible')) {
    			elementsWidth = elementsWidth + $(this).outerWidth(true);
    		}
    	});
    	width = $menu.width() - elementsWidth;
    	$menu.find('.other').css('width', width);
    	$menu.removeAttr('style');
		if ($menu.find('.lvl1.invisible').length==0) {
			$menu.find('.other').hide();
		} else {
			$menu.find('.other').show();
		}
    }
}

// Area2Darken
function RSMONOPOLY_Area2Darken(areaObj) {
	areaObj.toggleClass('area2darken');
}

// drop fancybox on mobile
function RSMONOPOLY_DropFancy() {
    if($(document).width()<600) {
        $('.fancyajax').removeClass('fancyajax fancybox.ajax').addClass('fancyajaxwait');
    } else {
        $('.fancyajaxwait').removeClass('fancyajaxwait').addClass('fancyajax fancybox.ajax');
    }
}

// popup gallery
function RSMONOPOLY_PopupGallerySetHeight() {
    if($('.popupgallery').length>0) {
        if($(document).width()>767) {
            var innerHeight = parseInt($('.popupgallery').parents('.fancybox-inner').height()),
                h1 = innerHeight-55,
                h2 = h1-30,
                h3 = innerHeight-55-parseInt($('.popupgallery').find('.preview').height());
            $('.popupgallery').find('.thumbs.style1').css('maxHeight', h3 );
        } else {
            var fullrightHeight = parseInt($('.popupgallery').find('.fullright').height());
            var innerHeight = parseInt($('.popupgallery').parents('.fancybox-inner').height()),
                h1 = innerHeight-55-fullrightHeight-25,
                h2 = h1-30-fullrightHeight-25,
                h3 = innerHeight-55-parseInt($('.popupgallery').find('.preview').height());
            $('.popupgallery').find('.thumbs.style1').css('maxHeight', 100 );
        }
        $('.popupgallery').find('.changeit').css('height', h1 );
        $('.popupgallery').find('.changeit').find('img').css('maxHeight', h2 );
    }
}
function RSMONOPOLY_PopupGallerySetPicture() {
    if($('.popupgallery').length>0) {
        $('.js-gallery').find('.thumbs').find('a[href="'+$('.changeFromSlider:not(.cantopen)').find('img').attr('src')+'"]').trigger('click');
    }
}

// set set
function RSMONOPOLY_SetSet() {
    RSMONOPOLY_SetCompared();
}
// set compare
function RSMONOPOLY_SetCompared() {
    $('.js-compare').removeClass('checked');
    for(element_id in RSMONOPOLY_COMPARE) {
        if(RSMONOPOLY_COMPARE[element_id]=='Y' && $('.js-elementid'+element_id).find('.js-compare').length>0) {
            $('.js-elementid'+element_id).find('.js-compare').addClass('checked').find('.count').html(' ('+RS_MONOPOLY_COUNT_COMPARE+')');
        }
    }
    $('.js-compare:not(.checked)').find('.count').html('');
}

$(document).ready(function(){
	
	$(document).on("closeFancy", function () {
		if($.fancybox && $.fancybox.isOpen) {
			setTimeout($.fancybox.close, 1500);
		}
	});

    $(document).on('click','a.area2darken',function(e){
        console.info( 'Area2Darken - block click' );
        e.preventDefault();
        e.stopImmediatePropagation();
    });

    RSMONOPOLY_DropFancy();

    $(window).resize(function(){
        setTimeout(function(){
            RSMONOPOLY_hideLis(true);
            RSMONOPOLY_DropFancy();
        }, 100);
    });
    $(window).load(function(){
        setTimeout(function(){
            RSMONOPOLY_hideLis(false);
            RSMONOPOLY_DropFancy();
        }, 100);
    });

    // main menu
    $(document).on('click','.main-menu-nav .dropdown a > span',function(){
        $(this).parent().parent().toggleClass('open');
        return false;
    });

	//captcha
	$(document).on('click', '.reloadCaptcha', function(){
        var $object = $(this).parents('.captcha_wrap');
        BX.ajax.loadJSON("/bitrix/tools/ajax_captcha.php", function(data) {
            $object.find('.captchaImg').attr('src','/bitrix/tools/captcha.php?captcha_sid='+data.captcha_sid);
            $object.find('.captchaSid').val(data.captcha_sid);
        });
        return false;
    });

    // header search box
    $(document).on('click', '.nav .search-btn', function () {
        var $searchBtn = $(this);
        if($searchBtn.hasClass('lupa')){
            $('.search-open').fadeIn(500,function(){
                $searchBtn.removeClass('lupa');
                $searchBtn.addClass('remove');
            });
        } else {
            $('.search-open').fadeOut(500,function(){
                $searchBtn.addClass('lupa');
                $searchBtn.removeClass('remove');
            });
        }   
    });

    // click at first main menu
    $(document).on('show.bs.dropdown', 'header .main-menu-nav li.dropdown, header .main-menu-nav li.dropdown > a', function(e){
        console.warn( 'script.js -> preventDefault' );
        e.preventDefault();
    });

     // click at sidebar menu
    $(document).on('shown.bs.collapse', '.nav-sidebar', function(e){
        $(e.target).parent().addClass('showed');
    }).on('hidden.bs.collapse', '.nav-sidebar', function(e){
        $(e.target).parent().removeClass('showed');
    });

    $('.owl').each(function(){
        var $owl = $(this),
            RSMONOPOLY_change_speed = 2000,
            RSMONOPOLY_change_delay = 8000,
            RSMONOPOLY_margin = 0,
            RSMONOPOLY_responsive = {0:{items:1},768:{items:1},1200:{items:1}};
        if( parseInt($owl.data('changespeed'))>0 ) {
            RSMONOPOLY_change_speed = $owl.data('changespeed');
        }
        if( parseInt($owl.data('changedelay'))>0 ) {
            RSMONOPOLY_change_delay = $owl.data('changedelay');
        }
        if( parseInt($owl.data('margin'))>0 ) {
            RSMONOPOLY_margin = $owl.data('margin');
        }
        if( $owl.data('responsive')!='' && (typeof($owl.data('responsive'))=='object') ) {
            RSMONOPOLY_responsive = $owl.data('responsive');
        }
        if( $owl.find('.item').length>1 ) {
            $owl.owlCarousel({
                items               : 4
                ,margin             : RSMONOPOLY_margin
                ,loop               : true
                ,autoplay           : true
                ,nav                : true
                ,navText            : ['<span></span>','<span></span>']
                ,navClass           : ['prev','next']
                ,autoplaySpeed      : RSMONOPOLY_change_speed
                ,autoplayTimeout    : RSMONOPOLY_change_delay
                ,smartSpeed         : RSMONOPOLY_change_speed
                ,onInitialize       : function (e) {
                    $owl.addClass('owl-carousel owl-theme');
                    if (this.$element.children().length <= this.settings.items) {
                        this.settings.loop = false;
                    }
                }
                ,onResize           : function (e) {
                    if (this._items.length <= this.settings.items) {
                        this.settings.loop = false;
                    }
                }
                ,onRefreshed        : function(){
                    $owl.removeClass('noscroll');
                    if($owl.find('.cloned').length<1) {
                        $owl.addClass('noscroll');
                    }
                }
                ,responsive         : RSMONOPOLY_responsive
            });
        }
    });

    // fancybox -> popup links
    var RSGoPro_FancyOptions1 = {},
        RSGoPro_FancyOptions2 = {};
    RSGoPro_FancyOptions1 = {
        maxWidth            : 400
        ,maxHeight          : 750
        ,minWidth           : 200
        ,minHeight          : 100
        ,openEffect         : 'none'
        ,closeEffect        : 'none'
        ,padding            : [20,24,15,24]
        ,helpers            : {
            title : {
                type : 'inside'
                ,position : 'top'
            }
        }
        ,beforeShow         : function(){
            var $element = $(this.element);
            if( $element.data('insertdata')!='' && (typeof($element.data('insertdata'))=='object') ) {
                setTimeout(function(){
                    var obj = $element.data('insertdata');
                    for(fieldName in obj) {
                        $('.fancybox-inner').find('[name="'+fieldName+'"]').val( obj[fieldName] );
                    }
                },50);
            }
            $(document).trigger('RSMONOPOLY_fancyBeforeShow');
        }
        ,afterShow          : function(){
            setTimeout(function(){
                $.fancybox.toggle();
                RSMONOPOLY_PopupGallerySetHeight();
                RSMONOPOLY_PopupGallerySetPicture();
                $(document).trigger('RSMONOPOLY_fancyAfterShow');
            },50);
        }
        ,onUpdate           : function(){
            setTimeout(function(){
                RSMONOPOLY_PopupGallerySetHeight();
                $(document).trigger('RSMONOPOLY_fancyOnUpdate');
            },50);
        }
    };
    $('.fancyajax').fancybox(RSGoPro_FancyOptions1);
    RSGoPro_FancyOptions2 = $.extend({}, RSGoPro_FancyOptions1);
    RSGoPro_FancyOptions2.ajax = {
        type: "POST",
        cache : false,
        data: { 'AJAX_CALL':'Y', 'POPUP_GALLERY':'Y' }
    };
    delete RSGoPro_FancyOptions2.minHeight;
    delete RSGoPro_FancyOptions2.maxHeight;
    RSGoPro_FancyOptions2.maxWidth = 1091;
    RSGoPro_FancyOptions2.minWidth = 600;
    RSGoPro_FancyOptions2.width = '90%';
    RSGoPro_FancyOptions2.height = '90%';
    RSGoPro_FancyOptions2.autoSize = false;
	
	$('.changeFromSlider:not(.cantopen)').fancybox(RSGoPro_FancyOptions2);
		
    $(document).on('click','.cantopen',function(){
        return false;
    });

    // pictures slider
    $(document).on('click','.thumbs .thumb a',function(){
        var $link = $(this);
        var $thumbs = $link.parents('.thumbs');
        $thumbs.find('.thumb').removeClass('checked');
        $thumbs.find( '.thumb.pic'+$link.data('index') ).addClass('checked');
        $( $thumbs.data('changeto') ).attr('src',$(this).attr('href'));
		
        $(document).trigger('RSMONOPOLY_changePicture', {
			id: $(this).data("index")
		});
        return false;
    });
    $(document).on('click','.js-nav',function(){
        var $btn = $(this),
            $gallery = $(this).parents('.js-gallery'),
            $curPic = $gallery.find('.thumb.checked'),
            $prev = ( $curPic.prev().hasClass('thumb') ? $curPic.prev() : $gallery.find('.thumb:last') ),
            $next = ( $curPic.next().hasClass('thumb') ? $curPic.next() : $gallery.find('.thumb:first') );
        if($btn.hasClass('prev')) {
            $prev.find('a').trigger('click');
        } else {
            $next.find('a').trigger('click');
        }
        return false;
    }).on('mouseenter mouseleave','.js-nav',function(){
        $('html').toggleClass('disableSelection');
    });
    $(document).on('click','.popupgallery .changeit img',function(){
        $('.popupgallery').find('.js-nav.next').trigger('click');
    });

    /* forms error show */
    $(document).on('focusin', '.form-control', function() {
        $(this).next().addClass('focused');
    });
    $(document).on('focusout', '.form-control', function() {
        $(this).next().removeClass('focused');
    });
    $(document).on('focusout', '.req-input', function() {
        if($(this).val()=='') {
            $(this).addClass('must-be-filled almost-filled');
            $(this).attr("placeholder", BX.message('RSMONOPOLY_JS_REQUIRED_FIELD') );
        }
    }).on('focusin', '.req-input', function() {
        if($(this).hasClass('must-be-filled')) {
            $(this).removeClass('must-be-filled almost-filled');
        }
    });
	// check custom bitrix.forms
	$(document).on('click', '.dropdown-menu .variable', function() {
		$(this).parents('.dropdown').find('.dropdown-button').html($(this).html()+'<span class="right-arrow-caret"></span>');
		$(this).parents('.dropdown').find('input[type="hidden"]').val($(this).data('value'));
	});
	$(document).on('click', '.btn.btn-primary', function() {
		submittedFlag = false;
		$(this).parents('form').find('.field-wrap.req').each(function() {
			if ( ($(this).find('input.req-input').val()=="" && $(this).hasClass('text-wrap')) || 
				 ($(this).find('select option:selected').length==0 && $(this).hasClass('select-wrap')) || 
				 ($(this).find('input.req-input').val()=="" && $(this).hasClass('calendar-wrap')) || 
				 ($(this).find('textarea').val()=="" && $(this).hasClass('textarea-wrap')) || 
				 ($(this).find('input.req-input').val()=="" && $(this).hasClass('file-wrap')) || 
				 ($(this).find('input:checked').length==0 && $(this).hasClass('choice-wrap'))
			    ) {
				$(this).addClass('has-error');
				submittedFlag = true;
			} else {
				$(this).removeClass('has-error');
			}
		});
		if (submittedFlag) {
			return false;
		}
	});
	$(document).on('click', '.checkbox label', function() {
		$(this).parent().find('input').checked = !$(this).parent().find('input').checked;	
	});
	$(document).on('change', '.almost-filled', function() {
		$(this).removeClass('almost-filled').attr('placeholder', '');
	});

    // AJAX -> add2compare 
    $(document).on('click','.js-compare',function(){
        console.info( 'AJAX -> add2compare ' );
        var $linkObj = $(this),
            url = $linkObj.parents('.js-element').find('.js-detail_page_url').attr('href'),
            id = parseInt( $linkObj.parents('.js-element').data('elementid') ),
            action = '';
        if(id>0){
            if( url.indexOf('?')==-1 ) {
                url = url + '?';
            }
            if( RSMONOPOLY_COMPARE[id]=='Y' || parseInt(RSMONOPOLY_COMPARE[id])>0 ) {
                action = 'DELETE_FROM_COMPARE_LIST';
            } else {
                action = 'ADD_TO_COMPARE_LIST';
            }
            url = url+'AJAX_CALL=Y&action='+action+'&id='+id;
            RSMONOPOLY_Area2Darken($('.js-compare'));
            $.getJSON(url, {}, function(json){
                if(json.TYPE=="OK"){
                    if( action=='DELETE_FROM_COMPARE_LIST' ){ // delete from compare{
                        delete RSMONOPOLY_COMPARE[id];
                    } else { // add to compare
                        RSMONOPOLY_COMPARE[id] = 'Y';
                    }
                    RS_MONOPOLY_COUNT_COMPARE = json.COUNT;
                    if( RS_MONOPOLY_COUNT_COMPARE>0 ){
                        $('.comparelist').removeClass('hidden').find('.count').html( json.COUNT_WITH_WORD );
                    } else {
                        $('.comparelist').addClass('hidden');
                    }
                } else {
                    console.warn( 'compare - error responsed' );
                }
            }).fail(function(data){
                console.warn( 'compare - fail request' );
            }).always(function(){
                RSMONOPOLY_Area2Darken($('.js-compare'));
                RSMONOPOLY_SetCompared();
            });
        }
        return false;
    });

});

//custom load files
$(function(){
    var wrapper = $( ".file_upload" ),
        inp = wrapper.find( "input" ),
        btn = wrapper.find( ".file-link" ),
        lbl = wrapper.find( ".file-link" );

    // Yep, it works!
    btn.add( lbl ).click(function(){
        inp.click();
    });

    var file_api = ( window.File && window.FileReader && window.FileList && window.Blob ) ? true : false;

    inp.change(function(){

        var file_name;
        if( file_api && inp[ 0 ].files[ 0 ] )
            file_name = inp[ 0 ].files[ 0 ].name;
        else
            file_name = inp.val().replace( "C:\\fakepath\\", '' );
        if( ! file_name.length )
            return;

        if( lbl.is( ":visible" ) ){
            lbl.text( file_name );
            btn.text( file_name );
        }else
            btn.text( file_name );
    }).change();

});

// owl init
function owlInit($owl, params) {
	
	var defaultParams = {
		items: 4,
		margin: 30,
		loop: true,
		autoplay: false,
		merge: true,
		nav: true,
		navText: ['<span></span>','<span></span>'],
		navClass: ['prev','next'],
		responsive: {},
		
		onInitialize: function (e) {
			$owl.addClass('owl-carousel owl-theme');
			if (this.$element.children().length <= this.settings.items) {
				this.settings.loop = false;
			}
		},
		onResize: function (e) {
			this.destroy();
			owlInit($owl, params);
		},
	};
	
	params = $.extend({}, defaultParams, params);
	
	return $owl.owlCarousel(params);
}