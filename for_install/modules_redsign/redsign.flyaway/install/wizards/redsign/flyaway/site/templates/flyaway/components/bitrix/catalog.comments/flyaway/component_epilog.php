<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) {
    die();
}

$ajaxMode = isset($templateData['BLOG']['BLOG_FROM_AJAX']) && $templateData['BLOG']['BLOG_FROM_AJAX'];

$asset = \Bitrix\Main\Page\Asset::getInstance();

if (!$ajaxMode) {
	CJSCore::Init(array('window', 'ajax'));
}

if (isset($templateData['BLOG_USE']) && $templateData['BLOG_USE'] == 'Y') {

    if ($ajaxMode) {
        $arBlogCommentParams = array(
			'SEO_USER' => 'N',
			'ID' => $arResult['BLOG_DATA']['BLOG_POST_ID'],
			'BLOG_URL' => $arResult['BLOG_DATA']['BLOG_URL'],
			'PATH_TO_SMILE' => $arParams['PATH_TO_SMILE'],
			'COMMENTS_COUNT' => $arParams['COMMENTS_COUNT'],
			"DATE_TIME_FORMAT" => $DB->DateFormatToPhp(FORMAT_DATETIME),
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"AJAX_POST" => $arParams["AJAX_POST"],
			"AJAX_MODE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"SIMPLE_COMMENT" => "Y",
			"SHOW_SPAM" => $arParams["SHOW_SPAM"],
			"NOT_USE_COMMENT_TITLE" => "Y",
			"SHOW_RATING" => $arParams["SHOW_RATING"],
			"RATING_TYPE" => $arParams["RATING_TYPE"],
			"PATH_TO_POST" => $arResult["URL_TO_COMMENT"],
			"IBLOCK_ID" => $templateData['BLOG']['AJAX_PARAMS']['IBLOCK_ID'],
			"ELEMENT_ID" => $templateData['BLOG']['AJAX_PARAMS']['ELEMENT_ID'],
			"NO_URL_IN_COMMENTS" => "L",
			"COMMENT_PROPERTY" => $arParams['COMMENT_PROPERTY'],
			"BLOG_RATE_FIELD" => $arParams['BLOG_RATE_FIELD'],
		);

		$APPLICATION->IncludeComponent(
			'bitrix:blog.post.comment',
			'flyaway',
			$arBlogCommentParams,
			$this,
			array('HIDE_ICONS' => 'Y')
		);
		return;
    } else {
        $_SESSION['IBLOCK_CATALOG_COMMENTS_PARAMS_'.$templateData['BLOG']['AJAX_PARAMS']["IBLOCK_ID"].'_'.$templateData['BLOG']['AJAX_PARAMS']["ELEMENT_ID"]] = $templateData['BLOG']['AJAX_PARAMS'];
        if ($templateData['BLOG']['AJAX_PARAMS']['SHOW_RATING'] == 'Y')
		{
			ob_start();
			$APPLICATION->IncludeComponent(
				"bitrix:rating.vote", $arParams['RATING_TYPE'],
				array()
			);
			ob_end_clean();
		}
    }
}
