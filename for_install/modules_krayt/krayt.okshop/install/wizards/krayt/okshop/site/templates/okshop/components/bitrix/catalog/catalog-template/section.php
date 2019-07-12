<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$arFilter = Array(
	"IBLOCK_ID"=>$arParams["IBLOCK_ID"], 
	"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
	"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
);
$countElements = CIBlockElement::GetList(Array(), $arFilter, Array());

if (!$arParams['FILTER_VIEW_MODE'])
	$arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
$arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');
$verticalGrid = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");

if ($verticalGrid)
{
	?><div class="workarea grid2x1"><?
}
if ($arParams['USE_FILTER'] == 'Y')
{

	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);
	if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
	{
		$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
	}
	elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
	{
		$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
	}

	$obCache = new CPHPCache();
	if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
	{
		$arCurSection = $obCache->GetVars();
	}
	elseif ($obCache->StartDataCache())
	{
		$arCurSection = array();
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID"));

			if(defined("BX_COMP_MANAGED_CACHE"))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/iblock/catalog");

				if ($arCurSection = $dbRes->Fetch())
				{
					$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
				}
				$CACHE_MANAGER->EndTagCache();
			}
			else
			{
				if(!$arCurSection = $dbRes->Fetch())
					$arCurSection = array();
			}
		}
		$obCache->EndDataCache($arCurSection);
	}
	if (!isset($arCurSection))
	{
		$arCurSection = array();
	}
	if ($verticalGrid)
	{
		?><div class="bx_sidebar"><?
	}
	?>
	
	
	<?if($countElements > 0) {?>
		
		<?$APPLICATION->IncludeComponent(
			"bitrix:catalog.smart.filter",
			"visual_".($arParams["FILTER_VIEW_MODE"] == "HORIZONTAL" ? "horizontal" : "vertical"),
			Array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"SECTION_ID" => $arCurSection['ID'],
				"FILTER_NAME" => $arParams["FILTER_NAME"],
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"SAVE_IN_SESSION" => "N",
				"XML_EXPORT" => "Y",
				"SECTION_TITLE" => "NAME",
				"SECTION_DESCRIPTION" => "DESCRIPTION",
				'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
				"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"]
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);?>
		
		<div class="advertisement">
			<?$APPLICATION->IncludeComponent("bitrix:main.include","", Array("AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/advertisement.inc.php","EDIT_TEMPLATE" => ""));?>
		</div>
		
		
	<?} else {?>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog-menu", array(
			"ROOT_MENU_TYPE" => "catalog",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => array(
			),
			"MAX_LEVEL" => "3",
			"CHILD_MENU_TYPE" => "catalog",
			"USE_EXT" => "Y",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N"
			),
			false
		);?>
	
		<div class="advertisement">
			<?$APPLICATION->IncludeComponent("bitrix:main.include","", Array("AREA_FILE_SHOW" => "file","PATH" => SITE_DIR."include/advertisement.inc.php","EDIT_TEMPLATE" => ""));?>
		</div>
			
		<?$APPLICATION->IncludeComponent("bitrix:news.list", ".default", array(
			"IBLOCK_TYPE" => "content",
			"IBLOCK_ID" => "#NEWS_IBLOCK_ID#",
			"NEWS_COUNT" => "3",
			"SORT_BY1" => "ACTIVE_FROM",
			"SORT_ORDER1" => "DESC",
			"SORT_BY2" => "SORT",
			"SORT_ORDER2" => "ASC",
			"FILTER_NAME" => "",
			"FIELD_CODE" => array(
				0 => "",
				1 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "",
				1 => "",
			),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "Y",
			"PREVIEW_TRUNCATE_LEN" => "",
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"PAGER_TEMPLATE" => "",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"AJAX_OPTION_ADDITIONAL" => ""
			),
			false
		);?>
	<?}?>
	
	<?
	if ($verticalGrid)
	{
		?></div><?
	}
}
if ($verticalGrid)
{
	?><div class="bx_content_section"><?
}
?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
		"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
		"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
	),
	$component
);?>

<?$intSectionID = 0;?>

