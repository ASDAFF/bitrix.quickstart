<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if($arParams["USE_RSS"]=="Y"):?>
	<?
		if(method_exists($APPLICATION, 'addheadstring'))
		$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" title="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" href="'.$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"].'" />');
	?>
	<a href="<?=$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["rss"]?>" target="_blank" title="RSS" class="rss_feed_icon"><?=GetMessage("RSS_TITLE")?></a>
<?endif;?>
<?if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?>
	<div class="right_side wide">
		<div class="ask_small_block">
			<div class="ask_btn_block">
				<a class="button vbig_btn wides ask_btn"><span><?=GetMessage("ASK_QUESTION")?></span></a>
			</div>
			<div class="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/ask_block_description.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("ASK_QUESTION_TEXT"), ));?>
			</div>
		</div>
	</div>
	<div class="left_side wide">
<?endif;?>
<?if($arParams["USE_FILTER"]=="Y"){
	$arYears=COptimus::GetYearsItems($arParams["IBLOCK_ID"]);
	arsort($arYears);
	if($arYears){?>
		<div class="filter_block border_block">
			<ul>
				<li class="prop <?=( $item["ACTIVE"] == "Y" ? 'active' : '' );?>">
					<a href="<?=$arParams["SEF_FOLDER"]?>"><?=GetMessage("ALL");?></a>
				</li>
				<?foreach( $arYears as $year ){?>
					<li class="prop <?=( $item["ACTIVE"] == "Y" ? 'active' : '' );?>">
						<?if( $arResult["VARIABLES"]["YEAR"] == $year ){?>
							<span>
						<?}else{?>
							<a href="<?=$arParams["SEF_FOLDER"]?><?=$year?>/">
						<?}?>
							<?=$year?>
						<?if( $arResult["VARIABLES"]["YEAR"] == $year ){?>
							</span>
						<?}else{?>
							</a>
						<?}?>
					</li>
				<?}?>
			</ul>
			<div class="cls"></div>
		</div>
	<?}?>
	<?$GLOBALS['arrFilter'] = array(
		">DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$arResult["VARIABLES"]["YEAR"], "DD.MM.YYYY"),
		"<=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".(intval($arResult["VARIABLES"]["YEAR"])+1), "DD.MM.YYYY"),
		array(
			"LOGIC" => "OR", 
			array( "PROPERTY_REGIONS" => $arRegion["ID"] ),
			array( "PROPERTY_REGIONS" => false )
		),
	);
	?>
<?}?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"main_template",
	Array(
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
		"IS_VERTICAL"	=>	$arParams["IS_VERTICAL"],
		"SHOW_FAQ_BLOCK"	=>	($arParams["SHOW_FAQ_BLOCK"] == "Y" ? "Y" : "N" ),
		"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
		"SORT_BY1"	=>	$arParams["SORT_BY1"],
		"SORT_ORDER1"	=>	$arParams["SORT_ORDER1"],
		"SORT_BY2"	=>	$arParams["SORT_BY2"],
		"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
		"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
		"SET_TITLE"	=>	$arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN"	=>	$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
		"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
		"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER"	=>	$arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER"	=>	$arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE"	=>	$arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE"	=>	$arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS"	=>	$arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
		"DISPLAY_NAME"	=>	"Y",
		"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME"	=>	$arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
		"SHOW_FAQ_BLOCK" => $arParams["SHOW_FAQ_BLOCK"],
		"SHOW_BACK_LINK" => $arParams["SHOW_BACK_LINK"],
	),
	$component
);?>

<?if ($arParams["SHOW_FAQ_BLOCK"]=="Y"):?></div><?endif;?>