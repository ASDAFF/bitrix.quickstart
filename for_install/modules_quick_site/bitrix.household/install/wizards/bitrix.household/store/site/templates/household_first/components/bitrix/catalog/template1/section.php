<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if($arParams["USE_COMPARE"]=="Y"):
	$APPLICATION->IncludeComponent(
		"bitrix:catalog.compare.list",
		"store",
		Array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"NAME" => $arParams["COMPARE_NAME"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		),
		$component
	);
endif;
?>



<!--noindex-->
<?if($arParams["USE_FILTER"]=="Y"):?>
<?$APPLICATION->IncludeComponent(
	"bitrix.household:store.catalog.filter",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FILTER_NAME" => '',
		"FIELD_CODE" => $arParams["FILTER_FIELD_CODE"],
 		"PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
		"PRICE_CODE" => $arParams["FILTER_PRICE_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "SEF_FOLDER" => $arParams["SEF_FOLDER"],
                "SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
	),
	$component
);
?>

<?endif?>

<?
$arAvailableSort = array(
	"price" => Array('catalog_PRICE_'.$arResult['_PRICE_ID'], "asc"),
	"brand" => Array('PROPERTY_PRODUSER.NAME', "desc"),
	"rating" => Array('PROPERTY_rating', "desc"),

);

$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "name";
$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];	

?>
<div class="filter">
	<div class="sort">
		<div class="array">
			<p class="e_act"><strong><?=GetMessage('SECT_SORT_LABEL')?>:</strong>&nbsp;&nbsp;&nbsp;
			<?$i=0;?>
			<?foreach ($arAvailableSort as $key => $val):
				$i++;
				$className = $sort == $val[0] ? 'active_down' : '';
				if ($className) 
					$className .= $sort_order == 'asc' ? ' asc' : ' desc';
				$newSort = $sort == $val[0] ? $sort_order == 'desc' ? 'asc' : 'desc' : $arAvailableSort[$key][1];
			?>

			<a href="<?=$APPLICATION->GetCurPageParam('sort='.$key.'&order='.$newSort, 	array('sort', 'order'))?>" class="<?=$className?>" rel="nofollow"><?=GetMessage('SECT_SORT_'.$key)?></a>
			<?if ($i!=count($arAvailableSort)) {?>&nbsp;&nbsp;|&nbsp;&nbsp;<?}?>
			
			<?endforeach;?>
			</p>
			<?
			  $link=str_replace("#IBLOCK_CODE#", $arResult["VARIABLES"]["IBLOCK_CODE"],$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"]);
			  $link=str_replace("#SECTION_CODE#", $arResult["VARIABLES"]["SECTION_CODE"],$link);
			 ?>
			<div class="comp_main"><?$APPLICATION->ShowProperty("CATALOG_COMPARE_LIST", "");?></div>
		</div>
		

		
<!--/noindex-->

<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $sort,//$arParams["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $sort_order,//$arParams["ELEMENT_SORT_ORDER"],
 		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
		"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],

		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		
		"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
		"COMPARE_NAME" => $arParams["COMPARE_NAME"],
		"ADD_SECTIONS_CHAIN" => "Y",
		'DISPLAY_AS_RATING'=>$arParams["DISPLAY_AS_RATING"],
		'ADD_PRODUSER_TO_TITLE'=>$arParams["ADD_PRODUSER_TO_TITLE"],
		'SHOW_FRACTION_PRICE'=>$arParams["SHOW_FRACTION_PRICE"]
	),
	$component
);
?>

