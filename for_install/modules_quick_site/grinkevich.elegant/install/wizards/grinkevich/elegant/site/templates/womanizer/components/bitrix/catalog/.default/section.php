<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<div id="right-col">
						<? if (CModule::IncludeModule("iblock") && COption::GetOptionString("eshop", "catalogSmartFilter", "Y", SITE_ID)=="Y")
						{
						    $arFilter = array(
						        "ACTIVE" => "Y",
						        "GLOBAL_ACTIVE" => "Y",
						        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
						    );
						    if(strlen($arResult["VARIABLES"]["SECTION_CODE"])>0)
						    {
						        $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
						    }
						    elseif($arResult["VARIABLES"]["SECTION_ID"]>0)
						    {
						        $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
						    }

							$obCache = new CPHPCache;
							if($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
							{
								$arCurSection = $obCache->GetVars();
							}
							else
							{
								$arCurSection = array();
								$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID"));
								$dbRes = new CIBlockResult($dbRes);

								if(defined("BX_COMP_MANAGED_CACHE"))
								{
									global $CACHE_MANAGER;
									$CACHE_MANAGER->StartTagCache("/iblock/catalog");

									if ($arCurSection = $dbRes->GetNext())
									{
										$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
									}
									$CACHE_MANAGER->EndTagCache();
								}
								else
								{
									if(!$arCurSection = $dbRes->GetNext())
										$arCurSection = array();
								}

								$obCache->EndDataCache($arCurSection);
							}




							$dbSection = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ID" =>$arCurSection["ID"]), false, array("UF_BROWSER_TITLE", "UF_TITLE_H1", "UF_KEYWORDS", "UF_META_DESCRIPTION"));
							if ($arSection = $dbSection->GetNext())
							{
								$arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"] = $arSection["UF_BROWSER_TITLE"];
								$arResult["SECTION_USER_FIELDS"]["UF_TITLE_H1"] = $arSection["UF_TITLE_H1"];
								$arResult["SECTION_USER_FIELDS"]["UF_KEYWORDS"] = $arSection["UF_KEYWORDS"];
								$arResult["SECTION_USER_FIELDS"]["UF_META_DESCRIPTION"] = $arSection["UF_META_DESCRIPTION"];
							}

							if (!empty($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]))
								$APPLICATION->SetTitle($arResult["SECTION_USER_FIELDS"]["UF_BROWSER_TITLE"]);
							else
								$APPLICATION->SetTitle($arSection["NAME"]);

							$APPLICATION->SetPageProperty("keywords", $arResult["SECTION_USER_FIELDS"]["UF_KEYWORDS"] ? $arResult["SECTION_USER_FIELDS"]["UF_KEYWORDS"] : $arSection["NAME"]);
							$APPLICATION->SetPageProperty("description", $arResult["SECTION_USER_FIELDS"]["UF_META_DESCRIPTION"] ? $arResult["SECTION_USER_FIELDS"]["UF_META_DESCRIPTION"] : $arSection["NAME"]);

						}

						if (!isset($GLOBALS['arrFilter']['=PROPERTY_5']) && count($GLOBALS['arrFilter']) == 1)
						{
							foreach ($_REQUEST AS $key => $value)
							{
								if (strpos($key, 'arrFilter_5_') !== false)
								{
									$GLOBALS['arrFilter']['ID'] = 0;

									continue;
								}
							}
						}
						?>



			<? if (CModule::IncludeModule("iblock") && COption::GetOptionString("eshop", "catalogSmartFilter", "Y", SITE_ID)=="Y"): ?>

									<?$APPLICATION->IncludeComponent(
								        "bitrix:catalog.smart.filter",
										"index",
								        Array(
								            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
								            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
								            "SECTION_ID" => $arCurSection["ID"],
								            "FILTER_NAME" => "arrFilter",
								            "PRICE_CODE" => $arParams["PRICE_CODE"],
								            "CACHE_TYPE" => "A",
								            "CACHE_TIME" => "36000000",
								            "CACHE_NOTES" => "",
								            "CACHE_GROUPS" => "Y",
								            "SAVE_IN_SESSION" => "N"
								        ),
								        false
								    );?>
			<? endif; ?>

	</div>

					<div id="center-col">
						<div id="breadcrumbs">
							<? $APPLICATION->IncludeComponent("bitrix:breadcrumb", "", Array(), false); ?>
							&rarr; <strong><?=$arSection['NAME'];?></strong>
						</div>





<?  // Elements sort
$arAvailableSort = array(
	"name" => Array("name", "asc"),
	"price" => Array('catalog_PRICE_1', "asc"),
	"date" => Array('PROPERTY_NEWPRODUCT', "desc"),
);

$sort = array_key_exists("sort", $_REQUEST) && array_key_exists(ToLower($_REQUEST["sort"]), $arAvailableSort) ? $arAvailableSort[ToLower($_REQUEST["sort"])][0] : "name";
$sort_order = array_key_exists("order", $_REQUEST) && in_array(ToLower($_REQUEST["order"]), Array("asc", "desc")) ? ToLower($_REQUEST["order"]) : $arAvailableSort[$sort][1];

$arAvailablePager = array("20", "50", "100");
$show_no = array_search($_REQUEST["show_no"], $arAvailablePager) ? $_REQUEST["show_no"] : $arAvailablePager[0];
?>





<?$current_view = COption::GetOptionString("eshop", "catalogView", "list", SITE_ID);?>





<?
	$res = $APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"catalog",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],//$arParams["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],//$arParams["ELEMENT_SORT_ORDER"],
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"CACHE_TYPE" => 'N',
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
		"PAGE_ELEMENT_COUNT" => $show_no,
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],

		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],

		"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
		"COMPARE_NAME" => $arParams["COMPARE_NAME"],
		"DISPLAY_IMG_WIDTH"	 =>	$arParams["DISPLAY_IMG_WIDTH"],
		"DISPLAY_IMG_HEIGHT" =>	$arParams["DISPLAY_IMG_HEIGHT"],

		"SHARPEN" => $arParams["SHARPEN"],
	),
	$component
);




?>







<?
if (CModule::IncludeModule("sale")){
	$arBasketItems = array();

	$dbBasketItems = CSaleBasket::GetList(
	        array(
	                "ID" => "ASC"
	            ),
	        array(
	                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"ORDER_ID" => "NULL"
	            ),
	        false,
	        false,
	        array("ID", "MODULE",
	              "PRODUCT_ID", "QUANTITY", "DELAY",
	              "CAN_BUY", "PRICE", "WEIGHT")
	    );


	while ($arItems = $dbBasketItems->Fetch())
	{
	    $arBasketItems[] = $arItems;
	}

	if( sizeof($arBasketItems) > 0 ){
		?>
			<script>
				$(document).ready(function(){
					<?

						foreach($arBasketItems as $value){
						    if($value["DELAY"] == "N")  {
							?>
							$("#prod<?=$value[PRODUCT_ID]?>").addClass("itemcart");
							<?
						    }

						}
					?>
				});
			</script>
		<?
	}
}

?>

