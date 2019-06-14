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
			<th style="border: none;background-color:#E7F2F2; padding:10px; text-align:left;text-transform:uppercase"><?=Loc::getMessage('AOS_TPL_FINANCE_BLOCK')?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="border:0;padding:10px;background-color:#F7FAFA">
				<table width="100%" style="padding:15px; margin:0; border-width:1px; border-style:solid; border-color:#E3E8EA; background-color:#EEF5F5" cellpadding="0" cellspacing="0">
					<thead>
					<tr>
						<td style="color: #4c6267;padding: 4px;border:none;border-bottom: 1px solid #d1d7d9;"><?=Loc::getMessage('AOS_TPL_FINANCE_FOR_PAYMENT')?></td>
						<td style="width:25px;border:none"></td>
						<td style="color: #749511;padding: 4px;border:none;border-bottom: 1px solid #d1d7d9;"><?=Loc::getMessage('AOS_TPL_FINANCE_SUM_PAID')?></td>
						<td style="width:25px;border:none"></td>
						<td style="color: #f29129;padding: 4px;border:none;border-bottom: 1px solid #d1d7d9;"><?=Loc::getMessage('AOS_TPL_FINANCE_PAYABLE')?></td>
					</tr>
					</thead>
					<tbody>
					<tr style="color: #4c6267;font-weight:bold;font-size: 30px;">
						<td style="padding: 5px;border:none"><?=SaleFormatCurrency(floatval($arResult['PRICE']), $arResult['CURRENCY'])?></td>
						<td style="width:25px;border:none"></td>
						<td style="padding: 5px;border:none"><?=SaleFormatCurrency(floatval($arResult['SUM_PAID']), $arResult['CURRENCY'])?></td>
						<td style="width:25px;border:none"></td>
						<td style="padding: 5px;border:none"><?=SaleFormatCurrency(floatval($arResult['PAYABLE']), $arResult['CURRENCY'])?></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		</tbody>
	</table>
<? endif ?>