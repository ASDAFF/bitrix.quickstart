<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="catalog">
<?
foreach($arResult["SECTIONS"] as $arSection):
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="catalog_title_main" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
		<h2><a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?><?if($arParams["COUNT_ELEMENTS"]):?>&nbsp;(<?=$arSection["ELEMENT_CNT"]?>)<?endif;?></a></h2>
	</div>
		<?
		$APPLICATION->IncludeComponent("bagmet:mobile.top", ".default", array(
			"IBLOCK_TYPE_ID" =>  $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ELEMENT_SORT_FIELD" => "RAND",
			"ELEMENT_SORT_ORDER" => "asc",
			"ELEMENT_COUNT" => "6",
		//	"FLAG_PROPERTY_CODE" => "SPECIALOFFER",
			"SECTION_ID" => $arSection['ID'],
			"OFFERS_LIMIT" => "5",
			"OFFERS_FIELD_CODE" => array(
				0 => "NAME",
				1 => "",
			),
			"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => "sort",
			"OFFERS_SORT_ORDER" => "asc",
			"ACTION_VARIABLE" => $arParams['ACTION_VARIABLE'],
			"PRODUCT_ID_VARIABLE" => /*"id_section".$arSection['ID'],*/$arParams['PRODUCT_ID_VARIABLE'],
			"PRODUCT_QUANTITY_VARIABLE" => "quantity",
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"SECTION_ID_VARIABLE" => "SECTION_ID",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "180",
			"CACHE_GROUPS" => "Y",
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => "N",
			"SHOW_PRICE_COUNT" => "1",
			"PRICE_VAT_INCLUDE" => "Y",
			//"CONVERT_CURRENCY" => "N",
			'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
			'CURRENCY_ID' => $arParams['CURRENCY_ID'],
			"OFFERS_CART_PROPERTIES" => $arParams['OFFERS_CART_PROPERTIES'],
			"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
			"DISPLAY_IMG_WIDTH" => "220",
			"DISPLAY_IMG_HEIGHT" => "260",
			"SHARPEN" => "2"
			),
			false
		);
		?>
<?endforeach;?>
</div>