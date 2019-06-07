<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 * @var CUserTypeManager         $USER_FIELD_MANAGER
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<? if($arResult): ?>
	<table style="margin:0;padding:0;border-collapse:collapse;border-width:1px;border-style:solid;border-color:#E0EAF0;"
	       width="100%"
	       border="0"
	       cellpadding="0"
	       cellspacing="0">
		<thead>
		<tr>
			<th style="border: none;background-color:#e7f2f2; padding:10px; text-align:center;font-size: 18px;">
				<?=($arResult['SUBJECT'] ? htmlspecialcharsback($arResult['SUBJECT']) : Loc::getMessage('AOS_TPL_HEADER_BLOCK'))?>
			</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="border:0;padding:10px;background-color:#F7FAFA"><?=htmlspecialcharsback($arResult['HTML'])?></td>
		</tr>
		</tbody>
	</table>
<? endif ?>