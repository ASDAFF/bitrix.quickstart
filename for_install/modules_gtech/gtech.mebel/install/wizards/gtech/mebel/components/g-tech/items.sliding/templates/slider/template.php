<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script src="<?=$templateFolder?>/js/slides.min.jquery.js"></script>

<script>
$(document).ready(function() {
	$("#btn_discounts").addClass("btn_discounts"); $("#btn_hits").addClass("btn_hits"); $("#btn_news").addClass("btn_news");
	$("#btn_discounts").addClass("slider_selected_btn");
    $("#btn_discounts").click(function(){
		$("div#slide_discounts").show(); $("div#slide_hits").hide(); $("div#slide_news").hide();
		$("div.btn_hits").removeClass("slider_selected_btn"); $("div.btn_news").removeClass("slider_selected_btn");
		$(this).addClass("slider_selected_btn");
	});
    $("#btn_hits").click(function(){
		$("div#slide_hits").show(); $("div#slide_discounts").hide(); $("div#slide_news").hide();
		$("div.btn_discounts").removeClass("slider_selected_btn"); $("div.btn_news").removeClass("slider_selected_btn");
		$(this).addClass("slider_selected_btn");
	})
    $("#btn_news").click(function(){
		$("div#slide_news").show(); $("div#slide_discounts").hide(); $("div#slide_hits").hide();
		$("div.btn_hits").removeClass("slider_selected_btn"); $("div.btn_discounts").removeClass("slider_selected_btn");
		$(this).addClass("slider_selected_btn");
	})	
$(function(){
	$('#slides1').slides({
 		preload: true,
 		preloadImage: 'img/loading.gif',
 		play: <?=$arParams["SLIDE_SPEED"]*1000?>,
 		pause: 2000,
 		hoverPause: true,
 		animationStart: function(current){
 			$('.caption').animate({
 				bottom:-35
 			},100);
 			if (window.console && console.log) {
 				// example return of current slide number
 				console.log('animationStart on slide: ', current);
 			};
 		},
 		
 	});
});
$(function(){
	$('#slides2').slides({
 		preload: true,
 		preloadImage: 'img/loading.gif',
 		play: <?=$arParams["SLIDE_SPEED"]*1000?>,
 		pause: 2000,
 		hoverPause: true,
 		animationStart: function(current){
 			$('.caption').animate({
 				bottom:-35
 			},100);
 			if (window.console && console.log) {
 				// example return of current slide number
 				console.log('animationStart on slide: ', current);
 			};
 		},
 		
 	});
});
$(function(){
	$('#slides3').slides({
 		preload: true,
 		preloadImage: 'img/loading.gif',
 		play: <?=$arParams["SLIDE_SPEED"]*1000?>,
 		pause: 2000,
 		hoverPause: true,
 		animationStart: function(current){
 			$('.caption').animate({
 				bottom:-35
 			},100);
 			if (window.console && console.log) {
 				// example return of current slide number
 				console.log('animationStart on slide: ', current);
 			};
 		},
 		
 	});
});
});
</script>

<div id="slide_discounts">
	<div id="slides1">
 		<div class="slides_container">
	 		<?foreach($arResult["ITEMS"]["DISCOUNT"] as $Item){?>
	 			<div class="slide">
					<table cellpadding="0" cellspacing="0" border="0" width="870px" height="236px" align="center">
						<tr>
							<td rowspan="2" align="center" valign="middle" width="330px">
								<a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slide_picture"><img src="<?=$Item["DETAIL_PICTURE"]?>" title="<?=$Item["NAME"]?>" alt="<?=$Item["NAME"]?>"></a>
							</td>
							<td height="80px" valign="bottom">
								<h1><a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slideTitle"><?=$Item["NAME"]?></a></h1>
							</td>
						</tr>
						<tr>
							<td valign="top" class="slidePrice">
								<?if($Item["PRICE"]["DISCOUNT_PRICE"]<$Item["PRICE"]["PRICE"]){?>
									<?=$Item["PRICE"]["DISCOUNT_PRICE"]?> <span class="rouble">c</span> (<s><?=$Item["PRICE"]["PRICE"]?> <span class="rouble">c</span></s>)
								<?}else{?><?=$Item["PRICE"]["PRICE"]?> <span class="rouble">c</span><?}?>
							</td>
						</tr>
					</table>
					<a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slide_detail"><?=GetMessage("SLIDER_DETAILLINK");?></a>
					<div class="slide_buy_item"><a href="<?=$Item["DETAIL_PAGE_URL"]?>?action=BUY&id=<?=$Item['ID']?>"><img src="<?=$templateFolder?>/images/buy-button.jpg" border="0"></a></div>
				</div>
	 		<?}?>                 					
 		</div>
 		<a href="#" class="prev"><img src="<?=$templateFolder?>/images/but-left.png" alt="Prev"></a>
 		<a href="#" class="next"><img src="<?=$templateFolder?>/images/but-right.png" alt="Next"></a>
	</div>
