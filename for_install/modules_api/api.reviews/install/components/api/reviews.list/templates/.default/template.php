<?php

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

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

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}

include 'ajax.php';

/*
	//Режим 1
	$reviewsId = "API_REVIEWS_LIST_" . $component->randString();
?>
	<div id="<?=$reviewsId?>">
		<?
		$dynamicArea = new \Bitrix\Main\Page\FrameStatic(ToLower($reviewsId));
		$dynamicArea->setAnimation(true);
		$dynamicArea->setStub('');
		$dynamicArea->setContainerID($reviewsId);
		$dynamicArea->startDynamicArea();
		include 'ajax.php';
		$dynamicArea->finishDynamicArea();
		?>
	</div>
<?
*/
/*
	//Режим 2
	$frame = $this->createFrame()->begin('Loading...');
	include 'ajax.php';
	$frame->end();
*/
/*
	//Режим 3
	$reviewsId = "api_reviews_".$component->randString();
?>
	<div id="<?=$reviewsId?>">
		<?
		$frame = $this->createFrame($reviewsId, false)->begin();
		require(realpath(dirname(__FILE__)).'/ajax.php');
		$frame->beginStub();
		$arResult['COMPOSITE_STUB'] = 'Y';
		require(realpath(dirname(__FILE__)).'/ajax.php');
		unset($arResult['COMPOSITE_STUB']);
		$frame->end();
		?>
	</div>
<?
*/

ob_start();
?>
	<style type="text/css">
		<?if($arParams['THUMBNAIL_WIDTH'] && $arParams['THUMBNAIL_HEIGHT']):?>
		.api-reviews-list .api-field-files .api-file-outer{
			width: <?=$arParams['THUMBNAIL_WIDTH']?>px;
			height: <?=$arParams['THUMBNAIL_HEIGHT']?>px;
		}
		<?endif?>
		<?if($arParams['COLOR']):?>
		#api-reviews-wait .api-image{
			background-image: url("/bitrix/images/api.reviews/<?=$arParams['THEME']?>/<?=$arParams['COLOR']?>/wait.svg");
		}
		<?endif?>
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {

			$.fn.apiReviewsList({
				use_stat: '<?=$arParams['USE_STAT']?>',
				mess: {
					shop_name: '<?=$arParams['SHOP_NAME']?>',
					shop_name_reply: '<?=$arParams['SHOP_NAME_REPLY']?>',
					review_delete: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_REVIEW_DELETE')?>',
					review_link: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_REVIEW_LINK')?>',
					btn_reply_save: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_BTN_REPLY_SAVE')?>',
					btn_reply_cancel: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_BTN_REPLY_CANCEL')?>',
					btn_reply_send: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_BTN_REPLY_SEND')?>',
				},
				getFileDelete: {
					confirmTitle: '<?=Loc::getMessage('apiReviesList_getFileDelete_confirmTitle')?>',
					confirmContent: '<?=Loc::getMessage('apiReviesList_getFileDelete_confirmContent')?>',
					labelOk: '<?=Loc::getMessage('apiReviesList_getFileDelete_labelOk')?>',
					labelCancel: '<?=Loc::getMessage('apiReviesList_getFileDelete_labelCancel')?>',
				},
				getVideoDelete: {
					confirmTitle: '<?=Loc::getMessage('apiReviesList_getVideoDelete_confirmTitle')?>',
					confirmContent: '<?=Loc::getMessage('apiReviesList_getVideoDelete_confirmContent')?>',
					labelOk: '<?=Loc::getMessage('apiReviesList_getVideoDelete_labelOk')?>',
					labelCancel: '<?=Loc::getMessage('apiReviesList_getVideoDelete_labelCancel')?>',
				}
			});

			$.fn.apiReviewsList('updateCount', '<?=$arResult['COUNT_ITEMS']?>');

			<?if($arResult['SCROLL_TO']):?>
			$(window).on('load',function () {
				var reviewId = '#review<?=$arResult['SCROLL_TO']?>';
				if($(reviewId).length){
					$('html, body').animate({
						scrollTop: $(reviewId).offset().top
					}, 400, function () {
						$(reviewId).addClass('api-active');
					});
				}
			});
			<?endif?>

			//$('[rel="apiReviewsPhoto"]').apiLightbox();
		});
	</script>
<?
$html = ob_get_contents();
ob_end_clean();

Asset::getInstance()->addString($html, true, AssetLocation::AFTER_JS);