<?$mainFilter = Array("!PROPERTY_main"=>false);
$el = CIBlockElement::GetList(Array(),Array("IBLOCK_CODE"=>"catalog","SITE_ID"=>SITE_ID))->GetNext();
$APPLICATION->IncludeComponent("bitrix:catalog.section", "hotgoods", array(
	"IBLOCK_TYPE" => "iarga_shopplus100",
	"IBLOCK_ID" => $el['IBLOCK_ID'],
	"SECTION_ID" => "",
	"SECTION_CODE" => "",
	"SECTION_USER_FIELDS" => array(
		0 => "",
		1 => "",
	),
	"ELEMENT_SORT_FIELD" => "SORT",
	"ELEMENT_SORT_ORDER" => "asc",
	"FILTER_NAME" => "mainFilter",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SHOW_ALL_WO_SECTION" => "Y",
	"PAGE_ELEMENT_COUNT" => "4",
	"LINE_ELEMENT_COUNT" => "2",
	"PROPERTY_CODE" => array(
		0 => "",
		1 => "vars",
		2 => "",
	),
	"OFFERS_LIMIT" => "5",
	"SECTION_URL" => "",
	"DETAIL_URL" => "",
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"PRODUCT_QUANTITY_VARIABLE" => "quantity",
	"PRODUCT_PROPS_VARIABLE" => "prop",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "180",
	"CACHE_GROUPS" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"ADD_SECTIONS_CHAIN" => "N",
	"DISPLAY_COMPARE" => "N",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"CACHE_FILTER" => "N",
	"PRICE_CODE" => array(
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRODUCT_PROPERTIES" => array(
	),
	"USE_PRODUCT_QUANTITY" => "N",
	"CONVERT_CURRENCY" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Товары",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

<div class="wrapper">

	<div class="info-description">
		
		<ul class="column-wrap">
			
			<li class="box">
				<div class="narrow">
					<?$el = CIBlockElement::GetList(Array(),Array("IBLOCK_CODE"=>"information","CODE"=>"payment"),false,false,Array("NAME","DETAIL_PAGE_URL","PREVIEW_TEXT"))->GetNext();?>
					<h2><a href="<?=$el['DETAIL_PAGE_URL']?>"><img src="<?=$templateFolder?>/images/icon-payment.gif" alt=""><?=$el['NAME']?></a></h2>
					<p><?=$el['PREVIEW_TEXT']?></p>
				</div><!--.narrow-end-->
			<!--.box-end-->
			
			<li class="box">
				<div class="narrow">
					<?$el = CIBlockElement::GetList(Array(),Array("IBLOCK_CODE"=>"information","CODE"=>"delivery"),false,false,Array("NAME","DETAIL_PAGE_URL","PREVIEW_TEXT"))->GetNext();?>	
					<h2><a href="<?=$el['DETAIL_PAGE_URL']?>"><img src="<?=$templateFolder?>/images/icon-delivery.gif" alt=""><?=$el['NAME']?></a></h2>
					<p><?=$el['PREVIEW_TEXT']?></p>
				</div><!--.narrow-end-->
			<!--.box-end-->
			
		</ul><!--.column-wrap-end-->
		
	</div><!--.info-description-end-->

</div><!--.wrapper-end-->