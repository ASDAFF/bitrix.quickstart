(function($) {
$(function() {

  $(document).bind('click', function(e) {
		var clicked = $(e.target);
		if (!clicked.parents().hasClass('dropdown')) {
			$('span.selectbox ul.dropdown').hide().find('li.sel').addClass('selected');
			$('span.selectbox').removeClass('focused');
		}
	});

	$('select.styled').each(function() {
		var self = $(this);
		var option = self.find('option');
		var optionSelected = self.find('option:selected');
		var dropdown = '';
		var selectText = self.find('option:first').text();
		if (optionSelected.length) selectText = optionSelected.text();

		var selectbox = $('<span>')
							.addClass('selectbox')
							.css({
								"display": "inline-block",
								"position": "relative"
							});
		var select = $('<span>')
							.addClass('select')
							.css({
								"float": "left",
								"position": "relative",
								"z-index": 100
							});
		var dropdown = $('<ul>')
							.addClass('dropdown')
							.css({
								"position": "absolute",
								"list-style": "none"
							})
		var text = $('<span>')
							.addClass('text')
							.text(selectText);
							
		select.append(text,$('<b class="trigger"><i class="arrow"></i></b>'));
		option.each(function(i){
			var cls = '';

			if ( $(this).is(':selected') ) cls = 'selected';
			if ( $(this).is(':disabled') ) cls = 'disabled';

			var item = $('<li>').addClass(cls).text($(this).text());
			dropdown.append(item);

			(function(item,dropdown,option,self){

				item.on('click',function(){
					self.find('option').attr('selected','');

					option.attr('selected','selected');
					dropdown.find('li').removeClass('selected');
					$(this).addClass('selected');
					text.text($(this).text());
					dropdown.hide();
					selectbox.removeClass('focused');
				})
			})(item,dropdown,$(this),self)

		})
		selectbox.append(select,dropdown);
		dropdown.hide()

		self.before(selectbox).css({position: 'absolute', left: -9999});

		dropdown.css({top: select.outerHeight()});

		select.click(function() {
			if ( dropdown.is(':hidden') ) {
				dropdown.show();
				selectbox.addClass('focused');
			} else {
				dropdown.hide();
				selectbox.removeClass('focused');
			}
			return false;
		});


	});

})
})(jQuery);


/*Popup*/
var html = document.documentElement;
$(function($) {
    var settings;
    $.fn.jpopup = function( options ) {
        return this.each(function()	{
            settings = $.extend({

            },options||{});
            
			var $obj = $(this);
            
			$obj.on( 'click', function( event ){
				event.preventDefault();
				
            });

        });
    };
});
$(function() {
$('div.jpopup').each(function () {
        var $obj = $(this);
        $obj.click(function(event) {
            var $event__target = $(event.target);
            if ($event__target.hasClass('jpopup__main__td') || $event__target.hasClass('jpopup__in') || $event__target.hasClass('jpopup__close')) {
                hidePopup($('div.jpopup'));
                return false;
            }
        });
        $(window).keydown(function (event) {
            if (event.keyCode == 27) {
                hidePopup('div.jpopup');
            }
        });
    });
});


function showPopup(obj) {
    var $popup = $(obj);
    var $html = $(html);
    var $body = $(document.body);
    var $body__width = $body.width();
    var $html__scrollTop = $html.scrollTop();
    $html.css({'overflow':'hidden'}).scrollTop($html__scrollTop).attr('data-scroll-top', $html__scrollTop);
    var $body__width__without__scroll = $body.width();
    var scroll__width = $body__width__without__scroll - $body__width;
    $html.css({'paddingRight':scroll__width + 'px'});
    $popup.removeClass('hide');
}

function hidePopup(obj) {
    var $popup = $(obj);
    var $html = $(html);
    $popup.addClass('hide');
    $html.css({'overflow':'', 'paddingRight':''}).scrollTop($html.attr('data-scroll-top'));
}


