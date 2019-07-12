<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();



// число элементов в строке
$countElemsInRow = 4;
//echo count($arResult[SEARCH]);
//deb($arResult["NAV_RESULT"]->NavRecordCount);
//deb($arParams);

?>
<div class="search-page">
<?
/*if ($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false ):
?>
<?
elseif ($arResult["ERROR_CODE"]!=0):
?>
	<?php 
	require_once($_SERVER["DOCUMENT_ROOT"] . SITE_DIR . "include/search_not_found.php");
	?>
<?
else*/
if (count($arResult["SEARCH"])>0): 
	//deb($arParams);
	/*$countElemsOnPage = $arParams["PAGE_RESULT_COUNT"];
	if ($countElemsOnPage == N_PAGE_SIZE_1) {
		$countElemsOnPage = N_PAGE_SIZE_2;
	} else {
		$countElemsOnPage = N_PAGE_SIZE_1;
	}
	*/
	$j = 1; // счетчик элементов для определения конца строки


	?>
			<div class="brend">	
			<?php 
	foreach($arResult["ELEMENTS"] as $val) {
		
		if (in_array($val["ID"], $arResult['FAVORITE_BRANDS'])) {
			$additional_class = ' active';
			$action = 'del';
		}
		else {
			$additional_class = '';
			$action = 'add';
		}
		$imageryURL = IMAGERIES_URI . "?elmid=" . $val['ID'];
		//deb($val['PROPERTY_PHOTOS_VALUE']);
		if($arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ] == "")
			$arResult['PREVIEW_PICTURE'][ $val['PROPERTY_PHOTOS_VALUE'][0] ] = SITE_TEMPLATE_PATH."/images/nophoto.png";
		
		$brandURL = SITE_DIR."brands/?id=" . $val['ID'];
		$FilterURL = "/clothing/?iblid=0&iNumPage=1&nPageSize=". N_PAGE_SIZE_1 ."&arFilter[0][PROPERTY_BRANDNAME]=".$val['ID'];
		?>
			<div class="list">
			<div class="itemsall clearfix brands-list">
				<div class="itemsall_img">
				<a href="<?=$brandURL?>">
				<img width="140" src="<?=$arResult['PREVIEW_PICTURE'][$val['ID']];?>" alt="" />
				</a>
				</div>
				<div class="item itemsall_op">
					<div class="title">
					<a href="<?=$brandURL?>"><?=$val['NAME']?></a>
					</div>
					
					<div class="clear"></div>
					<a href="<?=$FilterURL?>" class="btn">Товары бренда</a>
					<div class="personal_notes"><?=$val['DETAIL_TEXT'];?></div>
				</div>
			</div>
			<hr>
			</div>	
			
			<?php
		$j++;
			
	}
	?>			
			<div class="clear"></div>
			</div>
	<?php 
	//}
	?>
	<div id="navigate" class="navigate">
	<?=$arResult["NAV_STRING"]?>
	</div>
<?else:?>
	<?$APPLICATION->IncludeComponent("bitrix:news.detail", "pure", array(
			"IBLOCK_TYPE" => "systems",
			"IBLOCK_ID" => "231",
			"ELEMENT_ID" => "",
			"ELEMENT_CODE" => "search-by-brand",
			"CHECK_DATES" => "Y",
			"FIELD_CODE" => array(
					0 => "",
					1 => "",
			),
			"PROPERTY_CODE" => array(
					0 => "",
					1 => "",
			),
			"IBLOCK_URL" => "",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_GROUPS" => "N",
			"META_KEYWORDS" => "-",
			"META_DESCRIPTION" => "-",
			"BROWSER_TITLE" => "-",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"USE_PERMISSIONS" => "N",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "Страница",
			"PAGER_TEMPLATE" => "",
			"PAGER_SHOW_ALL" => "N",
			"DISPLAY_DATE" => "N",
			"DISPLAY_NAME" => "N",
			"DISPLAY_PICTURE" => "N",
			"DISPLAY_PREVIEW_TEXT" => "N",
			"USE_SHARE" => "N",
			"AJAX_OPTION_ADDITIONAL" => ""
	),
			false
	);
	?>
	<?php 
	//require_once($_SERVER["DOCUMENT_ROOT"] . SITE_DIR . "include/search_not_found.php");
	?>	
<?endif;?>
</div>