<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);
?>


<?php if ($arParams['USE_ARCHIVE'] == 'Y'): ?>
    	<?php //$this->SetViewTarget('sidebar'); ?>
        <?$APPLICATION->IncludeComponent(
        	"redsign:news.archive", 
        	"labels", 
        	array(
        		"COMPONENT_TEMPLATE" => "flyaway",
        		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
        		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
        		"FILTER_NAME" => $arParams["FILTER_NAME"],
        		"CHECK_DATES" => $arParams["CHECK_DATES"],
        		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
        		"CACHE_TIME" => $arParams["CACHE_TIME"],
        		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
        		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
        		"ACTIVE_DATE_FORMAT" => $arParams["LIST_ACTIVE_DATE_FORMAT"],
        		"SHOW_YEARS" => $arParams["ARCHIVE_SHOW_YEARS"],
        		"SHOW_MONTHS" => $arParams["ARCHIVE_SHOW_MONTHS"],
        		/*
        	    "PARENT_SECTION" => $arParams["CHECK_DATES"],
        		"PARENT_SECTION_CODE" => $arParams["CHECK_DATES"],
        		"INCLUDE_SUBSECTIONS" => $arParams["CHECK_DATES"],
        		"COMPOSITE_FRAME_MODE" => $arParams["CHECK_DATES"],
        		"COMPOSITE_FRAME_TYPE" => $arParams["CHECK_DATES"],
        		*/
        	    "SEF_FOLDER" => $arResult["FOLDER"],
        		"ARCHIVE_URL" => $arResult["FOLDER"].$arParams["ARCHIVE_URL"],
        	    "SEF_MODE" => $arParams["SEF_MODE"],
        	),
        	$component
        );?>
        <?php //$this->EndViewTarget(); ?>
<?php endif; ?>

<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	$arParams['RSFLYAWAY_LIST_TEMPLATES_LIST'],
	Array(
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
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
		// FLYAWAY
		"RSFLYAWAY_SHOW_BLOCK_NAME"		=> $arParams["RSFLYAWAY_SHOW_BLOCK_NAME_LIST"],
		"RSFLYAWAY_BLOCK_NAME_IS_LINK"		=> $arParams["RSFLYAWAY_BLOCK_NAME_IS_LINK_LIST"],
		"RSFLYAWAY_USE_OWL"				=> $arParams["RSFLYAWAY_USE_OWL_LIST"],
		"RSFLYAWAY_OWL_CHANGE_SPEED"		=> $arParams["RSFLYAWAY_OWL_CHANGE_SPEED_LIST"],
		"RSFLYAWAY_OWL_CHANGE_DELAY"		=> $arParams["RSFLYAWAY_OWL_CHANGE_DELAY_LIST"],
		"RSFLYAWAY_OWL_PHONE"				=> $arParams["RSFLYAWAY_OWL_PHONE_LIST"],
		"RSFLYAWAY_OWL_TABLET"				=> $arParams["RSFLYAWAY_OWL_TABLET_LIST"],
		"RSFLYAWAY_OWL_PC"					=> $arParams["RSFLYAWAY_OWL_PC_LIST"],
		"RSFLYAWAY_COLS_IN_ROW"			=> $arParams["RSFLYAWAY_COLS_IN_ROW_LIST"],
		"RSFLYAWAY_PROP_PUBLISHER_NAME"	=> $arParams["RSFLYAWAY_PROP_PUBLISHER_NAME_LIST"],
		"RSFLYAWAY_PROP_PUBLISHER_BLANK"	=> $arParams["RSFLYAWAY_PROP_PUBLISHER_BLANK_LIST"],
		"RSFLYAWAY_PROP_PUBLISHER_DESCR"	=> $arParams["RSFLYAWAY_PROP_PUBLISHER_DESCR_LIST"],
		"RSFLYAWAY_PROP_MARKER_TEXT"		=> $arParams["RSFLYAWAY_PROP_MARKER_TEXT_LIST"],
		"RSFLYAWAY_PROP_MARKER_COLOR"		=> $arParams["RSFLYAWAY_PROP_MARKER_COLOR_LIST"],
		"RSFLYAWAY_PROP_ACTION_DATE"		=> $arParams["RSFLYAWAY_PROP_ACTION_DATE_LIST"],
		"RSFLYAWAY_PROP_FILE"				=> $arParams["RSFLYAWAY_PROP_FILE_LIST"],
		"RSFLYAWAY_LINK"					=> $arParams["RSFLYAWAY_LINK_LIST"],
		"RSFLYAWAY_BLANK"					=> $arParams["RSFLYAWAY_BLANK_LIST"],
		"RSFLYAWAY_SHOW_DATE"				=> $arParams["RSFLYAWAY_SHOW_DATE_LIST"],
		"RSFLYAWAY_AUTHOR_NAME"			=> $arParams["RSFLYAWAY_AUTHOR_NAME_LIST"],
		"RSFLYAWAY_AUTHOR_JOB"				=> $arParams["RSFLYAWAY_AUTHOR_JOB_LIST"],
		"RSFLYAWAY_SHOW_BUTTON"			=> $arParams["RSFLYAWAY_SHOW_BUTTON_LIST"],
		"RSFLYAWAY_BUTTON_CAPTION"			=> $arParams["RSFLYAWAY_BUTTON_CAPTION_LIST"],
		"RSFLYAWAY_PROP_VACANCY_TYPE"		=> $arParams["RSFLYAWAY_PROP_VACANCY_TYPE_LIST"],
		"RSFLYAWAY_PROP_SIGNATURE"			=> $arParams["RSFLYAWAY_PROP_SIGNATURE_LIST"],
	),
	$component
);?>