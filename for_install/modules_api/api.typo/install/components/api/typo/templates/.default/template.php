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

use \Bitrix\Main\Web\Json;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(method_exists($this, 'setFrameMode'))
	$this->setFrameMode(true);

if($arParams)
{
	foreach($arParams as $key => $val)
		unset($arParams[ '~' . $key ]);
}
?>
<? if($arParams['MESS_TPL_CONTENT']): ?>
	<div class="api-typo"><?=$arParams['MESS_TPL_CONTENT']?></div>
<? endif ?>
<?
ob_start();
?>
	<script>
		jQuery(function ($) {
			$.fn.apiTypo(<?=Json::encode($arParams)?>);
		});
	</script>
<?
$html = ob_get_contents();
ob_end_clean();

Asset::getInstance()->addString($html);
?>