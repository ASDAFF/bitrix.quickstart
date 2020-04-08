<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

?>
<!-- action-detail -->
<div class="action-detail">
	<div class="row">
		<div class="col-md-8">
			<?$ElementID = $APPLICATION->IncludeComponent(
				'bitrix:news.detail',
				'',
				Array(
					'DISPLAY_DATE' => $arParams['DISPLAY_DATE'],
					'DISPLAY_NAME' => $arParams['DISPLAY_NAME'],
					'DISPLAY_PICTURE' => $arParams['DISPLAY_PICTURE'],
					'DISPLAY_PREVIEW_TEXT' => $arParams['DISPLAY_PREVIEW_TEXT'],
					'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
					'IBLOCK_ID' => $arParams['IBLOCK_ID'],
					'FIELD_CODE' => $arParams['DETAIL_FIELD_CODE'],
					'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
					'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
					'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
					'META_KEYWORDS' => $arParams['META_KEYWORDS'],
					'META_DESCRIPTION' => $arParams['META_DESCRIPTION'],
					'BROWSER_TITLE' => $arParams['BROWSER_TITLE'],
					'DISPLAY_PANEL' => $arParams['DISPLAY_PANEL'],
					'SET_TITLE' => $arParams['SET_TITLE'],
					'SET_STATUS_404' => $arParams['SET_STATUS_404'],
					'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
					'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
					'ACTIVE_DATE_FORMAT' => $arParams['DETAIL_ACTIVE_DATE_FORMAT'],
					'CACHE_TYPE' => $arParams['CACHE_TYPE'],
					'CACHE_TIME' => $arParams['CACHE_TIME'],
					'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
					'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
					'GROUP_PERMISSIONS' => $arParams['GROUP_PERMISSIONS'],
					'DISPLAY_TOP_PAGER' => $arParams['DETAIL_DISPLAY_TOP_PAGER'],
					'DISPLAY_BOTTOM_PAGER' => $arParams['DETAIL_DISPLAY_BOTTOM_PAGER'],
					'PAGER_TITLE' => $arParams['DETAIL_PAGER_TITLE'],
					'PAGER_SHOW_ALWAYS' => 'N',
					'PAGER_TEMPLATE' => $arParams['DETAIL_PAGER_TEMPLATE'],
					'PAGER_SHOW_ALL' => $arParams['DETAIL_PAGER_SHOW_ALL'],
					'CHECK_DATES' => $arParams['CHECK_DATES'],
					'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
					'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
					'IBLOCK_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'],
					'USE_SHARE' => $arParams['USE_SHARE'],
					'SHARE_HIDE' => $arParams['SHARE_HIDE'],
					'SHARE_TEMPLATE' => $arParams['SHARE_TEMPLATE'],
					'SHARE_HANDLERS' => $arParams['SHARE_HANDLERS'],
					'SHARE_SHORTEN_URL_LOGIN' => $arParams['SHARE_SHORTEN_URL_LOGIN'],
					'SHARE_SHORTEN_URL_KEY' => $arParams['SHARE_SHORTEN_URL_KEY'],
					"SET_LAST_MODIFIED" => "Y",
				),
				$component
			);
			?>
			<!-- detail-footer -->
			<div class="detail-footer">
				<div class="row">
					<div class="col-sm-6 col-sm-push-6">
						<div class="share">
							<? $APPLICATION->IncludeFile(
								'/includes/share.php',
								array(),
								array(
									'MODE' => 'text',
									'NAME' => 'Поделиться',
								)
							); ?>
						</div>
					</div>
					<div class="col-sm-6 col-sm-pull-6">
						<a class="btn btn-sm btn-info btn-back" href="<?=$arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news']?>"><span class="icon icon-arrow-left"></span> <?=GetMessage('T_NEWS_DETAIL_BACK')?></a>
					</div>
				</div>
			</div>

		</div>
		<div class="col-md-4">

			<!-- other-items -->
			<div class="other-items">
				<h3>Другие акции</h3>
				<? $GLOBALS["arrFilterNews"] = array("!ID" => $ElementID); ?>
				<?$APPLICATION->IncludeComponent(
	"bitrix:news.list", 
	"sidebar_item", 
	array(
		"COMPONENT_TEMPLATE" => "sidebar_item",
		"IBLOCK_TYPE" => "Content",
		"IBLOCK_ID" => \Indi\Main\Iblock\ID_Content_News,
		"NEWS_COUNT" => "2",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"FILTER_NAME" => "arrFilterNews",
		"FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "DATE_ACTIVE_FROM",
			3 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"DETAIL_URL" => "/news/#ELEMENT_CODE#/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "/news/",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"FILE_404" => ""
	),
	false
);?>



			</div>

		</div>
	</div>
</div>