/*
Form Validator
*/
		$(function(){

		if($('form.required').length){
		
		$('form.required').each(function(){
			var $obj = $(this);
			var $f = $('.required',$obj);
			var reg_phone_number = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/;
			var reg_email = /^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z])+$/;
			$obj.submit(function(){
				var err = 0;
				
				$f.removeClass('error-field').each(function(){
					var $i = $(this);
					if($i.siblings('span.error-message').length){
						$i.removeClass('error-field').siblings('span.error-message').remove();
						$i.unwrap();
					}
					$i.siblings('span.correct-message').remove();
					
					if($i.val()==''){
											
						$i.siblings('span.correct-message').remove();
						$i.addClass('error-field').wrap('<span class="o-error-field clearfix" />').parent().append('<span class="error-message">'+$i.attr('data-err-msg-empty')+'</span>');
						err++;
				 	} else {
						$i.after('<span class="correct-message"/>');
					}
					
					if(($i.val()!='')&&$i.attr('data-type')=='tel') {
						$i.siblings('span.correct-message').remove();
						if(!$i.val().match(reg_phone_number)){
										
							
							$i.addClass('error-field').wrap('<span class="o-error-field" />').parent().append('<span class="error-message">'+$i.attr('data-err-msg-correct')+'</span>');
							err++;
						} else {
							$i.after('<span class="correct-message"/>');
						}
					} 
					
					
					if(($i.val()!='')&&$i.attr('data-type') == 'repassword') {
						$i.siblings('span.correct-message').remove();
						if($i.val()!=$f.filter('.required-password').val()){
										
								
							$i.addClass('error-field').wrap('<span class="o-error-field" />').parent().append('<span class="error-message">'+$i.attr('data-err-msg-correct')+'</span>');
							err++;
							} else  {
								$i.after('<span class="correct-message"/>');
							}
					} 
					
					if(($i.val()!='')&&$i.attr('data-type')=='email') {
						$i.siblings('span.correct-message').remove();
						if(!$i.val().match(reg_email)){				
							
							$i.addClass('error-field').wrap('<span class="o-error-field" />').parent().append('<span class="error-message">'+$i.attr('data-err-msg-correct')+'</span>');
							err++;
						} else {
							$i.after('<span class="correct-message"/>');
						}
					} 
					
					
				});
				
				if(err>0){
					return false;
				}
				
			});
		});
	}
    });
    
$(function() {
    $('.content .project:even').addClass('mr-34');
    $('input.f_type').click(function() {
        $('div.f_type').removeClass('hide');
        $('div.s_type').addClass('hide');
    })
    $('input.s_type').click(function() {
        $('div.s_type').removeClass('hide');
        $('div.f_type').addClass('hide');
    })
    var cont_height = $('.conteiner').height();
    $('aside.f_r').height(cont_height - 15)
})

/****Lightbox******/
$(function() {
            $('a[href$=".jpg"], a[href$=".png"], a[href$=".gif"], a[href$=".JPG"], a[href$=".PNG"], a[href$=".GIF"]').lightBox();
     
        });
