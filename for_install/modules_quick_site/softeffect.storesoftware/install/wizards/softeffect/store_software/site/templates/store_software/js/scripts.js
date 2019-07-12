$(function() {
	// setup ul.tabs to work as tabs for each div directly under div.panes
	var cn=$('.opened').length;
	
	for(var i=0;i<cn;i++){
	
	if(!$("#"+i+" ul li").length){$("#"+i+"").attr('style','display:none')};

	};
	
	$("ul.tabs").tabs("div.panes > div", {
		effect: 'fade',
		tabs: 'li',
		fadeOutSpeed: 100
	});
	
	// izmenenie yakorya
	$('ul.tabs a').click(function () {
		window.location.hash=$(this).attr('href');
	});
	
	// otkryvaem vkladku iz yakorya
	if (window.location.hash!='' && window.location.hash!='#') {
		$('a[href='+window.location.hash+']').click();
	}
	
	// zaschita EMAIL ot spam botov
	$('.js_email').each (function () {
		js_email = $(this);
		js_str = js_email.attr('left_text')+'@'+js_email.attr('right_text')+'.'+js_email.attr('end_text');
		js_email.html(js_str);
		if (js_email.attr('href')!='') {
			js_email.attr('href', 'mailto:'+js_str);
		}
		js_email.removeAttr('left_text').removeAttr('right_text').removeAttr('end_text');
	});
	
	$('.do').each(function () {
		var classList = $(this).attr('class').toString().split(' ');
		var paramList = {
			wd: 'width',
			hg: 'height',
			pl: 'padding-left',
			pr: 'padding-right',
			pt: 'padding-top',
			pb: 'padding-bottom',
			mr: 'margin-right',
			ml: 'margin-left',
			mt: 'margin-top',
			mb: 'margin-bottom',
		}

		for(i=0; i<classList.length; i++) {
			for(var key in paramList) {
				if (classList[i].indexOf(key)+1) {
					tmpVal = classList[i].toString().split('-');
					$(this).css(paramList[tmpVal[0]], tmpVal[1]+'px');
				}
			}
		}
	});
	
	var triggers = $("a.modalInput").overlay({ 
	    expose: { 
	        color: '#111', 
	        loadSpeed: 200, 
	        opacity: 0.6 
	    }, 
	    closeOnClick: false 
	});
	
	Shadowbox.init({
	    handleOversize: 'resize',
	    modal: true,
	    slideshowDelay:0
	});
});

function basketOrderView() {
	$('#order-h1').css('display', 'block');
	$('.order_in_basket').css('display', 'block');
	destination = $('#order-h1').offset().top;
	if ($.browser.safari){
		$('body').animate( { scrollTop: destination }, 1100 );
	} else {
		$('html').animate( { scrollTop: destination }, 1100 );
	}
	return false;

}