<?
if($countElements > 0) 
{
	//default
	global $APPLICATION;
	$section_dir = $APPLICATION->GetCurDir();
	
	$section_view = (int)htmlspecialchars($APPLICATION->get_cookie('CATALOG_SECTION_VIEW'));
	if(!$section_view) $section_view = 1;
	$section_sort = (int)htmlspecialchars($APPLICATION->get_cookie('CATALOG_SECTION_SORT'));
	if(!$section_sort) $section_sort = 1;
	$section_page = (int)htmlspecialchars($APPLICATION->get_cookie('CATALOG_SECTION_PAGE'));
	if(!$section_page) $section_page = 1;


	//set filter
	if(!empty($_REQUEST['view'])) 
	{
		$section_view = intval($_REQUEST['view']);
		$APPLICATION->set_cookie("CATALOG_SECTION_VIEW", $section_view, time()+60*60*24*7);
	}
	if(!empty($_REQUEST['sort'])) 
	{
		$section_sort = intval($_REQUEST['sort']);
		$APPLICATION->set_cookie("CATALOG_SECTION_SORT", $section_sort, time()+60*60*24*7);	
	}
	if(!empty($_REQUEST['page'])) 
	{
		$section_page = intval($_REQUEST['page']);
		$APPLICATION->set_cookie("CATALOG_SECTION_PAGE", $section_page, time()+60*60*24*7);	
	}
	
	switch($section_view)
	{
		case '1': $template = '.default'; break;
		case '2': $template = 'list'; break;
	}
	
	switch($section_sort)
	{
		case '1':
			$arParams["ELEMENT_SORT_FIELD2"] = 'NAME';
			$arParams["ELEMENT_SORT_ORDER2"] = 'asc';
		break;
		case '2': 
			$arParams["ELEMENT_SORT_FIELD2"] = 'NAME';
			$arParams["ELEMENT_SORT_ORDER2"] = 'desc';
		break;
		case '3':
			$arParams["ELEMENT_SORT_FIELD2"] = 'CATALOG_PRICE_1';
			$arParams["ELEMENT_SORT_ORDER2"] = 'asc';
		break;
		case '4':
			$arParams["ELEMENT_SORT_FIELD2"] = 'CATALOG_PRICE_1';
			$arParams["ELEMENT_SORT_ORDER2"] = 'desc';
		break;
		case '5':
			$arParams["ELEMENT_SORT_FIELD2"] = 'PROPERTY_EMARKET_RATING';
			$arParams["ELEMENT_SORT_ORDER2"] = 'asc';
		break;
		case '6':
			$arParams["ELEMENT_SORT_FIELD2"] = 'PROPERTY_EMARKET_RATING';
			$arParams["ELEMENT_SORT_ORDER2"] = 'desc';
		break;
		case '7':
			$arParams["ELEMENT_SORT_FIELD2"] = 'PROPERTY_EMARKET_COMMENTS_COUNT';
			$arParams["ELEMENT_SORT_ORDER2"] = 'asc';
		break;
		case '8':
			$arParams["ELEMENT_SORT_FIELD2"] = 'PROPERTY_EMARKET_COMMENTS_COUNT';
			$arParams["ELEMENT_SORT_ORDER2"] = 'desc';
		break;
	}
	
	switch($section_page)
	{
		case '1': $arParams["PAGE_ELEMENT_COUNT"] = 40; break;
		case '2': $arParams["PAGE_ELEMENT_COUNT"] = 80; break;
		case '3': $arParams["PAGE_ELEMENT_COUNT"] = 120; break;
		case '4': $arParams["PAGE_ELEMENT_COUNT"] = 200; break;
		case '5': $arParams["PAGE_ELEMENT_COUNT"] = 1000; break;
	}
	
	?><div class="catalog-section-filter">
		<div class="option firts">
			<span><?=GetMessage('CSFILTER_VIEW');?>:</span>
			<a class="ico ico-1 <?if($section_view == 1) echo 'active';?>" href="<?=$APPLICATION->GetCurPageParam("view=1", array("view"))?>" title="Выводить товары плиткой"></a>
			<a class="ico ico-2 <?if($section_view == 2) echo 'active';?>" href="<?=$APPLICATION->GetCurPageParam("view=2", array("view"))?>" title="Выводить товары списком"></a>
		</div>
		<div class="option">
			<span><?=GetMessage('CSFILTER_SORTBY');?>:</span>
			<a 
				class="ico <?if(($section_sort == 1) || ($section_sort == 2)) echo 'active';?>" 
				href="<?
					$tempSort = 1;
					switch($section_sort)
					{
						case '1': $tempSort = 2; break;
						case '2': $tempSort = 1; break;
					}
					echo $APPLICATION->GetCurPageParam("sort=".$tempSort, array("sort"));
				?>">
				<?=GetMessage('CSFILTER_TITLE');?>
				<i class="ico-arrow <?if($section_sort == 2) echo 'down';?>"></i>
			</a>
			<a  class="ico <?if(($section_sort == 3) || ($section_sort == 4)) echo 'active';?>" 
				href="<?					
					$tempSort = 3;
					switch($section_sort)
					{
						case '3': $tempSort = 4; break;
						case '4': $tempSort = 3; break;
					}
					echo $APPLICATION->GetCurPageParam("sort=".$tempSort, array("sort"));
				?>">
				<?=GetMessage('CSFILTER_PRICE');?>
				<i class="ico-arrow <?if($section_sort == 4) echo 'down';?>"></i>
			</a>
			<a  class="ico <?if(($section_sort == 5) || ($section_sort == 6)) echo 'active';?>" 
				href="<?
					$tempSort = 5;
					switch($section_sort)
					{
						case '5': $tempSort = 6; break;
						case '6': $tempSort = 5; break;
					}
					echo $APPLICATION->GetCurPageParam("sort=".$tempSort, array("sort"));
				?>">
				<?=GetMessage('CSFILTER_RATING');?>
				<i class="ico-arrow <?if($section_sort == 6) echo 'down';?>"></i>
			</a>
			<a  class="ico <?if(($section_sort == 7) || ($section_sort == 8)) echo 'active';?>" 
				href="<?
					$tempSort = 7;
					switch($section_sort)
					{
						case '7': $tempSort = 8; break;
						case '8': $tempSort = 7; break;
					}
					echo $APPLICATION->GetCurPageParam("sort=".$tempSort, array("sort"));					
				?>">
				<?=GetMessage('CSFILTER_NUMBER');?>
				<i class="ico-arrow  <?if($section_sort == 8) echo 'down';?>"></i>
			</a>
		</div>
		<div class="option">
			<span><?=GetMessage('CSFILTER_PAGE');?>:</span>
			<a class="ico <?if($section_page == 1) echo 'active';?>" href="<?=$APPLICATION->GetCurPageParam("page=1", array("page"))?>">40</a>	
			<a class="ico <?if($section_page == 2) echo 'active';?>" href="<?=$APPLICATION->GetCurPageParam("page=2", array("page"))?>">80</a>
			<a class="ico <?if($section_page == 3) echo 'active';?>" href="<?=$APPLICATION->GetCurPageParam("page=3", array("page"))?>">120</a>	
			<a class="ico <?if($section_page == 4) echo 'active';?>" href="<?=$APPLICATION->GetCurPageParam("page=4", array("page"))?>">200</a>	
			<a class="ico <?if($section_page == 5) echo 'active';?>" href="<?=$APPLICATION->GetCurPageParam("page=5", array("page"))?>"><?=GetMessage('CSFILTER_ALL');?></a>	
		</div>
	</div><?
}?>

<?$intSectionID = $APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	$template,
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
		"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
		"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
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
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
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
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

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
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
		"OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],

		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

		'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
		'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
		'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
		'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
		'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
		'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
		'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
		'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

		'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
		"ADD_SECTIONS_CHAIN" => "N",
		"COMPARE_NAME" => $arParams['COMPARE_NAME']
	),
	$component
);
?><?
if ($verticalGrid)
{
	?></div>
	<div style="clear: both;"></div>
</div><?
}
?>