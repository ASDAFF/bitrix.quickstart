function RevealWindowLinkClick(Sender) {
	var modalLocation = $(Sender).attr('data-reveal-id');
	modalLocation = $('#'+modalLocation);
	modalLocation.WD_Reveal($(Sender).data());
	var CallbackOpen = $(Sender).attr('data-callback-open');
	if (WD_FunctionExists(CallbackOpen) && modalLocation.attr('data-callback-open-done')!='Y') {
		window[CallbackOpen](modalLocation, modalLocation.find('.wd_popup_content'), $(Sender));
		modalLocation.attr('data-callback-open-done','Y');
	}
	var CallbackShow = $(Sender).attr('data-callback-show');
	if (WD_FunctionExists(CallbackShow)) {
		window[CallbackShow](modalLocation, modalLocation.find('.wd_popup_content'), $(Sender));
	}
}

function WD_Popups_Init() {
	if (window.WD_Popup_LinkTo!=undefined) {
		for(var i in window.WD_Popup_LinkTo) {
			if (!window.WD_Popup_LinkTo.hasOwnProperty(i)) continue;
			for(var j in window.WD_Popup_LinkTo[i]) {
				$(i).attr(j,window.WD_Popup_LinkTo[i][j]);
			}
		}
	}
	var DefaultWidth = 300;
	WD_Popups_Init_Plugin();
	$('[data-reveal-id]').each(function() {
		var modalLocation = $(this).attr('data-reveal-id');
		modalLocation = $('#'+modalLocation);
		if ($(this).attr('data-reveal-tobody')=='Y') {
			$('body').eq(0).append(modalLocation);
		}
		var ModalWidth = parseInt(modalLocation.width());
		if (ModalWidth<0 || isNaN(ModalWidth)) ModalWidth = DefaultWidth;
		var ModalMarginLeft = parseInt(ModalWidth/2);
		if (ModalMarginLeft<0 || isNaN(ModalMarginLeft)) ModalMarginLeft = parseInt(DefaultWidth/2);
		ModalMarginLeft = -1 * ModalMarginLeft;
		modalLocation.css({'width':ModalWidth,'margin-left':ModalMarginLeft});
		var CallbackInit = modalLocation.attr('data-callback-init');
		if (WD_FunctionExists(CallbackInit)) {
			window[CallbackInit](modalLocation, modalLocation.find('.wd_popup_content'), $(this));
		}
	});
	if (jQuery.fn.on) {
		$('[data-reveal-id]').on('click', function(Event){
			Event.preventDefault();
			RevealWindowLinkClick(this);
		});
	} else if (jQuery.fn.live) {
		$('[data-reveal-id]').live('click', function(Event){
			Event.preventDefault();
			RevealWindowLinkClick(this);
		});
	} else {
		$('[data-reveal-id]').bind('click', function(Event){
			Event.preventDefault();
			RevealWindowLinkClick(this);
		});
	}
	$(window).load(function(){
		$('[data-reveal-id][data-autoopen=Y]').each(function(){
			var Link = $(this);
			var Delay = parseInt(Link.data('autoopen-delay'));
			if (isNaN(Delay)) Delay = 500;
			setTimeout(function(){
				Link.click();
			},Delay);
		});
	});
}

function WD_Popup_AJAX(Container, URL, FormData, Callback) {
	if (FormData==undefined) FormData = '';
	$.ajax({
		url: URL,
		type: 'POST',
		data: FormData,
		success: function(res) {
			if (typeof Callback == 'function') {
				Callback(res, Container, URL, FormData);
			}
		}
	});
}

function WD_Popup_GetContentObject(ID) {
	return $('#popup_'+ID+'_window .wd_popup_content');
}

function WD_Popup_Close(ID) {
	if (ID===false) {
		$('.wd_popup_window .wd_popup_close').click();
	} else {
		$('#popup_'+ID+'_window .wd_popup_close').click();
	}
}

function WD_Popup_Open(ID) {
	if (ID!==false && $('#wd_func_opener_'+ID).size()>0) {
		$('#wd_func_opener_'+ID).eq(0).click();
	}
}

function WD_FunctionExists(function_name) {
	if (typeof function_name == 'string'){
		return (typeof window[function_name] == 'function');
	} else{
		return (function_name instanceof Function);
	}
}

function WD_OnReady(callback){
	var addListener = document.addEventListener || document.attachEvent,
			removeListener = document.removeEventListener || document.detachEvent,
			eventName = document.addEventListener ? "DOMContentLoaded" : "onreadystatechange";
	addListener.call(document, eventName, function(){
		if (document.removeEventListener) {
			document.removeEventListener(eventName,arguments.callee,false);
		} else if (document.detachEvent) {
			document.detachEvent(eventName,arguments.callee,false);
		}
		callback();
	}, false);
}

