/**
 * Project:			Top horizontal navigation JS
 * File:			Hnav.js
 * Last Refactoring:
 * @version:	 	12.9.28.1155
 * @copyright:		 Bitrix Developpers 2012. All Rights Reserved.
 * @link 			http://1c-bitrix.ru
 */
 $(function(){
 	$(".allcatnav").live('click', function() {
		var fullmenu = $("#fullmenu ul").height();
		if($(this).hasClass('active')){
			$("#fullmenu").animate({height:0}, 400);
			$("#fullmenu").css({'box-shadow':'none'});
			$(this).removeClass('active');
			$("#fullmenu").removeClass('active');
		} else{
			$(this).addClass('active');
			$("#fullmenu").addClass('active');
			var offsethbn = $(".header-brandzone-nav").offset()
			$("#fullmenu").css({"right":offsethbn.left+"px","top":offsethbn.top+58+"px"})
			$("#fullmenu").css({display:'block',height:0,'box-shadow':'0 7px 12px 2px rgba(0,0,0,0.3), inset 0 5px 6px -3px rgba(0,0,0,0.3)'});
			$("#fullmenu").animate({height:fullmenu+"px"}, 400);
		} 

		return false;
	});
	$(".root-item").mouseover(function() {
	
		if($(".allcatnav").hasClass('active')){
			$("#fullmenu").animate({height:0}, 400);
			$("#fullmenu").css({'box-shadow':'none'});
			$(".allcatnav").removeClass('active');
			$("#fullmenu").removeClass('active');
			return false;
		}
	});
 });
function setWidthTopNav(){
	
	var tm = $('#top-menu').find('.root-item');
	var tmSum = tmLastW = 0;
	var tmMaxW = $("#top-menu-layout").width();
	
	for(var i = 0; tmSum < tmMaxW; i++) {
		tmLastW = $(tm[i]).width();
		tmSum = tmSum + tmLastW;
		
		if(i > tm.length) {break};
	}
	count = i;
	tmSumO = tmSum - tmLastW;	
	if (tmSum > tmMaxW){
		onAllButton();
		removeLink(count);
	} else {
		offAllButton();
	}
}
function onAllButton(){
	
	$(".allcatnav").css({display:'block',opacity:0});
	$(".allcatnav").animate({opacity:1}, 500);
}
function offAllButton(){
	
	$(".allcatnav").css({display:'block',opacity:1});
	$(".allcatnav").animate({opacity:0}, 500);
	$(".allcatnav").css({display:'none'});
	
}
function saveCookie(){
	
	$.cookie("tmAllcat", "1",{expires: 60});
	$.cookie("tmCol", count,{expires: 60});
	$.cookie("tmWidth", tmSumO,{expires: 60});
	
}
function removeLink(){
	
	for(var i = 0; i < count; i++) {
		$('#fullmenu > ul > li:nth-child('+i+')').css({display:"none"})
	}
	
}

$(document).ready(function() {

	if($.cookie("tmAllcat") == "1"){ 
	
		onAllButton();
		setWidthTopNav();

		tmSumC = $.cookie("tmWidth");
		if (tmSumO == tmSumC){
			removeLink(count);
		} else {
			saveCookie(count, tmSumO);
			removeLink(count);
		} 
	} else {
		setWidthTopNav();
		saveCookie(count, tmSumO);

	} 
});