(function($){$.fn.lightBox=function(settings){settings=jQuery.extend({overlayBgColor:'#12141F',overlayOpacity:0.8,fixedNavigation:true,imageLoading:'i/bg/lightbox-ico-loading.gif',imageBtnPrev:'',imageBtnNext:'',imageBtnClose:'i/bg/lightbox-btn-close.gif',imageBlank:'i/bg/lightbox-blank.gif',containerBorderSize:10,containerResizeSpeed:0,txtImage:'',txtOf:'/',keyToClose:'c',keyToPrev:'p',keyToNext:'n',imageArray:[],activeImage:0},settings);var jQueryMatchedObj=this;function _initialize(){_start(this,jQueryMatchedObj);return false;}
function _start(objClicked,jQueryMatchedObj){$('embed, object, select').css({'visibility':'hidden'});_set_interface();settings.imageArray.length=0;settings.activeImage=0;if(jQueryMatchedObj.length==1){settings.imageArray.push(new Array(objClicked.getAttribute('href'),objClicked.getAttribute('title')));}else{for(var i=0;i<jQueryMatchedObj.length;i++){settings.imageArray.push(new Array(jQueryMatchedObj[i].getAttribute('href'),jQueryMatchedObj[i].getAttribute('title')));}}
while(settings.imageArray[settings.activeImage][0]!=objClicked.getAttribute('href')){settings.activeImage++;}
_set_image_to_view();}
function _set_interface(){$('body').append('<div id="jquery-overlay"></div><div id="jquery-lightbox"><div class="lightbox"><div id="lightbox-container-image-data-box"><div id="lightbox-container-image-data"><div id="lightbox-image-details"><span id="lightbox-image-details-caption" class="h1"></span></div></div></div><div id="lightbox-container-image-box"><div id="lightbox-container-image" class="cont_light"><img id="lightbox-image"><div style="" id="lightbox-nav"><a href="#" id="lightbox-nav-btnPrev"></a><a href="#" id="lightbox-nav-btnNext"></a></div><div id="lightbox-loading"><a href="#" id="lightbox-loading-link"><img src="'+settings.imageLoading+'"></a></div><div id="lightbox-secNav"><a href="#" id="lightbox-secNav-btnClose">Закрыть</a></div></div></div></div></div>');var arrPageSizes=___getPageSize();$('#jquery-overlay').css({backgroundColor:settings.overlayBgColor,opacity:settings.overlayOpacity,width:arrPageSizes[0],height:arrPageSizes[1]}).fadeIn();var arrPageScroll=___getPageScroll();$('#jquery-lightbox').css({top:arrPageScroll[1]+(arrPageSizes[3]/10),left:arrPageScroll[0]}).show();$('#jquery-overlay,#jquery-lightbox').click(function(){_finish();});$('#lightbox-loading-link,#lightbox-secNav-btnClose').click(function(){_finish();return false;});$(window).resize(function(){var arrPageSizes=___getPageSize();$('#jquery-overlay').css({width:arrPageSizes[0],height:arrPageSizes[1]});var arrPageScroll=___getPageScroll();$('#jquery-lightbox').css({top:arrPageScroll[1]+(arrPageSizes[3]/10),left:arrPageScroll[0]});});}
function _set_image_to_view(){$('#lightbox-loading').show();if(settings.fixedNavigation){$('#lightbox-image,#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').show();}else{$('#lightbox-image,#lightbox-nav,#lightbox-nav-btnPrev,#lightbox-nav-btnNext,#lightbox-container-image-data-box,#lightbox-image-details-currentNumber').hide();}
var objImagePreloader=new Image();objImagePreloader.onload=function(){$('#lightbox-image').attr('src',settings.imageArray[settings.activeImage][0]);_resize_container_image_box(objImagePreloader.width,objImagePreloader.height);objImagePreloader.onload=function(){};};objImagePreloader.src=settings.imageArray[settings.activeImage][0];};function _resize_container_image_box(intImageWidth,intImageHeight){var intCurrentWidth=$('#lightbox-container-image-box').width();var intCurrentHeight=$('#lightbox-container-image-box').height();var intWidth=(intImageWidth+(settings.containerBorderSize*2));var intHeight=(intImageHeight+(settings.containerBorderSize*2));var intDiffW=intCurrentWidth-intWidth;var intDiffH=intCurrentHeight-intHeight;$('#lightbox-container-image-box').animate({width:intWidth,height:intHeight},settings.containerResizeSpeed,function(){_show_image();});if((intDiffW==0)&&(intDiffH==0)){if($.browser.msie){___pause(250);}else{___pause(100);}}
$('#lightbox-container-image-data-box').css({width:intImageWidth});$('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({height:intImageHeight+(settings.containerBorderSize*2)});};function _show_image(){$('#lightbox-loading').hide();$('#lightbox-image').fadeIn(function(){_show_image_data();_set_navigation();});_preload_neighbor_images();};function _show_image_data(){$('#lightbox-container-image-data-box').slideDown('fast');$('#lightbox-image-details-caption').hide();if(settings.imageArray[settings.activeImage][1]){$('#lightbox-image-details-caption').html(settings.imageArray[settings.activeImage][1]).show();}
if(settings.imageArray.length>1){$('#lightbox-image-details-currentNumber').html(settings.txtImage+' '+(settings.activeImage+1)+' '+settings.txtOf+' '+settings.imageArray.length).show();}}
function _set_navigation(){$('#lightbox-nav').show();$('#lightbox-nav-btnPrev,#lightbox-nav-btnNext').css({'background':'transparent url('+settings.imageBlank+') no-repeat'});if(settings.activeImage!=0){if(settings.fixedNavigation){$('#lightbox-nav-btnPrev').css({'background':'url('+settings.imageBtnPrev+') 30px center no-repeat'}).unbind().bind('click',function(){settings.activeImage=settings.activeImage-1;_set_image_to_view();return false;});}else{$('#lightbox-nav-btnPrev').unbind().hover(function(){$(this).css({'background':'url('+settings.imageBtnPrev+') 25px center no-repeat'});},function(){$(this).css({'background':'transparent url('+settings.imageBlank+') no-repeat'});}).show().bind('click',function(){settings.activeImage=settings.activeImage-1;_set_image_to_view();return false;});}}
if(settings.activeImage!=(settings.imageArray.length-1)){if(settings.fixedNavigation){$('#lightbox-nav-btnNext').css({'background':'url('+settings.imageBtnNext+') center no-repeat'}).unbind().bind('click',function(){settings.activeImage=settings.activeImage+1;_set_image_to_view();return false;});}else{$('#lightbox-nav-btnNext').unbind().hover(function(){$(this).css({'background':'url('+settings.imageBtnNext+') 80px center no-repeat'});},function(){$(this).css({'background':'transparent url('+settings.imageBlank+') no-repeat'});}).show().bind('click',function(){settings.activeImage=settings.activeImage+1;_set_image_to_view();return false;});}}
_enable_keyboard_navigation();}
function _enable_keyboard_navigation(){$(document).keydown(function(objEvent){_keyboard_action(objEvent);});}
function _disable_keyboard_navigation(){$(document).unbind();}
function _keyboard_action(objEvent){if(objEvent==null){keycode=event.keyCode;escapeKey=27;}else{keycode=objEvent.keyCode;escapeKey=objEvent.DOM_VK_ESCAPE;}
key=String.fromCharCode(keycode).toLowerCase();if((key==settings.keyToClose)||(key=='x')||(keycode==escapeKey)){_finish();}
if((key==settings.keyToPrev)||(keycode==37)){if(settings.activeImage!=0){settings.activeImage=settings.activeImage-1;_set_image_to_view();_disable_keyboard_navigation();}}
if((key==settings.keyToNext)||(keycode==39)){if(settings.activeImage!=(settings.imageArray.length-1)){settings.activeImage=settings.activeImage+1;_set_image_to_view();_disable_keyboard_navigation();}}}
function _preload_neighbor_images(){if((settings.imageArray.length-1)>settings.activeImage){objNext=new Image();objNext.src=settings.imageArray[settings.activeImage+1][0];}
if(settings.activeImage>0){objPrev=new Image();objPrev.src=settings.imageArray[settings.activeImage-1][0];}}
function _finish(){$('#jquery-lightbox').remove();$('#jquery-overlay').fadeOut(function(){$('#jquery-overlay').remove();});$('embed, object, select').css({'visibility':'visible'});}
function ___getPageSize(){var xScroll,yScroll;if(window.innerHeight&&window.scrollMaxY){xScroll=window.innerWidth+window.scrollMaxX;yScroll=window.innerHeight+window.scrollMaxY;}else if(document.body.scrollHeight>document.body.offsetHeight){xScroll=document.body.scrollWidth;yScroll=document.body.scrollHeight;}else{xScroll=document.body.offsetWidth;yScroll=document.body.offsetHeight;}
var windowWidth,windowHeight;if(self.innerHeight){if(document.documentElement.clientWidth){windowWidth=document.documentElement.clientWidth;}else{windowWidth=self.innerWidth;}
windowHeight=self.innerHeight;}else if(document.documentElement&&document.documentElement.clientHeight){windowWidth=document.documentElement.clientWidth;windowHeight=document.documentElement.clientHeight;}else if(document.body){windowWidth=document.body.clientWidth;windowHeight=document.body.clientHeight;}
if(yScroll<windowHeight){pageHeight=windowHeight;}else{pageHeight=yScroll;}
if(xScroll<windowWidth){pageWidth=xScroll;}else{pageWidth=windowWidth;}
arrayPageSize=new Array(pageWidth,pageHeight,windowWidth,windowHeight);return arrayPageSize;};function ___getPageScroll(){var xScroll,yScroll;if(self.pageYOffset){yScroll=self.pageYOffset;xScroll=self.pageXOffset;}else if(document.documentElement&&document.documentElement.scrollTop){yScroll=document.documentElement.scrollTop;xScroll=document.documentElement.scrollLeft;}else if(document.body){yScroll=document.body.scrollTop;xScroll=document.body.scrollLeft;}
arrayPageScroll=new Array(xScroll,yScroll);return arrayPageScroll;};function ___pause(ms){var date=new Date();curDate=null;do{var curDate=new Date();}
while(curDate-date<ms);};return this.unbind('click').click(_initialize);};})(jQuery);
