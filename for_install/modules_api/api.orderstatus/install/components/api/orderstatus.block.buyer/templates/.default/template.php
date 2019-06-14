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
			<th style="border: none;background-color:#E7F2F2; padding:10px; text-align:left;text-transform:uppercase"><?=Loc::getMessage('AOS_TPL_BUYER_BLOCK')?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="border:0;padding:0 10px 15px 10px;background-color:#F7FAFA">
				<? foreach($arResult['GROUP'] as $arGroup): ?>
					<div style="border: 1px solid #dadee1;border-radius: 3px;margin-top: 15px;padding: 5px;position:relative">
						<div style="font-size: 13px;line-height: 16px;position: absolute;top: -8px;left: 15px;height: 16px;padding: 0 5px;color: #66878f;background: #f7fafa;">
							<?=($arGroup['ID'] == 'USER_DESCRIPTION' ? Loc::getMessage('AOS_TPL_GROUP_BUYER_COMMENT') : $arGroup['NAME'])?>
						</div>
						<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0;padding:0">
							<tbody>
							<? if($arGroup['PROPERTIES']): ?>
								<? foreach($arGroup['PROPERTIES'] as $arProperty): ?>
									<tr>
										<td style="text-align:right;padding:2px 4px 3px 0;width:50%"><?=$arProperty['NAME']?>:</td>
										<td style="text-align:left;padding:2px 0 3px 4px;width:50%"><?=$arProperty['VALUE']?></td>
									</tr>
								<? endforeach; ?>
							<? endif ?>
							<? if($arGroup['ID'] == 'USER_DESCRIPTION'): ?>
								<tr>
									<td style="text-align:right;padding:2px 4px 3px 0;width:50%"><?=Loc::getMessage('AOS_TPL_BUYER_COMMENT')?>:</td>
									<td style="text-align:left;padding:2px 0 3px 4px;width:50%"><?=($arGroup['VALUE'] ? $arGroup['VALUE'] : Loc::getMessage('AOS_TPL_BUYER_COMMENT_NO'))?></td>
								</tr>
							<? endif ?>
							</tbody>
						</table>
					</div>
				<? endforeach; ?>
			</td>
		</tr>
		</tbody>
	</table>
<? endif ?>