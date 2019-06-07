<?php
/**
 * Bitrix vars
 *
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent         $component
 *
 * @var array                    $arParams
 * @var array                    $arResult
 *
 * @var string                   $templateName
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var array                    $templateData
 *
 * @var string                   $componentPath
 * @var string                   $parentTemplateFolder
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}
?>
<div id="reviews" class="api-reviews" itemscope itemtype="http://schema.org/Product">
	<? $APPLICATION->IncludeComponent(
		 'api:reviews.detail',
		 '',
		 array(
				'THEME'              => $arParams['~THEME'],
				'COLOR'              => $arParams['~COLOR'],
				'EMAIL_TO'           => $arParams['~EMAIL_TO'],
				'SHOP_NAME'          => $arParams['~SHOP_NAME'],
				'INCLUDE_CSS'        => $arParams['~INCLUDE_CSS'],
				'INCLUDE_JQUERY'     => $arParams['~INCLUDE_JQUERY'],
				'CACHE_TYPE'         => $arParams['~CACHE_TYPE'],
				'CACHE_TIME'         => $arParams['~CACHE_TIME'],
				'ITEMS_LIMIT'        => $arParams['~LIST_ITEMS_LIMIT'],
				'ACTIVE_DATE_FORMAT' => $arParams['~LIST_ACTIVE_DATE_FORMAT'],
				'SET_TITLE'          => $arParams['~LIST_SET_TITLE'],
				'SHOW_THUMBS'        => $arParams['~LIST_SHOW_THUMBS'],
				'ALLOW'              => $arParams['~LIST_ALLOW'],
				'SHOP_NAME_REPLY'    => $arParams['~LIST_SHOP_NAME_REPLY'],
				'PICTURE'            => $arParams['~PICTURE'],
				'RESIZE_PICTURE'     => $arParams['~RESIZE_PICTURE'],
				'USE_USER'           => $arParams['~USE_USER'],

				'MESS_ADD_UNSWER_EVENT_THEME'  => $arParams['~LIST_MESS_ADD_UNSWER_EVENT_THEME'],
				'MESS_ADD_UNSWER_EVENT_TEXT'   => $arParams['~LIST_MESS_ADD_UNSWER_EVENT_TEXT'],
				'MESS_TRUE_BUYER'              => $arParams['~LIST_MESS_TRUE_BUYER'],
				'MESS_HELPFUL_REVIEW'          => $arParams['~LIST_MESS_HELPFUL_REVIEW'],

				//new
				'USE_SUBSCRIBE'                => $arParams['~USE_SUBSCRIBE'],
				'DISPLAY_FIELDS'               => $arParams['~FORM_DISPLAY_FIELDS'],
				'MESS_FIELD_NAME_RATING'       => $arParams['MESS_FIELD_NAME_RATING'],
				'MESS_FIELD_NAME_ORDER_ID'     => $arParams['MESS_FIELD_NAME_ORDER_ID'],
				'MESS_FIELD_NAME_TITLE'        => $arParams['MESS_FIELD_NAME_TITLE'],
				'MESS_FIELD_NAME_ADVANTAGE'    => $arParams['MESS_FIELD_NAME_ADVANTAGE'],
				'MESS_FIELD_NAME_DISADVANTAGE' => $arParams['MESS_FIELD_NAME_DISADVANTAGE'],
				'MESS_FIELD_NAME_ANNOTATION'   => $arParams['MESS_FIELD_NAME_ANNOTATION'],
				'MESS_FIELD_NAME_DELIVERY'     => $arParams['MESS_FIELD_NAME_DELIVERY'],
				'MESS_FIELD_NAME_GUEST_NAME'   => $arParams['MESS_FIELD_NAME_GUEST_NAME'],
				'MESS_FIELD_NAME_GUEST_EMAIL'  => $arParams['MESS_FIELD_NAME_GUEST_EMAIL'],
				'MESS_FIELD_NAME_GUEST_PHONE'  => $arParams['MESS_FIELD_NAME_GUEST_PHONE'],
				'MESS_FIELD_NAME_CITY'         => $arParams['MESS_FIELD_NAME_CITY'],
				'MESS_FIELD_NAME_COMPANY'      => $arParams['~MESS_FIELD_NAME_COMPANY'],
				'MESS_FIELD_NAME_WEBSITE'      => $arParams['~MESS_FIELD_NAME_WEBSITE'],
				'MESS_FIELD_NAME_FILES'        => $arParams['~MESS_FIELD_NAME_FILES'],
				'MESS_FIELD_NAME_VIDEOS'       => $arParams['~MESS_FIELD_NAME_VIDEOS'],

				'THUMBNAIL_WIDTH'  => $arParams['~THUMBNAIL_WIDTH'],
				'THUMBNAIL_HEIGHT' => $arParams['~THUMBNAIL_HEIGHT'],

				'SET_STATUS_404' => $arParams['~SET_STATUS_404'],
				'SHOW_404'       => $arParams['~SHOW_404'],
				'FILE_404'       => $arParams['~FILE_404'],
				'MESSAGE_404'    => $arParams['~MESSAGE_404'],

				'COMPOSITE_FRAME_MODE' => $arParams['~COMPOSITE_FRAME_MODE'],
				'COMPOSITE_FRAME_TYPE' => $arParams['~COMPOSITE_FRAME_TYPE'],

				"ID"          => $arResult['VARIABLES']['review_id'],
				'DETAIL_HASH' => $arParams['~DETAIL_HASH'],
				'SEF_MODE'    => $arParams['~SEF_MODE'],
				'SEF_FOLDER'  => $arParams['~SEF_FOLDER'],
				'DETAIL_URL'  => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
				'LIST_URL'    => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['list'],
				'USER_URL'    => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['user'],
		 ),
		 $component
	); ?>
	<div class="api-back-link">
		<a href="<?=$arResult['FOLDER'] . $arResult['URL_TEMPLATES']['list'] . '#reviews'?>"><?=Loc::getMessage('API_REVIEWS_DETAIL_BACK_LINK')?></a>
	</div>
</div>
<?
ob_start();
?>
<script type="text/javascript">
	jQuery(document).ready(function ($) {
		$.fn.apiReviews();
	});
</script>
<?
$html = ob_get_contents();
ob_end_clean();

Asset::getInstance()->addString($html, true, AssetLocation::AFTER_JS);
?>