WD_OnReady(function(){
	if(!window.jQuery) {
		 var script = document.createElement('script');
		 script.type = "text/javascript";
		 script.src = "/bitrix/js/webdebug.popup/jquery.1.8.3.min.js";
		 document.getElementsByTagName('head')[0].appendChild(script);
	}
	if ('jQuery' in window) {
		WD_Popups_Init();
	} else {
		var t = setInterval(function() {
			if ('jQuery' in window) {
				WD_Popups_Init();
				clearInterval(t); 
			}
		}, 50);
	}
});

function WD_Popups_Init_Plugin() {
	$.fn.WD_Reveal = function(options) {
		var defaults = {  
			animation: 'fadeAndPop',
			animationspeed: 150,
			closeonbackgroundclick: true,
			close: '',
			noClose: '',
			overlay: '',
			overlayOpacity: 0.6,
			callbackClose:null
		};
		var options = $.extend({}, defaults, options);
		if (options.noClose=='Y') {
			options.closeonbackgroundclick = false;
		}
		return this.each(function() {
			if ($.trim(options.overlay)=='') options.overlay = 'wd_popup_overlay_'+Math.round(Math.random*10000000);
			var modal = $(this),
					topMeasure  = parseInt(modal.css('top')),
					topOffset = modal.height() + topMeasure,
					locked = false,
					modalBG = $('#'+options.overlay);
			if(modalBG.length == 0) {
				modalBG = $('<div id="'+options.overlay+'" class="wd_popup_overlay"></div>').insertAfter(modal);
			}
			modal.bind('reveal:open', function () {
				modalBG.unbind('click.modalEvent');
				$('#' + options.close).unbind('click.modalEvent');
				if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modal.css({'display':'block','top': $(document).scrollTop()-topOffset, 'opacity' : 0, 'visibility' : 'visible'});
						modalBG.fadeTo(options.animationspeed/2, options.overlayOpacity);
						modal.delay(options.animationspeed/2).animate({
							"top": $(document).scrollTop()+topMeasure + 'px',
							"opacity" : 1
						}, options.animationspeed,unlockModal());			
					}
					if(options.animation == "fade") {
						modal.css({'display':'block','opacity' : 0, 'visibility' : 'visible', 'top': $(document).scrollTop()+topMeasure});
						modalBG.fadeTo(options.animationspeed/2, options.overlayOpacity);
						modal.delay(options.animationspeed/2).animate({
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					} 
					if(options.animation == "none") {
						modal.css({'display':'block','visibility' : 'visible', 'top':$(document).scrollTop()+topMeasure});
						modalBG.css({"display":"block","opacity":options.overlayOpacity});	
						unlockModal();
					}
				}
				modal.unbind('reveal:open');
			});
			modal.bind('reveal:close', function () {
				var CanClose=true;
				if (WD_FunctionExists(options.callbackClose)) {
					if (window[options.callbackClose](modal, modal.find('.wd_popup_content').eq(0))===false) {
						CanClose = false;
					}
				}
				if (CanClose) {
					if(!locked) {
						lockModal();
						if(options.animation == "fadeAndPop") {
							modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
							modal.animate({
								"top":  $(document).scrollTop()-topOffset + 'px',
								"opacity" : 0
							}, options.animationspeed/2, function() {
								modal.css({'display':'none','top':topMeasure, 'opacity' : 1, 'visibility' : 'hidden'});
								if (modal.data('display')=='none') modal.html(modal.html());
								unlockModal();
							});
						}
						if(options.animation == "fade") {
							modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
							modal.animate({
								"opacity" : 0
							}, options.animationspeed, function() {
								modal.css({'display':'none','opacity' : 1, 'visibility' : 'hidden', 'top' : topMeasure});
								if (modal.data('display')=='none') modal.html(modal.html());
								unlockModal();
							});
						}  	
						if(options.animation == "none") {
							modal.css({'display':'none','visibility' : 'hidden', 'top' : topMeasure});
							modalBG.css({'display' : 'none'});
							if (modal.data('display')=='none') modal.html(modal.html());
						}
					}
					modal.unbind('reveal:close');
				}
			});
			modal.trigger('reveal:open');
			var closeButton = $('#' + options.close).bind('click.modalEvent', function () {
				modal.trigger('reveal:close');
			});
			if(options.closeonbackgroundclick) {
				modalBG.css({"cursor":"pointer"})
				modalBG.bind('click.modalEvent', function () {
					modal.trigger('reveal:close')
				});
			}
			if (options.noClose!='Y') {
				$('body').keyup(function(e) {
					if(e.which===27){modal.trigger('reveal:close');}
				});
			}
			function unlockModal() { 
				locked = false;
			}
			function lockModal() {
				locked = true;
			}
		});
	}
}