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
<? if($arResult['ITEMS']): ?>
	<table style="margin:0;padding:0;border-collapse:collapse;border-width:1px;border-style:solid;border-color:#E0EAF0;"
	       width="100%"
	       border="0"
	       cellpadding="0"
	       cellspacing="0">
		<thead>
		<tr>
			<th style="border: none;background-color:#E7F2F2; padding:10px; text-align:left;text-transform:uppercase"><?=Loc::getMessage('AOS_TPL_PAYMENT_BLOCK')?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="border:0;padding:10px;padding-top:0;background-color:#F7FAFA">
				<table width="100%" cellpadding="0" cellspacing="0" style="border-width:1px; border-style:solid; border-color:#E3E8EA;margin:10px 0 0 0; padding:0">
					<? foreach($arResult['ITEMS'] as $payment): ?>
						<?
						$psData = $payment['PAY_SYSTEM'];

						$shortLogoPath = $payment['SALE_URL'] . '/bitrix/images/sale/nopaysystem.gif';
						if($psData["LOGOTIP"])
						{
							$shortLogo     = \CFile::ResizeImageGet($psData["LOGOTIP"], array('width' => 80, 'height' => 60));
							$shortLogoPath = $payment['SALE_URL'] . $shortLogo['src'];
						}

						$paidString    = ($payment['PAID'] == 'Y') ? 'YES' : 'NO';
						$style         = ($payment['PAID'] == 'Y' ? 'color:#658d0f' : 'color:#cc2020');
						$paymentStatus = '<span style="' . $style . '">' . Loc::getMessage('AOS_TPL_PAYMENT_STATUS_' . $paidString) . '</span>';

						$tdStyle = 'border: none;padding:5px;';
						?>
						<tbody style="border-bottom:1px solid #c9d0d1;">
						<tr style="background-color:#E3E8EA; font-size: 13px;line-height: 25px;min-height: 25px;;vertical-align: middle;color: #646d7a;">
							<td style="border: none;padding:0 10px;text-align:left" colspan="4">
								<?=Loc::getMessage('AOS_TPL_PAYMENT_BLOCK_EDIT_PAYMENT_TITLE', array('#ID#' => $payment['ID'], '#DATE_BILL#' => $payment['DATE_BILL']))?>
							</td>
						</tr>
						<tr style="background-color:#F7FAFA">
							<td style="<?=$tdStyle?>" width="10%">
								<img src="<?=$shortLogoPath?>" style="border: 1px solid #acbbc0;max-width:80px" alt="<?=$payment['PAY_SYSTEM_NAME']?>">
							</td>
							<td style="<?=$tdStyle?>" width="30%"><?=htmlspecialcharsbx($payment['PAY_SYSTEM_NAME'])?></td>
							<td style="<?=$tdStyle?>" width="30%"><?=Loc::getMessage('AOS_TPL_PAYMENT_PAYABLE_SUM') . ':<br>' . SaleFormatCurrency($payment['SUM'], $payment['CURRENCY'])?></td>
							<td style="<?=$tdStyle?>" width="30%"><?=Loc::getMessage('AOS_TPL_PAYMENT_STATUS') . ':<br>' . $paymentStatus?>
								<? if($payment['DATE_PAID']): ?>
									<?=$payment['DATE_PAID'] . ' ' . htmlspecialcharsbx($payment['EMP_PAID_ID_NAME']) . ' ' . htmlspecialcharsbx($payment['EMP_PAID_ID_LAST_NAME']);?>
								<? endif ?>
							</td>
						</tr>
						</tbody>
					<? endforeach; ?>
				</table>
			</td>
		</tr>
		</tbody>
	</table>
<? endif ?>