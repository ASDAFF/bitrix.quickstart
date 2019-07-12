<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
if($arParams["USE_COMPARE"]=="Y"):
	if(!$arResult["VARIABLES"]["SECTION_CODE"])
		$arResult["URL_TEMPLATES"]["compare"]=str_replace("#SECTION_CODE#","0",$arResult["URL_TEMPLATES"]["compare"]);
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
	
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"",
	Array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"TOP_DEPTH" => 2,
	),
	$component
);
?>
<?if(!$arResult["VARIABLES"]["SECTION_ID"] && !$arResult["VARIABLES"]["SECTION_CODE"]):
	$resElem=CIBlockElement::GetList(
		Array("SORT"=>"ASC"),
		Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "SECTION_ID"=>0),
		 false,
		 array("nTopCount"=>1),
		 Array("ID")
	);	
	if($arElem=$resElem->GetNext()):
		
		$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
		if(!is_array($arrFilter))
			$arrFilter = array();
		$arrFilter['SECTION_ID']=0;
		?>
			</td>
						</tr>
					</table>
				</td>
				<td class="pr_rc">
							</td>
			</tr>
	</table>
		<!--noindex-->
		<?if($arParams["USE_FILTER"]=="Y"):?>		
		<?$APPLICATION->IncludeComponent(
			"bitrix.household:store.catalog.filter2",
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
				"SET_TITLE" => "N",
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
				"DETAIL_URL" => str_replace("#SECTION_CODE#","0",$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"]),
				
				"COMPARE_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["compare"],
				"COMPARE_NAME" => $arParams["COMPARE_NAME"],
				"USE_FILTER" => $arParams['USE_FILTER'],
				"ADD_SECTIONS_CHAIN" => "Y",
				
				"SEF_FOLDER" => $arParams["SEF_FOLDER"],
				"SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
				"FIELD_CODE" => $arParams["FILTER_FIELD_CODE"],
		 		"FILTER_PROPERTY_CODE" => $arParams["FILTER_PROPERTY_CODE"],
				"FILTER_PRICE_CODE" => $arParams["FILTER_PRICE_CODE"],
				'DISPLAY_AS_RATING'=>$arParams["DISPLAY_AS_RATING"],
				'ADD_PRODUSER_TO_TITLE'=>$arParams["ADD_PRODUSER_TO_TITLE"],
				'SHOW_FRACTION_PRICE'=>$arParams["SHOW_FRACTION_PRICE"]
			),
			$component
		);
		?>
	<?else:
		$resElem=CIBlockSection::GetList(
			Array("SORT"=>"ASC"),
			Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "DEPTH_LEVEL"=>1),
			 false,			 
			 Array("ID")
		);	
		if(!$arElem=$resElem->GetNext())
			echo GetMessage('CATALOG_EMPTY_CATALOG');
	endif;
endif?>