$(document).ready(function(){
		$(".mlfShap").css({'opacity': 0,});
		$(".mlfContent").css({'opacity': 0,});
		$(".skidka").css({'opacity': 0,'left':'-200px','position': 'relative'});
		$(".mlfContent .share").css({'opacity': 0,'margin-top': '-200px'});
		$(".mlfContent .text").css({'margin-top': '480px','opacity':0});
		
		setTimeout(function(){
			$(".mlfContent .text").animate({
				'margin-top': '0px',
				'opacity': 1,
			},1000);
		},1000);
		
		$(".mlfContent").animate({
			'opacity': '1',
		},1500);
		
		setTimeout(function(){
			$('.mlfShap').animate({
				'opacity': '1',
			},1500);;
		},1000);
		setTimeout(function(){
			$('.mlfContent .share').animate({
				'opacity': '1',
				'margin-top': '40px'
			},1000);;
		},1500);
		setTimeout(function(){
			$('.skidka').animate({
				'opacity': '1',
				'left': '0px'
			},1000);
		},2000);
		setInterval(function(){
			$('.skidka').animate({
				'opacity': '0.8'
			},500);
			setTimeout(function(){
			$('.skidka').animate({
				'opacity': '1'
			},500);
			},500);
		},4000);
		
});

$(function() {
 $('input, textarea').placeholder();
});