</div>
<div id="slide_hits">
	<div id="slides2">
	 	<div class="slides_container">
	 		<?foreach($arResult["ITEMS"]["HITS"] as $Item){?>
	 			<div class="slide">
					<table cellpadding="0" cellspacing="0" border="0" width="870px" height="236px" align="center">
						<tr>
							<td rowspan="2" align="center" valign="middle" width="330px">
								<a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slide_picture"><img src="<?=$Item["DETAIL_PICTURE"]?>" title="<?=$Item["NAME"]?>" alt="<?=$Item["NAME"]?>"></a>
							</td>
							<td height="80px" valign="bottom">
								<h1><a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slideTitle"><?=$Item["NAME"]?></a></h1>
							</td>
						</tr>
						<tr>
							<td valign="top" class="slidePrice">
								<?if($Item["PRICE"]["DISCOUNT_PRICE"]<$Item["PRICE"]["PRICE"]){?>
									<?=$Item["PRICE"]["DISCOUNT_PRICE"]?> <span class="rouble">c</span> (<s><?=$Item["PRICE"]["PRICE"]?> <span class="rouble">c</span></s>)
								<?}else{?><?=$Item["PRICE"]["PRICE"]?> <span class="rouble">c</span><?}?>
							</td>
						</tr>
					</table>
					<a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slide_detail"><?=GetMessage("SLIDER_DETAILLINK");?></a>
					<div class="slide_buy_item"><a href="<?=$Item["DETAIL_PAGE_URL"]?>?action=BUY&id=<?=$Item['ID']?>"><img src="<?=$templateFolder?>/images/buy-button.jpg" border="0"></a></div>
				</div>
	 		<?}?>                 					
 		</div>
 		<a href="#" class="prev"><img src="<?=$templateFolder?>/images/but-left.png" alt="Prev"></a>
 		<a href="#" class="next"><img src="<?=$templateFolder?>/images/but-right.png" alt="Next"></a>
	</div>
</div>
<div id="slide_news">
	<div id="slides3">
 		<div class="slides_container">
	 		<?foreach($arResult["ITEMS"]["NEWS"] as $Item){?>
 				<div class="slide">
					<table cellpadding="0" cellspacing="0" border="0" width="870px" height="236px" align="center">
						<tr>
							<td rowspan="2" align="center" valign="middle" width="330px">
								<a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slide_picture"><img src="<?=$Item["DETAIL_PICTURE"]?>" title="<?=$Item["NAME"]?>" alt="<?=$Item["NAME"]?>"></a>
							</td>
							<td height="80px" valign="bottom">
								<h1><a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slideTitle"><?=$Item["NAME"]?></a></h1>
							</td>
						</tr>
						<tr>
							<td valign="top" class="slidePrice">
								<?if($Item["PRICE"]["DISCOUNT_PRICE"]<$Item["PRICE"]["PRICE"]){?>
									<?=$Item["PRICE"]["DISCOUNT_PRICE"]?> <span class="rouble">c</span> (<s><?=$Item["PRICE"]["PRICE"]?> <span class="rouble">c</span></s>)
								<?}else{?><?=$Item["PRICE"]["PRICE"]?> <span class="rouble">c</span><?}?>
							</td>
						</tr>
					</table>
					<a href="<?=$Item["DETAIL_PAGE_URL"]?>" title="<?=$Item["NAME"]?>" class="slide_detail"><?=GetMessage("SLIDER_DETAILLINK");?></a>
					<div class="slide_buy_item"><a href="<?=$Item["DETAIL_PAGE_URL"]?>?action=BUY&id=<?=$Item['ID']?>"><img src="<?=$templateFolder?>/images/buy-button.jpg" border="0"></a></div>
				</div>
	 		<?}?>                 					
 		</div>
 		<a href="#" class="prev"><img src="<?=$templateFolder?>/images/but-left.png" alt="Prev"></a>
 		<a href="#" class="next"><img src="<?=$templateFolder?>/images/but-right.png" alt="Next"></a>
	</div>
</div>

<div id="btn_discounts"><div class="change_btn_left"></div><div class="change_btn_right"></div> <?=GetMessage("SLIDER_DISCOUNTS_TITLE");?> </div>
<div id="btn_hits"><div class="change_btn_left"></div><div class="change_btn_right"></div> <?=GetMessage("SLIDER_HITS_TITLE");?> </div>
<div id="btn_news"><div class="change_btn_left"></div><div class="change_btn_right"></div> <?=GetMessage("SLIDER_NEWS_TITLE");?> </div>