<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
jQuery(function(){		
<?foreach($arResult["~COUNTDOWN"] as $arItem):
	$COUNTDOWN_SALE_FROM = MakeTimeStamp($arItem["PROPERTIES"]["COUNTDOWN_SALE_FROM"]["VALUE"]);
	$COUNTDOWN_SALE_TO   = MakeTimeStamp($arItem["PROPERTIES"]["COUNTDOWN_SALE_TO"]["VALUE"]);
	
if($COUNTDOWN_SALE_FROM > 0 && $COUNTDOWN_SALE_TO > 0):
	$COUNTDOWN_SALE_FROM = date("F d, Y H:i:s", $COUNTDOWN_SALE_FROM);
	$COUNTDOWN_SALE_TO   = date("F d, Y H:i:s", $COUNTDOWN_SALE_TO);
?>
	(function(){
		var time = new Date().getTime(),
			countdownSaleFrom = new Date("<?=$COUNTDOWN_SALE_FROM?>").getTime(),
			countdownSaleTo   = new Date("<?=$COUNTDOWN_SALE_TO?>").getTime();		
		if(countdownSaleFrom < time && time < countdownSaleTo){
			$(".countdown-item.<?=$arItem["ID"]?>").countdownsl("<?=$COUNTDOWN_SALE_TO?>");	  		
			$(".countdown-container.<?=$arItem["ID"]?>").show();
		}
	})();	
<?endif?>
<?endforeach?>
});
</script>