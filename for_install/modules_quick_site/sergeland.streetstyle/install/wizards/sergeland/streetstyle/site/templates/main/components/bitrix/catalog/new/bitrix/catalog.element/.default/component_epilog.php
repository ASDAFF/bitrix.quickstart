<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();	
	global $arReccomendElementFilter; $arReccomendElementFilter = array(); 
	$arReccomendElementFilter["ID"] = $arResult["~RECOMMEND"]["ID"];
	global $MESS; @include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lang/".LANGUAGE_ID."/template.php");
?>
<div class="catalog-section-tab">
	<div class="drop-shadow">
		<div class="detail-content">
			<ul class="tabs-prop">
				<li class="active"><?=GetMessage("SERGELAND_STREETSTYLE_TAB_PROPERTY")?></li>
				<?if($arParams["USE_STORE"]=="Y"):?>
					<li><?=$arParams["MAIN_TITLE"]?></li>
				<?endif?>
				<?if($arParams["USE_REVIEW"]=="Y"):?>
					<li><?=GetMessage("SERGELAND_STREETSTYLE_TAB_REVIEWS")?></li>
				<?endif?>
			</ul>
			<div class="tabs-border"></div>
			<ul class="tab-content">
				<li class="active">
					<ul class="property">
						<li>
							<h4><?=$arResult["PROPERTIES"]["BREND"]["NAME"]?></h4>
							<span><i><?=$arResult["PROPERTIES"]["BREND"]["VALUE"]?></i></span>
						</li>
						<li>
							<h4><?=$arResult["PROPERTIES"]["COLLECTION"]["NAME"]?></h4>
							<span><i><?=$arResult["PROPERTIES"]["COLLECTION"]["VALUE"]?></i></span>
						</li>
						<li>
							<h4><?=$arResult["PROPERTIES"]["COUNTRY"]["NAME"]?></h4>
							<span><i><?=$arResult["PROPERTIES"]["COUNTRY"]["VALUE"]?></i></span>
						</li>						
					</ul>
				</li>
				<?if($arParams["USE_STORE"] == "Y" && IsModuleInstalled("catalog") && $arResult["ID"]):?>
				<li>	
					<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", ".default", array(
								"CACHE_TYPE" => $arParams["CACHE_TYPE"],
								"CACHE_TIME" => $arParams["CACHE_TIME"],
								"PER_PAGE" => "10",
								"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
								"SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
								"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
								"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
								"ELEMENT_ID" => $arResult["ID"],
								"STORE_PATH"  =>  $arParams["STORE_PATH"],
								"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
							)
					);?>
				</li>		
				<?endif?>
				<?if($arParams["USE_REVIEW"]=="Y" && IsModuleInstalled("forum") && $arResult["ID"]):?>
				<li>
					<br>
					<?$APPLICATION->IncludeComponent("bitrix:forum.topic.reviews",".default", array(
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"SHOW_AVATAR" => "N",
							"MESSAGES_PER_PAGE" => $arParams["MESSAGES_PER_PAGE"],
							"USE_CAPTCHA" => $arParams["USE_CAPTCHA"],
							"PATH_TO_SMILE" => $arParams["PATH_TO_SMILE"],
							"FORUM_ID" => $arParams["FORUM_ID"],
							"URL_TEMPLATES_READ" => $arParams["URL_TEMPLATES_READ"],
							"SHOW_LINK_TO_FORUM" => $arParams["SHOW_LINK_TO_FORUM"],
							"ELEMENT_ID" => $arResult["ID"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"AJAX_POST" => $arParams["REVIEW_AJAX_POST"],
							"POST_FIRST_MESSAGE" => $arParams["POST_FIRST_MESSAGE"],
							"URL_TEMPLATES_DETAIL" => $arParams["URL_TEMPLATES_DETAIL"],
						)
					);?>
				</li>	
				<?endif?>				
			</ul>
		</div>
	</div>
</div>
<?if(is_array($arResult["SECTION"])):?>
	<br><a href="<?=$arResult["SECTION"]["SECTION_PAGE_URL"]?>"><?=GetMessage("CATALOG_BACK")?></a>
<?endif?>
<br><br>
<?if(!empty($arResult["~RECOMMEND"]["ID"])):?>
	<h3><?=$arResult["~RECOMMEND"]["TITLE"]?>:</h3>
	<?$APPLICATION->IncludeComponent("bitrix:catalog.top", "", array(
		"PROPERTY_CODE" => $arParams["PROPERTY_CODE"],		
		"DETAIL_URL" => $arParams["DETAIL_URL"],
		"BASKET_URL" => $arParams["BASKET_URL"],		
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],		
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],		
		"DISPLAY_COMPARE" => "N",
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],		
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"FILTER_NAME" => "arReccomendElementFilter",
		"CACHE_FILTER" => "Y",
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		
		//Template parameters
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
		"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
		"PRODUCT_PROPERTIES" => $arParams["LIST_PROPERTY_CODE"],
		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],		
		)
	);?>
<?endif?>
<script>
jQuery(function(){		
<?foreach($arResult["~COUNTDOWN"] as $arItem):
	$COUNTDOWN_SALE_FROM = MakeTimeStamp($arItem["PROPERTIES"]["COUNTDOWN_SALE_FROM"]["VALUE"]);
	$COUNTDOWN_SALE_TO   = MakeTimeStamp($arItem["PROPERTIES"]["COUNTDOWN_SALE_TO"]["VALUE"]);
	
if($COUNTDOWN_SALE_FROM > 0 && $COUNTDOWN_SALE_TO > 0):
	$COUNTDOWN_SALE_FROM = date("F d, Y H:i:s", $COUNTDOWN_SALE_FROM);
	$COUNTDOWN_SALE_TO = date("F d, Y H:i:s", $COUNTDOWN_SALE_TO);
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