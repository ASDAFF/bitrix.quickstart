<?php
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

//$this - объект шаблона
//$component - объект компонента

//$this->GetFolder()
//$tplId = $this->GetEditAreaId($arResult['ID']);

//Объект родительского компонента
//$parent = $component->getParent();
//$parentPath = $parent->getPath();

use Bitrix\Main\Page\Asset,
	 Bitrix\Main\Page\AssetLocation,
	 Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams['INCLUDE_CSS'] == 'Y') {
	$this->addExternalCss($templateFolder . '/theme/' . $arParams['THEME'] . '/style.css');
}

//plugins
$this->addExternalCss($templateFolder . '/plugins/button/api.button.css');

include 'ajax.php';

ob_start();
?>
	<style type="text/css">
		<?if($arParams['COLOR']):?>
		#api-reviews-wait .api-image{
			background-image: url("/bitrix/images/api.reviews/<?=$arParams['THEME']?>/<?=$arParams['COLOR']?>/wait.svg");
		}
		<?endif?>
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$.fn.apiReviewsDetail({
				mess: {
					shop_name: '<?=$arParams['SHOP_NAME']?>',
					shop_name_reply: '<?=$arParams['SHOP_NAME_REPLY']?>',
					review_delete: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_REVIEW_DELETE')?>',
					review_link: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_REVIEW_LINK')?>',
					btn_reply_save: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_BTN_REPLY_SAVE')?>',
					btn_reply_cancel: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_BTN_REPLY_CANCEL')?>',
					btn_reply_send: '<?=Loc::getMessage('API_REVIEWS_LIST_JS_BTN_REPLY_SEND')?>'
				},
				getFileDelete:{
					confirmTitle: '<?=Loc::getMessage('apiReviesList_getFileDelete_confirmTitle')?>',
					confirmContent: '<?=Loc::getMessage('apiReviesList_getFileDelete_confirmContent')?>',
					labelOk: '<?=Loc::getMessage('apiReviesList_getFileDelete_labelOk')?>',
					labelCancel: '<?=Loc::getMessage('apiReviesList_getFileDelete_labelCancel')?>',
				},
				getVideoDelete:{
					confirmTitle: '<?=Loc::getMessage('apiReviesList_getVideoDelete_confirmTitle')?>',
					confirmContent: '<?=Loc::getMessage('apiReviesList_getVideoDelete_confirmContent')?>',
					labelOk: '<?=Loc::getMessage('apiReviesList_getVideoDelete_labelOk')?>',
					labelCancel: '<?=Loc::getMessage('apiReviesList_getVideoDelete_labelCancel')?>',
				}
			});

			$(window).on('load', function () {
				var reviewId = '#review<?=$arResult['ID']?>';
				$('html, body').animate({
					scrollTop: $(reviewId).offset().top
				}, 200);
			});
		});
	</script>

<?
$html = ob_get_contents();
ob_end_clean();

Asset::getInstance()->addString($html, true, AssetLocation::AFTER_JS);
?>